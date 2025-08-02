# 5_1_Inserting Data via Fixtures

Bu bölümde Symfony projesinde Doctrine Fixtures ile veritabanına örnek veri eklemek için yapılan değişiklikler özetlenmiştir.

## 1. Doctrine Fixtures Kurulumu

Geliştirme ortamında kullanılmak üzere paket yüklendi:

```
composer require --dev orm-fixtures
```

## 2. AppFixtures Sınıfı Düzenlendi

`src/DataFixtures/AppFixtures.php` dosyasında aşağıdaki değişiklikler yapıldı:

-   Doğru entity ve enum import edildi:
    ```php
    use App\Entity\Starship;
    use App\Model\StarshipStatusEnum;
    ```
-   Üç adet Starship entity'si oluşturuldu ve özellikleri set edildi:

    ```php
    $ship1 = new Starship();
    $ship1->setName('USS LeafyCruiser (NCC-0001)');
    $ship1->setClass('Garden');
    $ship1->setCaptain('Jean-Luc Pickles');
    $ship1->setStatus(StarshipStatusEnum::IN_PROGRESS);
    $ship1->setArrivedAt(new \DateTimeImmutable('-1 day'));

    $ship2 = new Starship();
    $ship2->setName('USS Espresso (NCC-1234-C)');
    $ship2->setClass('Latte');
    $ship2->setCaptain('James T. Quick!');
    $ship2->setStatus(StarshipStatusEnum::COMPLETED);
    $ship2->setArrivedAt(new \DateTimeImmutable('-1 week'));

    $ship3 = new Starship();
    $ship3->setName('USS Wanderlust (NCC-2024-W)');
    $ship3->setClass('Delta Tourist');
    $ship3->setCaptain('Kathryn Journeyway');
    $ship3->setStatus(StarshipStatusEnum::WAITING);
    $ship3->setArrivedAt(new \DateTimeImmutable('-1 month'));
    ```

-   Persist ve flush işlemleri eklendi:
    ```php
    $manager->persist($ship1);
    $manager->persist($ship2);
    $manager->persist($ship3);
    $manager->flush();
    ```

## 3. Starship Entity Güncellendi

-   `setStatus()` metodu eklendi:
    ```php
    public function setStatus(?StarshipStatusEnum $status): static
    {
        $this->status = $status;
        return $this;
    }
    ```

## 4. Fixture'ları Yükleme

Fixture'lar şu komutla yüklendi:

```
symfony console doctrine:fixtures:load
```

Bu işlemler sonucunda veritabanına örnek Starship verileri başarıyla eklendi.

## 🌱 Inserting Data via Fixtures / Fixtures ile Veri Ekleme

Artık bir veritabanı tablomuz var, ama verimiz yok! Geliştirme ortamında çalışırken, veritabanınızı doldurmak için sahte verilere sahip olmak faydalıdır: uygulamayı geliştirirken oynayabileceğiniz örnek veriler. Bu verilere `fixtures` denir.

Bizim durumumuzda, tablomuzu birkaç `Starship` ile önceden doldurmak harika olurdu! Doctrine, bu sahte `fixtures` verilerini ekleyen bir paket sunuyor! Terminalde şunu çalıştırın:

```bash
composer require --dev orm-fixtures
```

👉 Bu komut, `fixtures` paketini sadece geliştirme ortamı için yükler.

`--dev` kullandık çünkü `fixtures` sadece geliştirme ortamında gerekli. Ne yüklendiğine bakmak için yukarı kaydırın: `doctrine/data-fixtures` ve `doctrine-fixtures-bundle` kuruldu. Sonra şunu çalıştırın:

```bash
git status
```

👉 Bu komut, yükleme sırasında nelerin değiştiğini gösterir.

Flex'e özgü şeyler: bir bundle eklendi ve ayrıca `src/DataFixtures` dizini oluşturuldu. Haydi bunu inceleyelim: `src/DataFixtures/AppFixtures.php` dosyasını açın. Bu dosyada `load()` metodu, `fixtures`'ları oluşturacağımız yerdir. Oradaki kodu silin ve sıfırdan başlayalım.

## Create Entities/Varlıklar Oluşturma

Uygulama nerede çalışırsa çalışsın, veritabanına varlık eklemek oldukça basit! İlk olarak nesneyi her zamanki gibi oluşturun: `$ship1 = new Starship()` — bu `App\Entity` içindeki sınıf.

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 9
class AppFixtures extends Fixture
// ... line 11
    public function load(ObjectManager $manager): void
    {
        $ship1 = new Starship();
// ... lines 15 - 35
    }
}
```

👉 Bu kod, yeni bir `Starship` nesnesi oluşturur.

Önceki bölümde, `src/Model/` dizininde bir `StarshipRepository` servisi oluşturmuştuk. Onu açın. `findAll()` metodu, bu `Starship` nesnelerini dinamik olarak oluşturuyor. Bu verileri `fixtures` için kullanacağız!

İlk `Starship` nesnesinin ikinci parametresini (isim) kopyalayın. `AppFixtures` içine geri dönerek şu satırları ekleyin:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 9
class AppFixtures extends Fixture
// ... line 11
    public function load(ObjectManager $manager): void
    {
// ... line 14
        $ship1->setName('USS LeafyCruiser (NCC-0001)');
        $ship1->setClass('Garden');
        $ship1->setCaptain('Jean-Luc Pickles');
        $ship1->setStatus(StarshipStatusEnum::IN_PROGRESS);
        $ship1->setArrivedAt(new \DateTimeImmutable('-1 day'));
// ... lines 20 - 35
    }
}
```

👉 Bu kod, `Starship` varlığının alanlarını doldurur.

Diğer iki gemi için, `tutorial/` dizininden bazı kodları kopyalayacağız:

```php

//src/DataFixtures/AppFixtures.php
// ... lines 1 - 9
class AppFixtures extends Fixture
// ... line 11
    public function load(ObjectManager $manager): void
    {
// ... lines 14 - 20
        $ship2 = new Starship();
        $ship2->setName('USS Espresso (NCC-1234-C)');
        $ship2->setClass('Latte');
        $ship2->setCaptain('James T. Quick!');
        $ship2->setStatus(StarshipStatusEnum::COMPLETED);
        $ship2->setArrivedAt(new \DateTimeImmutable('-1 week'));
        $ship3 = new Starship();
        $ship3->setName('USS Wanderlust (NCC-2024-W)');
        $ship3->setClass('Delta Tourist');
        $ship3->setCaptain('Kathryn Journeyway');
        $ship3->setStatus(StarshipStatusEnum::WAITING);
        $ship3->setArrivedAt(new \DateTimeImmutable('-1 month'));
// ... lines 34 - 35
    }
}
```

👉 Bu kod, iki ek `Starship` nesnesi daha tanımlar.

Artık üç gemi nesnemiz var, ama henüz hiçbir şey kaydedilmedi — yani veritabanına `persist` edilmedi. Ama ilginçtir ki, Doctrine bize bir `ObjectManager` geçiriyor. Bu, Doctrine'in kalbidir. Nesneleri kaydetmek, almak, güncellemek ve silmek için bunu kullanacağız.

### Varlıkları Kaydetme

Kullanımı oldukça basit: gemi nesnelerini oluşturduktan sonra şunları yazın:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 9
class AppFixtures extends Fixture
// ... line 11
    public function load(ObjectManager $manager): void
    {
// ... lines 14 - 34
        $manager->persist($ship1);
        $manager->persist($ship2);
        $manager->persist($ship3);
// ... lines 38 - 39
    }
}
```

👉 Bu kod, nesneleri veritabanına kaydetmek için kuyruğa alır.

`persist()` komutu aslında verileri hemen eklemez: sadece onları kayıt kuyruğuna ekler.

### `flush`

Gemi verilerini gerçekten eklemek (INSERT) için, şunu yazın:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 9
class AppFixtures extends Fixture
// ... line 11
    public function load(ObjectManager $manager): void
    {
// ... lines 14 - 38
        $manager->flush();
    }
}
```

👉 Bu komut, kuyruktaki tüm nesneleri veritabanına yazar.

`flush()` gerçekten harikadır: kaydedilmek üzere kuyrukta olan tüm nesnelere bakar ve veritabanına verimli bir SQL sorgusu ile yazar. Bu durumda, üç `Starship` varlığını tek seferde ekleyecek.

### `fixtures`'ları Yükleme

`fixtures` tamam! Bu kodu nasıl çalıştıracağız? Şöyle:

```bash
symfony console doctrine:fixtures:load
```

👉 Bu komut, `fixtures` verilerini veritabanına yükler. Mevcut verilerin silineceğini doğrulamak için onay ister.

Başarılı mı? Şimdi şu ham SQL sorgusunu tekrar çalıştırın:

```bash
symfony console doctrine:query:sql 'SELECT * FROM starship'
```

👉 Bu komut, `starship` tablosundaki tüm verileri sorgular.

Gemilerimiz var! Harika!

Artık veri içeren bir veritabanımız var! Sırada ne mi var? Uygulamanın denetleyicilerini, `Starship` verilerini veritabanından çekip sayfada gösterecek şekilde yeniden düzenleyeceğiz. Bu, sandığınızdan çok daha kolay olacak!

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./5_Inserting Data via Fixtures.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./5_1_Setting Relations in Foundry.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
