# 🗑️ Orphan Removal / Orphan Removal (Yetim Silme)

`make:entity` ile bir ilişki eklediğimizde, bize `orphanRemoval` hakkında soru sormuştu. Şimdi bunun ne olduğunu ve ne zaman kullanılacağını öğrenme zamanı.

Fixture'larda `$starshipPart = StarshipPartFactory::createOne()` ile başlayın. Dikkat çekici olması için bunu her uzay yolculuğu için hayati bir öğe yapalım: "Toilet Paper". Evet, pandemiye gönderme! 🙂

Bu parçayı yukarıdaki Starship'e atayın (eksik olan `$ship =` satırını ekleyin) ve ardından `$starshipPart`'ı dökün:


```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 10
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 15 - 30
        $ship = StarshipFactory::createOne([
// ... lines 32 - 36
        ]);
        $starshipPart = StarshipPartFactory::createOne([
            'name' => 'Toilet Paper',
            'starship' => $ship,
        ]);
        dump($starshipPart);
// ... lines 44 - 46
    }
}
```

👉 Bu kod, bir "Toilet Paper" parçası oluşturur ve bir gemiye atar.

Şimdiye kadar her şey yolunda: herhangi bir hata yok. Fixture'ları tekrar yükleyin:

```shell
symfony console doctrine:fixtures:load
```

👉 Fixture'lar başarıyla yüklenir ve ilk defa bahsedilen proxy nesnesini görürsünüz.

## 🕵️‍♂️ Unveiling the Proxy Object / Proxy Nesnesini Açığa Çıkarmak

Unutmayın: Foundry ile bir nesne oluşturduğunuzda, size dönen nesne aslında bir proxy nesnesidir. Çoğu zaman bunu fark etmezsiniz veya umursamazsınız: tüm metot çağrıları proxy üzerinden gerçek nesneye iletilir.

Ama burada her şeyin net olması için, hem `$ship` hem de `$starshipPart`'ı `_real()` ile çıkaralım:


```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 10
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 15 - 30
        $ship = StarshipFactory::createOne([
// ... lines 32 - 36
        ])->_real();
        $starshipPart = StarshipPartFactory::createOne([
            'name' => 'Toilet Paper',
            'starship' => $ship,
        ])->_real();
        dump($starshipPart);
// ... lines 44 - 46
    }
}
```

👉 Bu kod, proxy yerine gerçek nesneleri elde eder.

Fixture'ları tekrar çalıştırın:

```shell
symfony console doctrine:fixtures:load
```

👉 Artık proxy olmadan, `StarshipPart` gerçekten doğru `Starship` ile ilişkili görünüyor – mesela USS Espresso ile. Her şey yolunda!

## 🗑️ Deleting a Starship Part: The Plot Thickens / Bir StarshipPart'ı Silmek

Peki bir `StarshipPart` silmek istersek ne olur? Normalde şöyle yaparız: `$manager->remove($starshipPart)`, sonra `$manager->flush()`. Ama biraz farklı bir yol deneyelim: parçayı gemisinden sadece çıkaralım: `$ship->removePart($starshipPart);`


```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 10
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 15 - 38
        $starshipPart = StarshipPartFactory::createOne([
            'name' => 'Toilet Paper',
            'starship' => $ship,
        ])->_real();
        $ship->removePart($starshipPart);
        $manager->flush();
        dump($starshipPart);
// ... lines 46 - 48
    }
}
```

👉 Bu kod, parçayı gemiden çıkarır ve değişiklikleri kaydeder.

Ne olacağını düşünüyorsunuz? Parça silinecek mi? Yoksa sadece gemiden çıkarılıp "yetim" (orphan) mi olacak? Yani gemisiz kalacak. Deneyin:

```shell
symfony console doctrine:fixtures:load
```

👉 Favori hatamız patlıyor:
`starship_id cannot be null.`

## 🛠️ Fixing the Null Error / Null Hatasını Düzeltmek

Neden bu oldu? `removePart()` çağrıldığında, parçanın gemisi null olarak ayarlanıyor. Ama biz nullable: false yaptık: Her parça bir gemiye ait olmalı. Çözüm? Aslında ne istediğinize bağlı: Parçaların yetim kalmasına izin vermek ister misiniz? Tamam! `StarshipPart`'ta nullable'ı true yapın ve bir migration oluşturun.

Yoksa bir parça gemisinden çıkarılırsa, tamamen veritabanından silinsin mi istiyorsunuz? Belki gemi sahibi geri dönüşüm sevmiyor! Bunu yapmak için, `Starship`'e gidin ve OneToMany ilişkiye `orphanRemoval: true` ekleyin:


```php
// src/Entity/Starship.php
// ... lines 1 - 13
class Starship
{
// ... lines 16 - 41
    /**
     * @var Collection<int, StarshipPart>
     */
    #[ORM\OneToMany(targetEntity: StarshipPart::class, mappedBy: 'starship', orphanRemoval: true)]
    private Collection $parts;
// ... lines 47 - 180
}
```

👉 Bu kod, ilişkiye `orphanRemoval: true` ekler.

Şimdi geri dönüp fixture'ları tekrar yükleyin:

```shell
symfony console doctrine:fixtures:load
```

👉 Artık hata yok! ID null değil çünkü o kayıt tamamen veritabanından silindi. Yani `orphanRemoval` şunu demek:

Herhangi bir parça yetim kalırsa, onu tamamen sil.

Sırada: Bir ilişkinin sırasını kontrol etmenin bir yolu – örneğin, `$ship->getParts()`'ın alfabetik olarak dönmesini sağlamak.
