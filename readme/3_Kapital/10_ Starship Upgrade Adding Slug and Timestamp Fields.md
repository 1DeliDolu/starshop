# 🚀 Starship Upgrade: Adding Slug and Timestamp Fields / Uzay Gemisi Güncellemesi: Slug ve Zaman Damgası Alanları Ekleme

Starfleet Karargahı’ndaki amirallerden yeni gereksinimler geldi. Artık URL'de `id` görmek istemiyorlar, yani `/starship/1` yerine `/starship/enterprise` gibi insan tarafından okunabilir bir ad istiyorlar. Buna “slug” denir. Bunu gerçekleştirmek için `Starship` varlığına yeni bir alan eklememiz gerekiyor.

## ➕ Adding Fields to an Existing Entity / Mevcut Bir Varlığa Alan Ekleme

Bunu elle de ekleyebiliriz: özelliği, getter/setter metodlarını ve `#[ORM\Column]` özniteliğini eklemek yeterli olur. Ya da biraz hile yapabiliriz! Şu komutu çalıştırın:

```bash
symfony console make:entity Starship
```

Bu sefer yeni bir varlık oluşturmak yerine mevcut bir varlığa alan ekleyeceğiz. `slug` adında, türü `string`, uzunluğu 255 olan bir alan ekleyin. "Nullable olsun mu?" – hayır, ama şimdilik evet seçin. Ayrıca iki kullanışlı alan daha ekleyelim: `updatedAt`, türü `datetime_immutable`, nullable? evet; ve `createdAt`, türü `datetime_immutable`, nullable? evet.

Komuttan çıkmak için Enter tuşuna basın. `Starship` varlığını kontrol edin: `src/Entity/Starship.php`

```php
// src/Entity/Starship.php

// ... lines 1 - 8
class Starship
{
// ... lines 11 - 30
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slug = null;
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;
// ... lines 39 - 153
}
```
👉 `slug`, `createdAt` ve `updatedAt` alanları başarıyla eklendi. `slug` için `length: 255` silinebilir, çünkü bu zaten varsayılandır.

## 📦 First Migration / İlk Göç (Migration)

Yeni alan eklendi, harika! Ama bu sütunlar hâlâ veritabanında yok. Bunun için migration oluşturmalıyız:

```bash
symfony console make:migration
```

Yeni migration dosyasını açın. Doctrine, entity sınıfıyla veritabanını karşılaştırarak SQL’i otomatik oluşturur:

```php
// migrations/Version20241201203154.php

// ... lines 1 - 12
final class Version20241201203154 extends AbstractMigration
{
// ... lines 15 - 19
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE starship ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE starship ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE starship ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN starship.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN starship.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }
// ... lines 29 - 37
}
```

👉 Bu migration, yeni alanları veritabanına ekler.

Açıklama ekleyin:

```php
    public function getDescription(): string
    {
        return 'Add slug and timestamps to starship';
    }
```

Ve migration’ı çalıştırın:

```bash
symfony console doctrine:migrations:migrate
```

👉 Bu komut, yeni sütunları veritabanına uygular.

Kontrol etmek için sorgu çalıştırın:

```bash
symfony console doctrine:query:sql "SELECT name, slug, updated_at, created_at FROM starship"
```

👉 Sütunlar var ama değerleri boş. Bunları sonradan otomatik olarak ayarlayacağız.

## ❗ Making Fields Required / Alanları Zorunlu Yapma

`Starship` sınıfını açın. `$slug` için `nullable: true` kısmını kaldırın. Bu, varsayılan olarak `nullable: false` olur.

Ayrıca `unique: true` özelliğini ekleyin. Aynı şekilde `$createdAt` ve `$updatedAt` için de `nullable: true` kısımlarını kaldırın:

```php
//bsrc/Entity/Starship.php

// ... lines 1 - 8
class Starship
{
// ... lines 11 - 30
    #[ORM\Column(unique: true)]
    private ?string $slug = null;
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;
// ... lines 39 - 153
}
```
👉 Alanlar artık zorunlu (`NOT NULL`) ve `slug` benzersiz (`UNIQUE`) olacak şekilde ayarlandı.

## 📦 Second Migration / İkinci Göç

Yine entity değişti ve veritabanı güncellenmeli. Şu komutu çalıştırın:

```
symfony console make:migration
```

Yeni migration dosyasını açın. `up()` metodunda sütunları `NOT NULL` olarak değiştiren ve `slug` için `UNIQUE INDEX` oluşturan SQL var:

```php 

//bmigrations/Version20241201203519.php

// ... lines 1 - 12
final class Version20241201203519 extends AbstractMigration
{
// ... lines 15 - 19
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE starship ALTER slug SET NOT NULL');
        $this->addSql('ALTER TABLE starship ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE starship ALTER updated_at SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C414E64A989D9B62 ON starship (slug)');
    }
}
```

👉 Bu migration, sütunları zorunlu yapar ve `slug` için benzersiz indeks oluşturur.

Açıklama ekleyin:

```php
    public function getDescription(): string
    {
        return 'Make slug and timestamps not nullable';
    }
```

Ve çalıştırın:

```
symfony console doctrine:migrations:migrate
```

💥 Hata! Alanlar hâlâ `NULL`, bu yüzden `NOT NULL` yapılamaz.

## ✍️ Editing the Migration with Custom SQL / Migration’ı El ile SQL Ekleyerek Düzenleme

Son migration dosyasını açın. `up()` metodunun başına şu satırı ekleyin:

```php
$this->addSql('UPDATE starship SET slug = id, created_at = arrived_at, updated_at = arrived_at');
```

```php
// migrations/Version20241201203519.php

// ... lines 1 - 12
final class Version20241201203519 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE starship SET slug = id, created_at = arrived_at, updated_at = arrived_at');
        // ... devamındaki SQL'ler
    }
}
```
👉 Bu SQL, `slug` alanına `id`, `created_at` ve `updated_at` alanlarına da `arrived_at` zamanını kopyalar.

Şimdi tekrar migration’ı çalıştırın:

```bash
symfony console doctrine:migrations:migrate
```

Ve veriyi kontrol edin:

```bash
symfony console doctrine:query:sql "SELECT name, slug, updated_at, created_at FROM starship"
```

👉 Alanlar artık dolu!

## ♻️ Reloading the Fixtures / Fixtures Verilerini Yeniden Yükleme

Ancak şu anda bir sorun var. Şu komutu çalıştırın:

```bash
symfony console doctrine:fixtures:load
```

💥 Patlama! Fixtures dosyasında bu üç gerekli alan için değer atanmadı.

`StarshipFactory` dosyasını güncelleyerek bu alanlara varsayılan değerler atayabiliriz… ama bir sonraki adımda çok daha iyi bir yöntem göstereceğiz: bu alanları otomatik olarak ayarlayan bir "doctrine extension" paketi. En iyisi bu… sırada o var!

