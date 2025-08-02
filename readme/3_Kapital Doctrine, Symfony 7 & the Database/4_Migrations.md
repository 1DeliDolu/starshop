# 🧱 Migrations / Veritabanı Geçişleri

Bir `Starship` varlığımız var... ama `starship` tablomuz yok! Çözüm ne? Veritabanı geçişleri!

## `make:migration`

İlk geçişimizi çalıştırarak oluşturalım:

```bash
symfony console make:migration
```

👉 Bu komut, yeni bir geçiş dosyası oluşturur.

Başarılı! Bu komut gerçek tabloyu eklemedi, ancak `migrations/` dizininde yeni bir dosya oluşturdu. Haydi buna bakalım!

Ooh, bu bir PHP sınıfı ve `up()` metodu, tablomuzu oluşturmak için gereken SQL komutunu içeriyor. Güzel olan şu: Doctrine, varlıklarımızın mevcut durumu ile veritabanını karşılaştırdı ve onları eşleştirmek için gerekli SQL'i oluşturdu. Vay canına!

```php
//migrations/Version20241111171351.php
// ... lines 1 - 12
final class Version20241111171351 extends AbstractMigration
{
// ... lines 15 - 19
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE starship (
        id SERIAL NOT NULL,
        name VARCHAR(255) NOT NULL,
        class VARCHAR(255) NOT NULL,
        captain VARCHAR(255) NOT NULL,
        status VARCHAR(255) NOT NULL,
        arrived_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
        PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN starship.arrived_at IS \'(DC2Type:datetime_immutable)\'');
    }
// ... lines 26 - 32
}
```

👉 Bu sınıf, `starship` tablosunu oluşturmak için gerekli SQL komutlarını içerir.

Ayrıca bir `down()` metodu da var... çünkü geçişler geri alınabilir. Ama ben bunu hiç kullanmadım, bu yüzden `down()` kısmını dert etmiyorum.

SQL hakkında dikkat edilmesi gereken bir şey: Kullandığınız veritabanı platformuna özel biçimdedir. Bizim durumumuzda, bu Postgres'e özgü SQL. SQLite kullanıyorsanız, SQLite'a özgü SQL görürdünüz.

İsterseniz, `getDescription()` metoduna bu geçişin ne yaptığıyla ilgili bir not ekleyin:

```php
// migrations/Version20241111171351.php
// ... lines 1 - 12
final class Version20241111171351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add starship table';
    }
// ... lines 19 - 32
}
```

👉 Bu metod, geçişin açıklamasını döndürür.

## Checking the Migration Status / Geçiş Durumunu Kontrol Etme

Terminale geçin ve şunu çalıştırın:

```bash
symfony console doctrine:migrations:list
```

👉 Bu komut, mevcut geçişleri ve durumlarını listeler.

Çıktı biraz garip olabilir ama geçiş sınıfımızı ve açıklamasını görebiliriz. Durum: henüz uygulanmadı. Haydi uygulayalım!

```bash
symfony console doctrine:migrations:migrate
```

👉 Bu komut, tanımlanan geçişleri veritabanına uygular.

Devam etmek istiyor muyuz? Evet! Başarılı! Şimdi tekrar deneyin:

```bash
symfony console doctrine:migrations:list
```

👉 Bu komutla geçişin artık "migrated" olduğunu görebilirsiniz.

## How Migrations Work / Geçişler Nasıl Çalışır?

Doctrine, hangi geçişlerin çalıştırıldığını nasıl izler? Bir `doctrine_migration_versions` tablosu oluşturur ve her çalıştırılan geçiş için bu tabloya bir satır ekler.

Görebiliriz! Şunu çalıştırın:

```bash
symfony console doctrine:query:sql "select * from doctrine_migration_versions"
```

👉 Bu komut, geçişlerin kaydedildiği tabloyu sorgular.

Bakın! Orada geçiş sınıfımız var, ne zaman çalıştırıldığı, ne kadar sürdüğü ve geçişin en sevdiği renk... şaka şaka, sonuncusu yok.

Bu, artık bir `starship` tablomuz olduğu anlamına mı geliyor? Bir başka ham SQL sorgusu ile öğrenelim!

```bash
symfony console doctrine:query:sql "select * from starship"
```

👉 Bu komut, `starship` tablosundaki verileri sorgular.

Sorgu boş bir sonuç döndürdü.

Yeşil demek iyi demek, değil mi? Aynen öyle! Bu bize `starship` tablosunda henüz veri olmadığını... ama tablonun var olduğunu gösteriyor!

Varlık sınıfı tamam: ✅
Veritabanı tablosu tamam: ✅
Veritabanında veri? Bunu bir sonraki bölümde öğreneceğiz!

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./3_Starship Entity.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./4_1_mysql.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
