# 🧩 Setting Relations in Foundry / Foundry'de İlişkileri Ayarlama

Tamam, elimizde birkaç parça ve birkaç yıldız gemisi var, ancak test verisi filomuzu doldurmak için çok daha fazlasını istiyorum. Bu iş, bizim iyi dostumuz olan `Foundry` için mükemmel bir görev. Manuel kodu kaldırın, ardından herhangi bir yere örneğin: `StarshipPartFactory::createMany(100)` yazın:


```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 8
use App\Factory\StarshipPartFactory;
// ... lines 10 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 41
        StarshipPartFactory::createMany(100);
    }
}
```

👉 Bu kod, 100 tane yıldız gemisi parçası oluşturur.

Ve fixture'ları deneyin:

```bash
symfony console doctrine:fixtures:load
```

👉 Bu komut, veritabanını fixture verileriyle doldurur.

Uh-oh!

`starship_id` `starship_part` içinde null olamaz.

Bu, `StarshipPartFactory`'ye kadar izleniyor, `defaults()` metodunda. Bu metot, her yeni `StarshipPart` oluşturulduğunda ona gönderilen veridir. Altın kural, `defaults()` metodunun, nesnedeki her zorunlu özellik için bir anahtar döndürmesidir. Şu anda açıkça `starship` özelliğini atlıyoruz, o yüzden bunu ekleyelim. `starship`'i, `starship_id` değil, ve dizi aktararak `Starship::randomOrCreate()` adlı hoş bir metoda ayarlayın:


```php
// src/Factory/StarshipPartFactory.php
// ... lines 1 - 11
final class StarshipPartFactory extends PersistentProxyObjectFactory
{
// ... lines 14 - 45
    protected function defaults(): array|callable
    {
// ... lines 48 - 50
        return [
// ... lines 52 - 54
            'starship' => StarshipFactory::randomOrCreate([
// ... line 56
            ]),
        ];
    }
// ... lines 60 - 69
}
```

👉 Bu kod, her parça için rastgele bir yıldız gemisi oluşturur veya mevcut olanlardan birini kullanır.

## 🎬 Setting the Stage for Starship Parts / Yıldız Gemisi Parçaları için Zemin Hazırlama

Anasayfada sadece `in progress` veya `waiting` durumundaki yıldız gemilerini listeliyoruz. Bu parçaların, `in progress` durumuna sahip bir gemiyle ilişkili olduğundan emin olmak için, dizideki `status` anahtarını `StarshipStatusEnum::IN_PROGRESS` olarak ayarlayın:


```php
// src/Factory/StarshipPartFactory.php
// ... lines 1 - 5
use App\Entity\StarshipStatusEnum;
// ... lines 7 - 11
final class StarshipPartFactory extends PersistentProxyObjectFactory
{
// ... lines 14 - 45
    protected function defaults(): array|callable
    {
// ... lines 48 - 50
        return [
// ... lines 52 - 54
            'starship' => StarshipFactory::randomOrCreate([
                'status' => StarshipStatusEnum::IN_PROGRESS,
            ]),
        ];
    }
// ... lines 60 - 69
}
```

👉 Bu kod, her parçayı `in progress` durumundaki bir gemiyle ilişkilendirir.

Bu `randomOrCreate()` etkileyici bir metottur: Önce veritabanında bu kriterlere uyan (`in progress` bir gemi) bir `Starship` arar. Bulursa onu kullanır. Bulamazsa, bu durumla yeni bir tane oluşturur.

Şimdi fixture'ları tekrar deneyin.

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komutla fixture'lar tekrar yüklenir.

Hata yok! Veritabanını kontrol edin:

```shell
symfony console doctrine:query:sql "SELECT * FROM starship_part"
```

👉 Bu komut, tüm yıldız gemisi parçalarını listeler.

Dikkatlice bakın... Tamam! Her biri rastgele bir `Starship`e bağlı 100 parçamız var ve bu gemi `in progress` durumunda olmalı. Bu, muhtemelen şimdiye kadarki en verimli 5 dakikamdı!

## 🎛️ Taking Control in Foundry / Foundry'de Kontrolü Ele Almak

Ama ya daha fazla kontrole ihtiyacımız olursa? Ya bu 100 parçanın hepsini aynı gemiye atamak istersek? Çok kullanışlı gelmese de, Foundry ve ilişkileri daha iyi anlamamıza yardımcı olacak.

Öncelikle bir gemi değişkeni alın: `$ship = StarshipFactory::createOne()`:

src/DataFixtures/AppFixtures.php

```php
// ... lines 1 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 32
        $ship = StarshipFactory::createOne([
// ... lines 34 - 38
        ]);
// ... lines 40 - 44
    }
}
```

👉 Bu kod, bir yıldız gemisi oluşturur ve değişkende saklar.

Ardından, `StarshipPartFactory::createMany()` içinde, ikinci argümanı belirterek tüm parçaların bu belirli gemiye atanmasını sağlayın:


```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 32
        $ship = StarshipFactory::createOne([
// ... lines 34 - 38
        ]);
// ... lines 40 - 41
        StarshipPartFactory::createMany(100, [
            'starship' => $ship,
        ]);
    }
}
```

👉 Bu kod, tüm parçaların aynı gemiye atanmasını sağlar.

Fixture'ları tekrar yükleyin.

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, tüm parçaları aynı gemiye atar.

Ve bitti! Artık tüm parçalar aynı tek gemiyle ilişkili. Eğer `Starship` sorgularsanız, 23 tane olduğunu görürsünüz: en alttaki 20, artı eklediğimiz 3 tane. Her şey yolunda gidiyor!

## 🌀 The Foundry Plot Twist / Foundry'de Beklenmedik Bir Durum

İşler burada ilginçleşiyor. `StarshipPartFactory` içinde, `randomOrCreate()` yerine `createOne()` kullanın:

src/Factory/StarshipPartFactory.php

```php
// ... lines 1 - 11
final class StarshipPartFactory extends PersistentProxyObjectFactory
{
// ... lines 14 - 45
    protected function defaults(): array|callable
    {
// ... lines 48 - 50
        return [
// ... lines 52 - 54
            'starship' => StarshipFactory::createOne([
// ... line 56
            ]),
        ];
    }
// ... lines 60 - 69
}
```

👉 Bu kod, her parça için yeni bir yıldız gemisi oluşturur.

Fixture'ları tekrar yükleyin.

```shell
symfony console doctrine:fixtures:load
```

Ve... tüm gemileri sorgulayın.

```shell
symfony console doctrine:query:sql "SELECT * FROM starship"
```

Bir anda bir filo oluştu! Tam olarak 123 gemi. Ne oldu?

Her bir parça için `defaults()` metodu çağrılıyor. Yani 100 parçanın her biri için bu satır çalışıyor ve bir `Starship` oluşturup kaydediyor, hatta anında üzerine yazsak bile.

Çözüm? Bunu `StarshipFactory::new()` olarak değiştirin:


```php
// src/Factory/StarshipPartFactory.php
// ... lines 1 - 11
final class StarshipPartFactory extends PersistentProxyObjectFactory
{
// ... lines 14 - 45
    protected function defaults(): array|callable
    {
// ... lines 48 - 50
        return [
// ... lines 52 - 54
            'starship' => StarshipFactory::new([
// ... line 56
            ]),
        ];
    }
// ... lines 60 - 69
}
```

👉 Bu kod, veritabanında kaydedilmemiş yeni bir fabrika örneği oluşturur.

Bu gizli sos: veritabanında bir nesne değil, yeni bir fabrika örneği oluşturur. Deneyin:

```shell
symfony console doctrine:fixtures:load
```

Gemileri sorgulayın.

```shell
symfony console doctrine:query:sql "SELECT * FROM starship"
```

Mükemmel! Tekrar 23 tane gemimiz var.

## 🍰 Factories are Object Recipes / Factory'ler Nesne Tarifleridir

İlginç bir bilgi! Bu fabrika örneklerini, nesneleri oluşturmak için tarif gibi kullanabiliriz. `StarshipFactory::new(['status' => StarshipStatusEnum::STATUS_IN_PROGRESS])` veritabanında bir nesne oluşturmaz. Hayır: `new()` bir fabrika örneği oluşturur. Bir özelliğe fabrika verdiğinizde, Foundry o nesneyi ancak gerekirse oluşturur. Yani `Starship` üzerine yazılmazsa, yeni bir `Starship` oluşturulur ve kaydedilir. İlişkiler ayarlanırken Foundry'de en iyi uygulama budur: Özelliğe bir fabrika örneği atayın.

Fixture'larımızı, override'ı kaldırarak temizleyin:


```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 41
        StarshipPartFactory::createMany(100);
    }
}
```

👉 Artık tüm parçalar yine uygun şekilde oluşturulacak.

Ve... tekrar `randomOrCreate()`e dönün:


```php
// src/Factory/StarshipPartFactory.php
// ... lines 1 - 11
final class StarshipPartFactory extends PersistentProxyObjectFactory
{
// ... lines 14 - 45
    protected function defaults(): array|callable
    {
// ... lines 48 - 54
            'starship' => StarshipFactory::randomOrCreate([
// ... line 56
            ]),
        ];
    }
// ... lines 60 - 69
}
```

👉 Bu kod, tekrar rastgele veya mevcut uygun bir yıldız gemisi kullanır.

Çünkü dürüst olalım, bu oldukça kullanışlı bir metot.

Son bir kez fixture'ları tekrar yükleyin ve bir şeylerin bozulmadığından emin olun:

```shell
symfony console doctrine:fixtures:load
```

👉 Fixture'lar sorunsuzca yüklenir.
