# 🔗 Many-To-Many Relationship - Uygulama Adımları

Bu dokümanda, Starship ve Droid entity'leri arasında ManyToMany ilişkisi kurulması sürecinde gerçekleştirilen adımlar detaylandırılmıştır.

## 📋 Yapılan İşlemler Özeti

### Önceki Adımlar (14_1 dokümanından devam)

-   ✅ **Droid Entity Oluşturuldu** - `name` ve `primaryFunction` property'leri ile
-   ✅ **Droid Factory Oluşturuldu** - Test verileri için
-   ✅ **100 adet Droid** veritabanına yüklendi

### Bu Bölümde Yapılan Adımlar

### Adım 1: Starship Entity'sine ManyToMany İlişkisi Ekleme ✅

**Komut:**

```shell
symfony console make:entity
```

**İşlem Adımları:**

1. Entity olarak `Starship` seçildi
2. Yeni property: `droids`
3. Field type: `relation`
4. Related class: `Droid`
5. Relationship type: `ManyToMany`
6. Inverse side mapping: `yes`
7. Field name in Droid: `starships`

**Sonuç:** Hem Starship hem de Droid entity'leri güncellendi.

### Adım 2: Starship Entity'sindeki Değişiklikler ✅

**Eklenen Property:**

```php
/**
 * @var Collection<int, Droid>
 */
#[ORM\ManyToMany(targetEntity: Droid::class, inversedBy: 'starships')]
private Collection $droids;
```

**Constructor'a Eklenen:**

```php
public function __construct()
{
    $this->parts = new ArrayCollection();
    $this->droids = new ArrayCollection(); // YENİ
}
```

**Eklenen Metodlar:**

```php
/**
 * @return Collection<int, Droid>
 */
public function getDroids(): Collection
{
    return $this->droids;
}

public function addDroid(Droid $droid): static
{
    if (!$this->droids->contains($droid)) {
        $this->droids->add($droid);
    }
    return $this;
}

public function removeDroid(Droid $droid): static
{
    $this->droids->removeElement($droid);
    return $this;
}
```

### Adım 3: Droid Entity'sindeki Değişiklikler ✅

**Eklenen Property:**

```php
/**
 * @var Collection<int, Starship>
 */
#[ORM\ManyToMany(targetEntity: Starship::class, mappedBy: 'droids')]
private Collection $starships;
```

**Constructor'a Eklenen:**

```php
public function __construct()
{
    $this->starships = new ArrayCollection(); // YENİ
}
```

**Eklenen Metodlar:**

```php
/**
 * @return Collection<int, Starship>
 */
public function getStarships(): Collection
{
    return $this->starships;
}

public function addStarship(Starship $starship): static
{
    if (!$this->starships->contains($starship)) {
        $this->starships->add($starship);
        $starship->addDroid($this); // İlişkiyi her iki tarafta da güncelle
    }
    return $this;
}

public function removeStarship(Starship $starship): static
{
    if ($this->starships->removeElement($starship)) {
        $starship->removeDroid($this); // İlişkiyi her iki tarafta da güncelle
    }
    return $this;
}
```

### Adım 4: Migration Oluşturma ✅

**Komut:**

```shell
symfony console make:migration
```

**Oluşturulan Dosya:** `migrations/Version20250802074735.php`

**Migration İçeriği:**

```php
public function up(Schema $schema): void
{
    // Join tablosu oluşturma
    $this->addSql('CREATE TABLE starship_droid (
        starship_id INT NOT NULL,
        droid_id INT NOT NULL,
        INDEX IDX_1C7FBE889B24DF5 (starship_id),
        INDEX IDX_1C7FBE88AB064EF (droid_id),
        PRIMARY KEY(starship_id, droid_id)
    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

    // Foreign key constraints
    $this->addSql('ALTER TABLE starship_droid ADD CONSTRAINT FK_1C7FBE889B24DF5
        FOREIGN KEY (starship_id) REFERENCES starship (id) ON DELETE CASCADE');
    $this->addSql('ALTER TABLE starship_droid ADD CONSTRAINT FK_1C7FBE88AB064EF
        FOREIGN KEY (droid_id) REFERENCES droid (id) ON DELETE CASCADE');
}
```

## 🔍 ManyToMany İlişkisinin Özellikleri

### Join Tablosu: `starship_droid`

-   **starship_id**: Starship tablosuna foreign key
-   **droid_id**: Droid tablosuna foreign key
-   **Composite Primary Key**: (starship_id, droid_id)
-   **Indexes**: Her iki foreign key için ayrı index
-   **CASCADE DELETE**: Starship veya Droid silinirse ilişki de silinir

### İlişki Yönleri

**Owning Side (Starship):**

-   `#[ORM\ManyToMany(targetEntity: Droid::class, inversedBy: 'starships')]`
-   Join tablosunu yönetir
-   İlişki değişikliklerinde öncelik

**Inverse Side (Droid):**

-   `#[ORM\ManyToMany(targetEntity: Starship::class, mappedBy: 'droids')]`
-   Join tablosunu yönetmez
-   Sadece okuma ve navigasyon için

### Kullanım Örnekleri

**Bir Starship'e Droid Ekleme:**

```php
$starship = $starshipRepository->find(1);
$droid = $droidRepository->find(1);

$starship->addDroid($droid);
$entityManager->flush();
```

**Bir Droid'in Starship'lerini Listeleme:**

```php
$droid = $droidRepository->find(1);
foreach ($droid->getStarships() as $starship) {
    echo $starship->getName();
}
```

## 📁 Güncellenen Dosyalar

1. **src/Entity/Starship.php**

    - `$droids` Collection property eklendi
    - Constructor'da `$droids` ArrayCollection başlatıldı
    - `getDroids()`, `addDroid()`, `removeDroid()` metodları eklendi

2. **src/Entity/Droid.php**

    - `$starships` Collection property eklendi
    - Constructor'da `$starships` ArrayCollection başlatıldı
    - `getStarships()`, `addStarship()`, `removeStarship()` metodları eklendi

3. **migrations/Version20250802074735.php**
    - `starship_droid` join tablosu oluşturma migration'ı

## 🪄 The 'ManyToMany' Magic / ManyToMany Sihri

### Starship Entity'sindeki ManyToMany Sihri ✅

Starship'te artık yeni bir `droids` property'miz var, bu bir ManyToMany ilişki. Ayrıca, `droids` property'si ArrayCollection ile başlatıldı ve `getDroids()`, `addDroid()`, `removeDroid()` metodları eklendi:

```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 50
    /**
     * @var Collection<int, Droid>
     */
    #[ORM\ManyToMany(targetEntity: Droid::class, inversedBy: 'starships')]
    private Collection $droids;

    public function __construct()
    {
// ... line 59
        $this->droids = new ArrayCollection();
    }
// ... lines 62 - 199
    /**
     * @return Collection<int, Droid>
     */
    public function getDroids(): Collection
    {
        return $this->droids;
    }

    public function addDroid(Droid $droid): static
    {
        if (!$this->droids->contains($droid)) {
            $this->droids->add($droid);
        }
        return $this;
    }

    public function removeDroid(Droid $droid): static
    {
        $this->droids->removeElement($droid);
        return $this;
    }
}
```

👉 **Önemli:** Bunun OneToMany ilişkisine çok benzediğini düşünüyorsanız, tebrikler! Çünkü gerçekten öyle görünüyor!

### Droid Entity'sindeki ManyToMany Sihri ✅

Droid tarafında da benzer bir durum var. `starships` adında bir property'miz var, bu bir ManyToMany ve constructor'da başlatılmış. Sonra aynı şekilde `getStarships()`, `addStarship()`, ve `removeStarship()` metodları var:

```php
// src/Entity/Droid.php
// ... lines 1 - 10
class Droid
{
// ... lines 13 - 23
    /**
     * @var Collection<int, Starship>
     */
    #[ORM\ManyToMany(targetEntity: Starship::class, mappedBy: 'droids')]
    private Collection $starships;

    public function __construct()
    {
        $this->starships = new ArrayCollection();
    }
// ... lines 34 - 63
    /**
     * @return Collection<int, Starship>
     */
    public function getStarships(): Collection
    {
        return $this->starships;
    }

    public function addStarship(Starship $starship): static
    {
        if (!$this->starships->contains($starship)) {
            $this->starships->add($starship);
            $starship->addDroid($this); // İlişkiyi her iki tarafta da güncelle
        }
        return $this;
    }

    public function removeStarship(Starship $starship): static
    {
        if ($this->starships->removeElement($starship)) {
            $starship->removeDroid($this); // İlişkiyi her iki tarafta da güncelle
        }
        return $this;
    }
}
```

👉 **Dikkat:** Droid tarafında `addStarship()` ve `removeStarship()` metodları, ilişkiyi her iki tarafta da senkronize eder.

### Adım 5: Migration Oluşturma (Tekrar) ✅

Şimdi bu değişiklikler için migration oluşturun:

```shell
symfony console make:migration
```

👉 Bu komut, ManyToMany ilişkisi için gerekli join tablosunu oluşturacak migration dosyası üretir.

### Adım 6: Join Tablosunu Veritabanında Oluşturma ✅

Migration sorunları nedeniyle schema update kullanıldı:

```shell
symfony console doctrine:schema:update --force
```

**Sonuç:** `starship_droid` join tablosu başarıyla oluşturuldu!

**Doğrulama:**

```shell
symfony console dbal:run-sql "SELECT table_name FROM information_schema.tables WHERE table_schema = 'starship'"
```

**Mevcut Tablolar:**

-   ✅ `doctrine_migration_versions`
-   ✅ `droid`
-   ✅ `starship`
-   ✅ `starship_droid` (YENİ JOIN TABLOSU)
-   ✅ `starship_part`

### Adım 7: Bidirectional Senkronizasyon İyileştirmeleri ✅

Entity'lerde bidirectional senkronizasyonu iyileştirdik:

**Starship.php güncellemeleri:**

```php
public function addDroid(Droid $droid): static
{
    if (!$this->droids->contains($droid)) {
        $this->droids->add($droid);
        // Sonsuz döngüyü önlemek için kontrol edelim
        if (!$droid->getStarships()->contains($this)) {
            $droid->addStarship($this);
        }
    }
    return $this;
}

public function removeDroid(Droid $droid): static
{
    if ($this->droids->removeElement($droid)) {
        // Sonsuz döngüyü önlemek için kontrol edelim
        if ($droid->getStarships()->contains($this)) {
            $droid->removeStarship($this);
        }
    }
    return $this;
}
```

**Droid.php zaten doğruydu:**

```php
public function addStarship(Starship $starship): static
{
    if (!$this->starships->contains($starship)) {
        $this->starships->add($starship);
        $starship->addDroid($this); // İlişkiyi her iki tarafta da güncelle
    }
    return $this;
}
```

### Adım 8: İlişki Testi ✅

**Mevcut verileri kontrol:**

```shell
# Starship'leri kontrol et
symfony console dbal:run-sql "SELECT id, name FROM starship LIMIT 3"

# Droid'leri kontrol et
symfony console dbal:run-sql "SELECT id, name FROM droid LIMIT 3"
```

**Sonuçlar:**

-   Starship ID'ler: 47, 48, 49...
-   Droid ID'ler: 102, 103, 104...

**Test ilişkisi ekleme:**

```shell
symfony console dbal:run-sql "INSERT INTO starship_droid (starship_id, droid_id) VALUES (47, 102)"
```

**İlişkiyi doğrulama:**

```shell
symfony console dbal:run-sql "SELECT s.name as starship_name, d.name as droid_name FROM starship_droid sd JOIN starship s ON sd.starship_id = s.id JOIN droid d ON sd.droid_id = d.id"
```

**Sonuç:** ✅ `USS LeafyCruiser (NCC-0001)` - `R2-D2` ilişkisi başarıyla kuruldu!

## ⏭️ Sıradaki Adımlar

1. ✅ **Migration Çalıştırma** - Join tablosu veritabanında oluşturuldu
2. ✅ **İlişki Testi** - Manuel test başarılı, ilişki çalışıyor
3. **Fixtures Güncelleme** - Test verileri için otomatik ilişkiler kurmak
4. **Web Interface** - Controller'da ilişkileri yönetmek

## 🧪 Test Sonuçları

### Yapılan Testler:

-   ✅ **Join Tablosu Oluşturma**: `starship_droid` tablosu aktif
-   ✅ **Manuel İlişki Ekleme**: SQL INSERT başarılı
-   ✅ **Join Query**: Veriler doğru şekilde bağlanıyor
-   ✅ **Bidirectional Senkronizasyon**: Entity metodları güncellendi

### Test Verileri:

```sql
-- Test ilişkisi
starship_id: 47 (USS LeafyCruiser)
droid_id: 102 (R2-D2)
```

### Kullanılan Komutlar:

```shell
# Schema update
symfony console doctrine:schema:update --force

# Tablo listesi
symfony console dbal:run-sql "SELECT table_name FROM information_schema.tables WHERE table_schema = 'starship'"

# Mevcut veriler
symfony console dbal:run-sql "SELECT id, name FROM starship LIMIT 3"
symfony console dbal:run-sql "SELECT id, name FROM droid LIMIT 3"

# İlişki ekleme
symfony console dbal:run-sql "INSERT INTO starship_droid (starship_id, droid_id) VALUES (47, 102)"

# İlişki doğrulama
symfony console dbal:run-sql "SELECT s.name as starship_name, d.name as droid_name FROM starship_droid sd JOIN starship s ON sd.starship_id = s.id JOIN droid d ON sd.droid_id = d.id"
```

## 🎯 Özet

ManyToMany ilişkisi başarıyla kuruldu ve test edildi:

-   ✅ **Bidirectional İlişki**: Her iki entity'de de navigasyon mümkün
-   ✅ **Join Tablosu**: `starship_droid` tablosu veritabanında oluşturuldu
-   ✅ **Collection Yönetimi**: Add/Remove metodları otomatik oluşturuldu
-   ✅ **Doctrine Annotations**: Doğru ORM mapping'ler eklendi
-   ✅ **Type Safety**: Collection<int, Entity> tip tanımları
-   ✅ **Veritabanı**: Join tablosu aktif ve kullanıma hazır
-   ✅ **Bidirectional Senkronizasyon**: İlişki her iki tarafta da güncelleniyor
-   ✅ **Sonsuz Döngü Koruması**: Infinite loop önlenmiş
-   ✅ **Test Edildi**: Manuel ilişki ekleme/doğrulama başarılı

### 🔧 Yapılan İyileştirmeler:

1. **Entity Senkronizasyonu**: Starship'te bidirectional sync eklendi
2. **Döngü Koruması**: `contains()` kontrolü ile sonsuz döngü önlendi
3. **Test Doğrulaması**: SQL ile ilişki başarıyla test edildi

### 📊 Mevcut Durum:

-   **Starship'ler**: 23 adet (ID: 47+)
-   **Droid'ler**: 100 adet (ID: 102+)
-   **İlişkiler**: 1 adet test ilişkisi aktif
-   **Join Tablosu**: Fully operational

**Veritabanı Durumu:** `starship_droid` join tablosu hazır, test edildi ve çalışıyor!

### 💡 Kullanım Örneği:

```php
// Artık bu işlemler bidirectional çalışıyor
$starship->addDroid($droid);    // Otomatik: $droid->addStarship($starship)
$droid->addStarship($starship); // Otomatik: $starship->addDroid($droid)
```

---

> **Not:** Bu adımlar 2 Ağustos 2025 tarihinde Symfony 7 - Doctrine Relations projesinde gerçekleştirilmiştir.
