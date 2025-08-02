# 🤖 Droid Entity for the ManyToMany Relationship - Uygulama Adımları

Bu dokümanda, ManyToMany ilişkisi için Droid Entity'sinin oluşturulması sürecinde gerçekleştirilen adımlar detaylandırılmıştır.

## 📋 Yapılan İşlemler Özeti

### Adım 1: Droid Entity Oluşturma ✅

**Komut:**

```shell
symfony console make:entity Droid
```

**Eklenen Property'ler:**

-   `name` (string, 255 karakter, nullable=false)
-   `primaryFunction` (string, 255 karakter, nullable=false)

**Oluşturulan Dosya:** `src/Entity/Droid.php`

**Entity İçeriği:**

```php
<?php

namespace App\Entity;

use App\Repository\DroidRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DroidRepository::class)]
class Droid
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $primaryFunction = null;

    // Getter ve Setter metodları...
}
```

### Adım 2: Migration Oluşturma ve Veritabanı Güncelleme ✅

**Migration Oluşturma:**

```shell
symfony console make:migration
```

**Veritabanını Güncelleme:**

```shell
symfony console doctrine:schema:update --force
```

**Sonuç:** `droid` tablosu başarıyla oluşturuldu.

### Adım 3: Droid Factory Oluşturma ✅

**Komut:**

```shell
symfony console make:factory Droid
```

**Oluşturulan Dosya:** `src/Factory/DroidFactory.php`

### Adım 4: Factory İçeriğini Güncelleme ✅

**Güncellenen Bölüm:** `defaults()` metodu

```php
protected function defaults(): array|callable
{
    return [
        'name' => self::faker()->randomElement([
            'R2-D2', 'C-3PO', 'BB-8', 'ZZZ-123',
        ]),
        'primaryFunction' => self::faker()->randomElement([
            'astromech',
            'protocol',
            'astromech',
            'assassin',
            'sleeper',
        ]),
    ];
}
```

### Adım 5: AppFixtures Dosyasını Güncelleme ✅

**Dosya:** `src/DataFixtures/AppFixtures.php`

**Eklenen Import:**

```php
use App\Factory\DroidFactory;
```

**Eklenen Satır:**

```php
DroidFactory::createMany(100);
```

### Adım 6: Fixtures Yükleme ✅

**Komut:**

```shell
symfony console doctrine:fixtures:load --no-interaction
```

**Sonuç:** 100 adet droid verisi başarıyla veritabanına eklendi.

## 🔍 Doğrulama

**Droid Sayısını Kontrol Etme:**

```shell
symfony console dbal:run-sql "SELECT COUNT(*) as droid_count FROM droid"
```

**Sonuç:** 100 droid başarıyla oluşturuldu.

**Örnek Droid Verilerini Görüntüleme:**

```shell
symfony console dbal:run-sql "SELECT * FROM droid LIMIT 5"
```

## 📁 Oluşturulan/Güncellenen Dosyalar

1. **Yeni Dosyalar:**

    - `src/Entity/Droid.php` - Droid entity'si
    - `src/Factory/DroidFactory.php` - Droid factory'si
    - `src/Repository/DroidRepository.php` - Droid repository'si (otomatik oluşturuldu)
    - `migrations/Version20250802073428.php` - Migration dosyası

2. **Güncellenen Dosyalar:**
    - `src/DataFixtures/AppFixtures.php` - DroidFactory import ve createMany eklendi

## 📊 Veritabanı Durumu

```sql
-- Droid tablosu yapısı
CREATE TABLE droid (
    id INT AUTO_INCREMENT NOT NULL,
    name VARCHAR(255) NOT NULL,
    primary_function VARCHAR(255) NOT NULL,
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

-- 100 adet droid verisi yüklendi
-- Örnek veriler: R2-D2, C-3PO, BB-8, ZZZ-123
-- Fonksiyonlar: astromech, protocol, assassin, sleeper
```

## ✨ Özet

Droid Entity'si başarıyla oluşturuldu ve ManyToMany ilişkisi kurulumuna hazır hale getirildi. Şu ana kadar:

-   ✅ Droid entity'si oluşturuldu
-   ✅ Veritabanı tablosu oluşturuldu
-   ✅ Factory ile test verileri hazırlandı
-   ✅ 100 adet droid verisi yüklendi

**Sonraki Adım:** Starship ve Droid arasında ManyToMany ilişkisi kurulacak.

---

> **Not:** Bu adımlar 2 Ağustos 2025 tarihinde Symfony 7 - Doctrine Relations projesinde gerçekleştirilmiştir.
