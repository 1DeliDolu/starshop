# 🔗 Many-To-Many Relationship / Çoktan-Çoka (Many-To-Many) İlişki

Artık elimizde `Starship` varlığı ve `Droid` varlığı kurulu ve birbirleriyle iletişime geçmeye hazır. Bu iki varlığı nasıl birbirine bağlayacağız?

Şöyle düşünün: Her `Starship`, işleri sorunsuz yürütmek (ve bazen biraz komedi için) bir grup `Droid` ekibine ihtiyaç duyacak... ve her `Droid` de birden fazla `Starship`te görev yapabilmeli. Veritabanını şimdilik unutun, sadece nesnelere odaklanın. `Starship` varlığımızda, kendisine atanan tüm `Droid`leri tutacak bir `droids` özelliği olması gerekir.

Harika! Terminale geri dönün ve şunu çalıştırın:

```shell
symfony console make:entity
```

👉 Bu komutla, `Starship` varlığını güncelleyip yeni bir `droids` özelliği ekleyeceğiz. "relation" seçeneğini kullanarak sihirbaza girin. Bu sefer ihtiyacımız olan ilişki türü: `ManyToMany`.

Her `Starship` birden fazla `Droid`e sahip olabilir ve her `Droid` birden fazla `Starship`te görev alabilir. Bu tam aradığımız şey!

Sihirbaz, ilişkinin ters tarafını da haritalamak isteyip istemediğimizi soracak. Yani, `Droid`lerin kendilerine bağlı tüm `Starship`leri listeleyebilmesini ister misiniz: `$droid->getShips()` gibi. Faydalı olur, o yüzden "evet" diyelim. `Droid` içindeki yeni alanın adı olarak `starships` gayet uygun.

Her iki tarafı da (hem `Starship` hem de `Droid`) güncellediğine dikkat edin. Değişiklikleri inceleyin.

## The 'ManyToMany' Magic / ManyToMany Sihri
`Starship` içerisinde artık yeni bir `droids` özelliğimiz var, bu bir `ManyToMany` ilişki. Ayrıca, `droids` özelliği `ArrayCollection` ile başlatıldı ve `getDroids()`, `addDroid()`, `removeDroid()` metodları eklendi:


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

👉 Bu sınıfta, `Starship` nesnesine çoklu `Droid` ekleyip çıkarmanızı sağlayan metotlar var.

Eğer bunun bir `OneToMany` ilişkisine çok benzediğini düşünüyorsanız, doğru! Çünkü aslında öyle görünüyor.

`Droid` tarafında da benzer bir durum var. `starships` adında bir özelliğimiz var, bu bir `ManyToMany` ve constructor'da başlatılmış. Sonra aynı şekilde `getStarships()`, `addStarship()`, ve `removeStarship()` metotları var:


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
            $starship->addDroid($this);
        }
        return $this;
    }
    public function removeStarship(Starship $starship): static
    {
        if ($this->starships->removeElement($starship)) {
            $starship->removeDroid($this);
        }
        return $this;
    }
}
```

👉 Bu sınıfta, bir `Droid` nesnesini farklı `Starship`lere ekleyip çıkarabilirsiniz.

Bunun için migration oluşturun. Terminale dönüp şunu çalıştırın:

```shell
symfony console make:migration
```

👉 Bu komut, ManyToMany ilişkisi için migration dosyası oluşturur.

## Unveiling the Join Table / Join Tablosunu Ortaya Çıkarmak
Harika! Oluşan migration dosyasına göz atın: gerçekten ilginç. Artık `starship_droid` adında yeni bir tablo var! Bu tabloda bir `starship_id` yabancı anahtarı ile bir `droid_id` yabancı anahtarı bulunuyor:


```php
// migrations/Version20250311014256.php
// ... lines 1 - 12
final class Version20250311014256 extends AbstractMigration
{
// ... lines 15 - 19
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE starship_droid (starship_id INT NOT NULL, droid_id INT NOT NULL, PRIMARY KEY(starship_id, droid_id))');
        $this->addSql('CREATE INDEX IDX_1C7FBE889B24DF5 ON starship_droid (starship_id)');
        $this->addSql('CREATE INDEX IDX_1C7FBE88AB064EF ON starship_droid (droid_id)');
        $this->addSql('ALTER TABLE starship_droid ADD CONSTRAINT FK_1C7FBE889B24DF5 FOREIGN KEY (starship_id) REFERENCES starship (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE starship_droid ADD CONSTRAINT FK_1C7FBE88AB064EF FOREIGN KEY (droid_id) REFERENCES droid (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
// ... lines 29 - 36
}
```

👉 Bu migration dosyası, iki varlık arasında join tablosu oluşturur ve gerekli anahtarları tanımlar.

Bir `ManyToMany` ilişkiyi veritabanında böyle yaparsınız: bir join tablosu ile. Doctrine’in asıl büyüsü, bizim sadece nesneleri düşünmemizi sağlamasıdır. Bir `Starship` nesnesi birçok `Droid` nesnesine sahip olur ve bir `Droid` nesnesi de birçok `Starship` nesnesine sahip olur. Doctrine, bu ilişkiyi veritabanına kaydetmenin tüm detaylarını otomatik olarak halleder.

Devam etmeden önce, bu migration'ı çalıştırın. Terminale dönüp şunu çalıştırın:

```shell
symfony console doctrine:migrations:migrate
```

👉 Bu komut, join tablosunu ve ilişkileri veritabanında aktif hale getirir.

Harika! Artık yepyeni bir join tablosuna sahibiz. Peki… `Droid` nesnelerini `Starship` nesneleriyle nasıl ilişkilendireceğiz? Sıradaki adımda bu anlatılacak... Ve buna bayılacaksınız!
