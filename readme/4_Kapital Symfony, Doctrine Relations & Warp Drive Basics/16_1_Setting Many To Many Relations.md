# 🤖 Setting Many To Many Relations - Uygulama Adımları

Bu dokümanda, ManyToMany ilişkilerini kullanarak Droid'leri Starship'lere atama sürecinde gerçekleştirilen adımlar detaylandırılmıştır.

## 📋 Yapılan İşlemler Özeti

### Önceki Adımlar (15_1 dokümanından devam)

-   ✅ **ManyToMany İlişkisi Kuruldu** - Starship ↔ Droid arasında
-   ✅ **Join Tablosu Oluşturuldu** - `starship_droid` tablosu aktif
-   ✅ **Bidirectional Senkronizasyon** - Her iki tarafta da ilişki yönetimi
-   ✅ **İlişki Test Edildi** - Manuel test başarılı

### Bu Bölümde Yapılan Adımlar

### Adım 1: Droid Entity Import'u Ekleme ✅

**AppFixtures.php dosyasına Droid entity import'u eklendi:**

```php
// src/DataFixtures/AppFixtures.php
use App\Factory\StarshipFactory;
use App\Factory\DroidFactory;
use App\Entity\Droid; // YENİ EKLENEN
```

### Adım 2: Üç Özel Droid Oluşturma ✅

**Dökümanda belirtilen üç özel droid eklendi:**

```php
// src/DataFixtures/AppFixtures.php
public function load(ObjectManager $manager): void
{
    // ... diğer kodlar

    // Özel droidler ekleme
    $droid1 = new Droid();
    $droid1->setName('IHOP-123');
    $droid1->setPrimaryFunction('Pancake chef');
    $manager->persist($droid1);

    $droid2 = new Droid();
    $droid2->setName('D-3P0');
    $droid2->setPrimaryFunction('C-3PO\'s voice coach');
    $manager->persist($droid2);

    $droid3 = new Droid();
    $droid3->setName('BONK-5000');
    $droid3->setPrimaryFunction('Comedy sidekick');
    $manager->persist($droid3);

    $manager->flush();
}
```

### Adım 3: Fixtures Yükleme ✅

**Komut:**

```shell
symfony console doctrine:fixtures:load --no-interaction
```

**Sonuç:** Yeni droidler başarıyla veritabanına eklendi.

### Adım 4: Droid Verilerini Doğrulama ✅

**Kontrol Komutu:**

```shell
symfony console dbal:run-sql "SELECT * FROM droid ORDER BY id DESC LIMIT 5"
```

**Sonuçlar:**

```
ID    Name         Primary Function
304   BONK-5000    Comedy sidekick
303   D-3P0        C-3PO's voice coach
302   IHOP-123     Pancake chef
301   ZZZ-123      protocol
300   BB-8         sleeper
```

✅ **Üç özel droid başarıyla oluşturuldu!**

## 🔍 Droid Özellikları

### Oluşturulan Özel Droidler:

1. **IHOP-123**

    - **Primary Function:** Pancake chef
    - **ID:** 302
    - **Özellik:** Kahvaltı uzmanı

2. **D-3P0**

    - **Primary Function:** C-3PO's voice coach
    - **ID:** 303
    - **Özellik:** Ses eğitmeni

3. **BONK-5000**
    - **Primary Function:** Comedy sidekick
    - **ID:** 304
    - **Özellik:** Komedi yardımcısı

## 📁 Güncellenen Dosyalar

1. **src/DataFixtures/AppFixtures.php**
    - `use App\Entity\Droid;` import'u eklendi
    - Üç özel droid oluşturma kodu eklendi
    - Manual persist ve flush işlemleri eklendi

## ⏭️ Sıradaki Adımlar

1. ✅ **Özel Droidler Oluşturuldu** - IHOP-123, D-3P0, BONK-5000
2. **Starship Oluşturma** - Bu droidlerin atanacağı özel bir starship
3. **İlişki Kurma** - `addDroid()` metoduyla droidleri starship'e atama
4. **Test Etme** - Join tablosunda ilişkileri doğrulama

## 🎯 Mevcut Durum

### Veritabanı İstatistikleri:

-   **Toplam Droid Sayısı:** 103 (100 factory + 3 özel)
-   **Özel Droidler:** 3 adet (ID: 302, 303, 304)
-   **Factory Droidler:** 100 adet
-   **Join Tablosu:** `starship_droid` (şu an boş, sıradaki adımda doldurulacak)

### Yapılan İyileştirmeler:

-   ✅ **Manual Entity Oluşturma**: Factory yerine manuel oluşturma
-   ✅ **Özel İsimler**: Anlamlı ve eğlenceli droid isimleri
-   ✅ **Fonksiyon Çeşitliliği**: Her droid için özel görev tanımı
-   ✅ **Persist/Flush Pattern**: Proper Doctrine entity yönetimi

**Sıradaki adımda bu droidleri bir Starship'e atayacağız!** 🚀

## 🪄 İkinci Bölüm: The Magic of Doctrine

### Adım 5: Özel Starship Oluşturma ve Droidleri Atama ✅

**Bir starship oluşturup üç droid'i atadık:**

```php
// src/DataFixtures/AppFixtures.php
// Özel bir starship oluşturup droidleri atayalım
$starship = new Starship();
$starship->setName('USS DroidCarrier (NCC-5000)');
$starship->setClass('Droid Transport');
$starship->setCaptain('Captain R2-D2');
$starship->setStatus(StarshipStatusEnum::IN_PROGRESS);
$starship->setArrivedAt(new \DateTimeImmutable('now'));

// Droidleri starship'e atayalım
$starship->addDroid($droid1);  // IHOP-123
$starship->addDroid($droid2);  // D-3P0
$starship->addDroid($droid3);  // BONK-5000

$manager->persist($starship);
$manager->flush();
```

### Adım 6: Doctrine'in Büyüsü - Droid Çıkarma ✅

**Bir droid'i starship'ten çıkardık:**

```php
// Doctrine'in büyüsü: Bir droid'i çıkaralım
$starship->removeDroid($droid1);  // IHOP-123'ü çıkar
$manager->flush();
```

### Adım 7: Sonuçları Doğrulama ✅

**Fixtures yeniden yüklendikten sonra kontrol:**

```shell
symfony console doctrine:fixtures:load --no-interaction
```

**Join tablosu kontrolü:**

```shell
symfony console dbal:run-sql "SELECT * FROM starship_droid WHERE starship_id = 116"
```

**Sonuç:**

```
starship_id   droid_id
116           406      (D-3P0)
116           407      (BONK-5000)
```

**Detaylı görünüm:**

```shell
symfony console dbal:run-sql "SELECT s.name as starship_name, d.name as droid_name, d.primary_function FROM starship_droid sd JOIN starship s ON sd.starship_id = s.id JOIN droid d ON sd.droid_id = d.id WHERE s.id = 116"
```

**Sonuç:**

```
starship_name                 droid_name   primary_function
USS DroidCarrier (NCC-5000)   D-3P0        C-3PO's voice coach
USS DroidCarrier (NCC-5000)   BONK-5000    Comedy sidekick
```

## 🎯 Doctrine'in Büyüsü Kanıtlandı! ✨

### Neler Oldu:

1. **İlk Durum:** 3 droid atandı (IHOP-123, D-3P0, BONK-5000)
2. **İşlem:** `removeDroid($droid1)` ile IHOP-123 çıkarıldı
3. **Sonuç:** Join tablosunda sadece 2 satır kaldı!

### Büyülü Noktalar:

-   🪄 **Otomatik Join Tablo Yönetimi**: Doctrine, join tablosundaki satırları otomatik ekler/çıkarır
-   🔄 **Bidirectional Sync**: İlişki her iki tarafta da güncellenir
-   💾 **Persistent State**: `flush()` ile veritabanı durumu senkronize olur
-   🧹 **Clean-up**: Çıkarılan ilişkiler veritabanından otomatik temizlenir

### Anahtar Kavramlar:

-   **Entity İlişki Yönetimi**: Sadece PHP objelerini manipüle ediyoruz
-   **SQL Abstraction**: Join tablosundaki SQL işlemlerini Doctrine hallediyor
-   **Data Integrity**: Foreign key constraints otomatik korunuyor

## 📁 Güncellenmiş Dosyalar (İkinci Bölüm)

1. **src/DataFixtures/AppFixtures.php**
    - Özel starship oluşturma kodu eklendi (USS DroidCarrier)
    - Droid atama işlemleri eklendi (`addDroid()` metodları)
    - Droid çıkarma testi eklendi (`removeDroid()` metodu)
    - Doctrine büyü testi implement edildi

## 🎯 Final Durum

### Veritabanı İstatistikleri:

-   **Toplam Droid Sayısı:** 103 (100 factory + 3 özel)
-   **Aktif Starship:** USS DroidCarrier (ID: 116)
-   **Join Tablosu:** 2 aktif ilişki (D-3P0, BONK-5000)
-   **Çıkarılan Droid:** IHOP-123 (join tablosundan otomatik temizlendi)

### Başarıyla Tamamlanan İşlemler:

-   ✅ **ManyToMany İlişki Kurumu**: Starship ↔ Droid
-   ✅ **Özel Droid Oluşturma**: 3 unique droid with custom functions
-   ✅ **Starship Oluşturma**: USS DroidCarrier with droid transport mission
-   ✅ **İlişki Atama**: `addDroid()` metodlarıyla 3 droid atandı
-   ✅ **İlişki Çıkarma**: `removeDroid()` metoduyla 1 droid çıkarıldı
-   ✅ **Doctrine Büyüsü**: Join tablo otomatik yönetimi test edildi

**Doctrine gerçekten büyülü! Sadece PHP entity metodlarını kullanarak veritabanındaki join tablolarını otomatik yönetiyor!** 🪄✨

---

> **Not:** Bu adımlar 2 Ağustos 2025 tarihinde Symfony 7 - Doctrine Relations projesinde gerçekleştirilmiştir.
