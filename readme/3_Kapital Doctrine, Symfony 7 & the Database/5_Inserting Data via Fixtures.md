# 🧪 Inserting Data via Fixtures / Fixture ile Veri Ekleme

Veritabanı tablomuz hazır, ancak şimdi biraz veriye ihtiyacımız var! Geliştirme ortamında çalışırken, veritabanınızı sahte verilerle doldurmak faydalı olur: uygulama geliştirirken oynayabileceğiniz veriler. Bu verilere `fixture` adı verilir.

Bizim durumumuzda, tabloyu birkaç `Starship` ile önceden doldurmak harika olurdu! Doctrine, bu sahte fixture verilerini ekleyen bir paket bile sunar! Terminalinizde şu komutu çalıştırın:

```bash
composer require --dev orm-fixtures
```

👉 Bu komut, sadece geliştirme ortamında kullanılacak `fixture` paketini yükler.

`--dev` kullandık çünkü fixture'lara yalnızca geliştirme ortamında ihtiyacımız var. Ne kurulduğuna bakmak için yukarı kaydırın: `doctrine/data-fixtures` ve `doctrine-fixtures-bundle`. Şunu çalıştırın:

```bash
git status
```

👉 Bu komut, Flex tarafından nelerin eklendiğini gösterir.

Standart Flex işlemleri: bir `bundle` eklendi ve ayrıca `src/DataFixtures` dizini oluşturuldu. Şimdi orayı inceleyelim: `src/DataFixtures/AppFixtures.php` dosyasını açın. Bu `load()` metodu, fixture'ları oluşturacağımız yerdir. İçeriği silerek sıfırdan başlayalım.

## 🛠️ Create Entities / Entity'leri Oluştur

Nerede olursanız olun, veritabanına entity eklemek oldukça basit! İlk olarak nesneyi normal şekilde oluşturun: `$ship1 = new Starship()` - bu `App\Entity` içindeki sınıf.

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

👉 Bu kod bloğunda ilk `Starship` nesnesi oluşturuluyor.

Önceki bölümde `src/Model/` dizininde `StarshipRepository` servisini oluşturmuştuk. Orayı açın. `findAll()` metodumuz bu `Starship` nesnelerini anlık olarak oluşturuyor. Bu verileri fixture olarak kullanacağız!

İlk `Starship` için ikinci argümanı (isim) kopyalayın. `AppFixtures` içinde şu çağrıyı yapın: 
    `$ship1->setName('USS LeafyCruiser (NCC-0001)')`. 
Aynısını 
    `class`: `$ship1->setClass('Garden')`, 
    `captain`: `$ship1->setCaptain('John Luke Pickles')`, 
    `status`: `$ship1->setStatus(StarshipStatusEnum::IN_PROGRESS)` 
için yapın ve `enum`'u import etmeyi unutmayın. Son olarak 
    `arrivedAt`: `$ship1->setArrivedAt(new \DateTimeImmutable('-1 day'))`.

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


👉 Bu kod bloğunda `Starship` nesnesi özellikleriyle dolduruluyor.

Diğer iki gemi için tutorial/ dizininden kodları kopyalayın ve yapıştırın.

```php 
// src/DataFixtures/AppFixtures.php

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


👉 Bu kod bloğunda iki yeni `Starship` nesnesi oluşturuluyor.

Artık elimizde üç gemi nesnesi var, ancak henüz hiçbiri veritabanına kaydedilmedi. Doctrine bize bir `ObjectManager` geçiriyor. Bu, Doctrine'in kalbidir. Onu veritabanından nesne kaydetmek, almak, güncellemek ve silmek için kullanacağız.

## 💾 Persist Entities / Entity'leri Kalıcılaştır

Kullanmamız için, gemi nesnelerini oluşturduktan sonra şu satırları yazın: `$manager->persist($ship1)`, `$manager->persist($ship2)` ve `$manager->persist($ship3)`. Ancak `persist()` bunları hemen eklemez: sadece kaydedilecekler kuyruğuna ekler.

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


👉 Bu kod bloğunda üç `Starship` nesnesi `persist()` edilerek kaydedilmek üzere kuyruklanıyor.

## 🚀 Flush / Kaydet (Flush)

Bu gemileri veritabanına gerçekten eklemek için şu satırı yazın: `$manager->flush()`.

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


👉 Bu komut, tüm `persist()` edilen nesneleri veritabanına tek sorguda yazar.

`flush()` oldukça etkileyici: kaydedilmek üzere kuyrukta bekleyen tüm nesnelere bakar ve onları veritabanına etkili bir SQL sorgusuyla yazar. Bu durumda üç `Starship` birden eklenir. Süper!

## 🔄 Load Fixtures / Fixture'ları Yükle

Fixture'lar tamam! Bu kodu nasıl çalıştırırız? Şu komutu çalıştırın:

```bash
symfony console doctrine:fixtures:load
```

👉 Bu komut, fixture'ları veritabanına yükler. Tüm mevcut verileri sileceği için onay ister.

Evet'i seçin ve… Başarılı mı?

Şu SQL sorgusunu tekrar çalıştırın:

```bash
symfony console doctrine:query:sql "SELECT * FROM starship"
```

👉 Bu komut, `starship` tablosundaki tüm verileri görüntüler.

Gemilerimiz geldi! Harika!

Şimdi elimizde verilerle dolu bir veritabanı var! Bir sonraki adımda, uygulamamızın denetleyicilerini yeniden düzenleyip gemileri veritabanından çekerek sayfada göstermeye başlayacağız. Bu, tahmin ettiğinizden çok daha kolay olacak!
