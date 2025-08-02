# 🤖 Many To Many with Foundry / Foundry ile Çoktan Çoğa İlişki

`AppFixtures` içinde daha önce bir `Droid`'i bir `Starship`'e elle atamıştık. Ama şimdi, bir droid ordusu ve bir yıldız gemisi filosu oluşturmak ve hepsini aynı anda atamak istiyorum.

`AppFixtures` içindeki o manuel `Droid` ve `Starship` atamalarını kaldır.

## Droid Ordusunu ve Starship Filosunu Oluşturmak

Aşağıda, yıldız gemilerini ve parçalarını oluşturduğumuz yere gel. Artık bir sürü droid'e de ihtiyacımız var: `DroidFactory::createMany(100)`.

Aşağıda, `droids`'u `DroidFactory::randomRange(1, 5)` olarak ayarla:


```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 47
        DroidFactory::createMany(100);
        StarshipFactory::createMany(100, [
            'droids' => DroidFactory::randomRange(1, 5),
        ]);
// ... line 53
    }
}
```

👉 Burada 100 droid ve 100 yıldız gemisi oluşturuluyor. Her `Starship` için 1 ile 5 arasında rastgele droid atanıyor.

Bu, her `Starship`'e 1 ile 5 arasında rastgele droid atayacak.

## The Magic of Symfony / Symfony'nin Büyüsü

Belki bir şeyi fark ettin: burada bir `droids` özelliği ayarlıyoruz, ama `Starship` içinde bir `setDroids()` metodu yok! Normalde bu, bir hata fırlatırdı. Ama çalışacak! Foundry, bir `addDroid()` metodu olduğunu görüyor ve bunun yerine her bir `Droid` için bunu tek tek çağırıyor.

## Test Run / Test Çalıştırma

Bunu çalışırken görmek için terminalini aç ve şu komutu çalıştır:

```bash
symfony console doctrine:fixtures:load
```

👉 Bu komut, veri yükleyicileri (fixtures) çalıştırır ve veritabanını doldurur.

Hata yok mu? Biraz şaşırdım, yani, memnun oldum. Droid'lere göz atmak için:

```bash
symfony console doctrine:query:sql 'SELECT * FROM droid'
```

👉 Bu komut, `droid` tablosundaki tüm kayıtları listeler.

100 tane eğlenceli, sevimli droid. Ayrıca `starship_droid` tablosunu da incele:

```bash
symfony console doctrine:query:sql 'SELECT * FROM starship_droid'
```

👉 Bu komut, `starship_droid` eşleştirme tablosundaki verileri gösterir.

Her yıldız gemisine rastgele droidler atanmış gibi görünmeli.

## Hold Up, Something's Not Right! / Bir Sorun Var!

Ama dur bir dakika. Bu "rastgele" droidler – ironik tırnak işaretlerimi hissettin mi? – aslında hiç de rastgele değil! Hep aynı 3 droid tekrar tekrar atanıyor. Sorun şu ki, `randomRange(1, 5)` sadece bir kez çağrılıyor: yani aynı 1 ila 5 rastgele droid her `Starship`'e atanıyor. Arzuladığımız çeşitlilik bu değil.

## Closures & Foundry / Closure'lar ve Foundry

Bunu, bir closure (anonim fonksiyon) geçirerek düzelt:


```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 50
        DroidFactory::createMany(100);
        StarshipFactory::createMany(100, fn() => [
            'droids' => DroidFactory::randomRange(1, 5),
        ]);
// ... line 56
    }
}
```

👉 Burada, her `Starship` için anonim fonksiyon ile gerçekten rastgele droid atanıyor.

Foundry, bu callback'i 100 yıldız gemisinin her biri için çalıştıracak. Yani `randomRange(1, 5)` her seferinde çağrılacak ve her gemi için gerçekten rastgele bir droid dizisi oluşturulacak.


Fixtures'ı tekrar çalıştır ve SQL sorgusunu yükle:

```bash
symfony console doctrine:fixtures:load
```

👉 Bu komut, veri yükleyicileri tekrar çalıştırır ve veritabanını günceller.

```bash
symfony console doctrine:query:sql 'SELECT * FROM starship_droid'
```

👉 Bu komut, droidlerin yıldız gemilerine dağılımını gösterir.

Artık yıldız gemilerine gerçekten rastgele droidler atanmış olacak.

Bunu ayrıca, `droids` anahtarını aşağıda, `StarshipFactory`'deki `defaults()` metoduna taşıyarak da çözebilirdik. Ama ben `defaults()`'u sadece gerekli özellikler için tutmayı seviyorum. Ve droidler teknik olarak zorunlu olmadığından – onlarsız tuvaleti temizlemek kolay değil! – onları `defaults()` dışında tutmayı ve `StarshipFactory`'yi kullandığımız yerde ayarlamayı tercih ediyorum.

Sonraki adımda, Çoktan Çoğa ilişkiler arasında nasıl JOIN yapılacağını öğreneceğiz. Yine, bu işin çoğunu Doctrine hallediyor.
