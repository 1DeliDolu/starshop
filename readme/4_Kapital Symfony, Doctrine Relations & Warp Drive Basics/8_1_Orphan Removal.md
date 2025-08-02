# 🧹 Orphan Removal / Yetim Kaldırma

`make:entity` kullanarak ilişki eklediğimizde bize `orphanRemoval` hakkında sordu. Şimdi bunun ne olduğunu ve ne zaman kullanılacağını öğrenme zamanı.

## 🧪 Test Senaryosu Kurma

Fixtures'ta `$starshipPart = StarshipPartFactory::createOne()` ile başlayın. Öne çıkarmak için, bunu herhangi bir uzay yolculuğu için kritik bir öğe yapacağım: "Toilet Paper." Evet, pandemi zamanlarına şakacı bir selam. Iğrenç!

```php
// src/DataFixtures/AppFixtures.php
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ... önceki gemiler ...
        $ship = StarshipFactory::createOne([
            'name' => 'USS Wanderlust (NCC-2024-W)',
            'class' => 'Delta Tourist',
            'captain' => 'Kathryn Journeyway',
            'status' => StarshipStatusEnum::WAITING,
            'arrivedAt' => new \DateTimeImmutable('-1 month'),
        ]);
        $starshipPart = \App\Factory\StarshipPartFactory::createOne([
            'name' => 'Toilet Paper',
            'starship' => $ship,
        ]);
        dump($starshipPart);

        // ... diğer fixtures kodları
    }
}
```

Bu parçayı yukarıdaki Starship'e atayın (eksik olan `$ship =` ekleyin) sonra `$starshipPart`'ı dump edin.

Şu ana kadar, şu ana kadar iyi: hiçbir şey süslü değil. Fixtures'ları yeniden yüklemeyi deneyin:

```bash
symfony console doctrine:fixtures:load
```

Hata yok ve ilk kez bahsettiğim proxy nesnesini görüyoruz.

## 🎭 Proxy Nesnesini Açığa Çıkarma

Hatırlayın: Foundry ile bir nesne oluşturduğunuzda, size parlak yeni nesnenizi geri verir, ancak proxy adı verilen başka bir nesnenin içinde paketlenmiştir. Çoğu zaman: fark etmez veya umursamazsınız: proxy'deki tüm metot çağrıları gerçek nesneye iletilir.

Ama işleri kristal berraklığında yapmak istediğim için, `_real()` kullanarak hem `$ship` hem de `$starshipPart`'tan gerçek nesneyi çıkarın:

```php
// src/DataFixtures/AppFixtures.php
public function load(ObjectManager $manager): void
{
    // ... önceki gemiler ...
    $ship = StarshipFactory::createOne([
        'name' => 'USS Wanderlust (NCC-2024-W)',
        'class' => 'Delta Tourist',
        'captain' => 'Kathryn Journeyway',
        'status' => StarshipStatusEnum::WAITING,
        'arrivedAt' => new \DateTimeImmutable('-1 month'),
    ])->_real();
    $starshipPart = \App\Factory\StarshipPartFactory::createOne([
        'name' => 'Toilet Paper',
        'starship' => $ship,
    ])->_real();
    dump($starshipPart);

    // ... diğer fixtures kodları
}
```

Fixtures'ları tekrar çalıştırın:

```bash
symfony console doctrine:fixtures:load
```

Ve... her şey pürüzsüz. Proxy olmadan, StarshipPart'ın gerçekten doğru Starship'e bağlı olduğunu görebiliriz - daha önce oluşturduğumuz USS Wanderlust. Şu ana kadar, tüm sistemler devam ediyor!

## 🗑️ Starship Part Silme: Olay Kalınlaşıyor

Peki ya bir `StarshipPart`'ı silmemiz gerekirse? Normalde `$manager->remove($starshipPart)`, sonra `$manager->flush()` deriz. Ama işleri karıştıralım: parçayı sadece gemisinden kaldıralım: `$ship->removePart($starshipPart)`:

```php
// src/DataFixtures/AppFixtures.php
public function load(ObjectManager $manager): void
{
    // ... önceki kodlar ...
    $starshipPart = \App\Factory\StarshipPartFactory::createOne([
        'name' => 'Toilet Paper',
        'starship' => $ship,
    ])->_real();
    $ship->removePart($starshipPart);
    $manager->flush();
    dump($starshipPart);

    // ... diğer fixtures kodları
}
```

Ne olacağını düşünüyorsunuz? Parçayı silecek mi? Yoksa sadece gemiden mi kaldıracak? Bu durumda, parça uzayda dolaşıyor olacak, yetim kalacak. Deneyin:

```bash
symfony console doctrine:fixtures:load
```

Favori hatamızla patlar:

> starship_id cannot be null.

## 🔧 Null Hatasını Düzeltme

Bu neden oldu? `removePart()` çağırdığımızda, Starship'i null'a ayarlar. Ama bunu `nullable: false` ile izin vermez yaptık: her parça bir gemiye ait olmalı.

Çözüm? Bu, istediğinize bağlıdır: parçaların yetim kalmasına izin vermek mi istiyoruz? Harika! `StarshipPart`'ta nullable'ı true olarak değiştirin ve bir migration yapın.

Ya da belki bir parça aniden gemisinden kaldırılırsa, o parçayı veritabanından tamamen silmek istiyoruz. Belki gemi sahibi geri dönüşümün büyük bir hayranı değildir. Bunu yapmak için Starship'e gidin ve OneToMany'ye `orphanRemoval: true` ekleyin:

```php
// src/Entity/Starship.php
class Starship
{
    // ...
    /**
     * @var Collection<int, StarshipPart>
     */
    #[ORM\OneToMany(targetEntity: StarshipPart::class, mappedBy: 'starship', orphanRemoval: true)]
    private Collection $parts;
    // ...
}
```

Geri dönün ve fixtures'ları yeniden yükleyin:

```bash
symfony console doctrine:fixtures:load
```

Görünürde hata yok! ID null çünkü veritabanından tamamen silindi. Yani `orphanRemoval` şu anlama gelir:

> Hey, bu parçalardan herhangi biri yetim kalırsa onları yakma fırınına atın.

## 🎯 Orphan Removal Ne Zaman Kullanılır?

### ✅ **Kullanılması Gereken Durumlar:**

-   **Kompozisyon ilişkileri**: Parça sadece o gemi için anlam taşıyorsa
-   **Bağımlı nesneler**: Ana nesne olmadan var olması mantıklı değilse
-   **Otomatik temizlik**: Manuel silme işlemlerini önlemek için

### ❌ **Kullanılmaması Gereken Durumlar:**

-   **Bağımsız nesneler**: Parça başka gemilere de bağlanabiliyorsa
-   **Paylaşılan kaynaklar**: Birden fazla yerden referans edilebiliyorsa
-   **Geçici ayrılmalar**: Geçici olarak bağlantı kesilmesi normalse

## 🧹 Temizlik

Test kodumuz temizleyip fixtures'ları son kez yükleyelim:

```php
// src/DataFixtures/AppFixtures.php
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        StarshipFactory::createOne([
            'name' => 'USS LeafyCruiser (NCC-0001)',
            'class' => 'Garden',
            'captain' => 'Jean-Luc Pickles',
            'status' => StarshipStatusEnum::IN_PROGRESS,
            'arrivedAt' => new \DateTimeImmutable('-1 day'),
        ]);
        StarshipFactory::createOne([
            'name' => 'USS Espresso (NCC-1234-C)',
            'class' => 'Latte',
            'captain' => 'James T. Quick!',
            'status' => StarshipStatusEnum::COMPLETED,
            'arrivedAt' => new \DateTimeImmutable('-1 week'),
        ]);
        StarshipFactory::createOne([
            'name' => 'USS Wanderlust (NCC-2024-W)',
            'class' => 'Delta Tourist',
            'captain' => 'Kathryn Journeyway',
            'status' => StarshipStatusEnum::WAITING,
            'arrivedAt' => new \DateTimeImmutable('-1 month'),
        ]);
        StarshipFactory::createMany(20);
        \App\Factory\StarshipPartFactory::createMany(100);
    }
}
```

```bash
symfony console doctrine:fixtures:load
```

Tamam, sırada: İlişkinin sırasını kontrol etmenin bir yolu, `$ship->getParts()`'ın alfabetik olarak döndürmesi gibi.

## 🎯 Öğrenilen Dersler

1. **Orphan Removal**: Yetim kalan nesneleri otomatik olarak siler
2. **Proxy Objects**: Foundry proxy nesneleri oluşturur, `_real()` ile gerçek nesneyi alırız
3. **Null Constraints**: `nullable: false` yetim kalmasını engeller
4. **Kompozisyon vs Agregasyon**: orphanRemoval kompozisyon ilişkileri için uygundur
5. **Otomatik Temizlik**: Manuel silme işlemlerini azaltır

### 🔍 **Önemli Detay:**

`orphanRemoval: true` sadece `removePart()` ile parça kaldırıldığında çalışır. Gemi tamamen silinirse parçalar normal foreign key constraint'ler tarafından etkilenir.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./6_1_The Two Sides of a Relation Owning vs Inverse.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./9_Pagination.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
