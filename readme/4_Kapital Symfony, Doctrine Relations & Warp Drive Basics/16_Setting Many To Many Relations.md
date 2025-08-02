# 🤖 Setting Many To Many Relations / Çoktan Çoğa İlişkilerin Ayarlanması

Şimdi, `ManyToMany`'nin son kısmına dalalım. Bir köşede, `Starship` varlığı var ve bu varlık, `ManyToMany` ilişkisi ile `Droid` varlığına bağlı. Bu ilişki bize, hangi droidlerin hangi yıldız gemilerine bindiğini takip eden ekstra bir tablo olan bir "join table" (ilişki tablosu) kazandırıyor. Peki bir `Droid`'i bir `Starship`'e nasıl atarız? `AppFixtures`'a geçelim.

## Adding some droids / Bazı droidler eklemek

Önce, birkaç droid ekleyelim. Üç droid oluşturan kodu ekleyeceğim. Sınıfı hızlıca içe aktarın ya da `Alt + Enter` kullanın:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 4
use App\Entity\Droid;
// ... lines 6 - 11
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 16 - 22
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
// ... lines 38 - 63
    }
}
```

👉 Bu kodda, üç farklı droid oluşturulup özellikleri ayarlandıktan sonra veritabanına kaydediliyor.

Ve... droidlerimiz oldu! Pek bir numarası yok: yeni bir `Droid` oluşturmak, gerekli özellikleri ayarlamak, `persist` ve `flush` etmek.

## Assigning Droids to Starships / Droid'leri Starship'lere Atamak

Şimdi eğlenceli kısma geçelim: Bir `Droid`'i bir `Starship`'e atamak. Bir `Starship` değişkeni oluşturun ve büyüye hazır olun:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 11
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $starship = StarshipFactory::createOne([
// ... lines 17 - 21
        ]);
// ... lines 23 - 63
    }
}
```

👉 Burada, bir yıldız gemisi (`Starship`) örneği oluşturuluyor.

Bu iki varlığı ilişkilendirmenin yolu şaşırtıcı derecede basit ve `OneToMany` ilişkisinden tanıdık gelecektir. Tahmin edebilirsin!

`flush()`'tan önce: `$starship->addDroid($droid1)`. Aynı şeyi diğer iki droid için de yap — `$starship->addDroid($droid2)` ve `$starship->addDroid($droid3)`:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 11
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $starship = StarshipFactory::createOne([
// ... lines 17 - 21
        ]);
// ... lines 23 - 25
        $starship->addDroid($droid1);
        $manager->persist($droid1);
// ... lines 28 - 31
        $starship->addDroid($droid2);
        $manager->persist($droid2);
// ... lines 34 - 37
        $starship->addDroid($droid3);
        $manager->persist($droid3);
        $manager->flush();
// ... lines 41 - 66
    }
}
```

👉 Bu kodda, her üç droid de ilgili yıldız gemisine atanıyor ve ardından veritabanına kaydediliyor.

Ekip droid yapımı pankeklerine hazır, haydi bunu deneyelim!

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, `fixtures` dosyasını yükleyerek veritabanını günceller.

Hiç hata yok. Gerçekten çalışıp çalışmadığını görmek için şunu çalıştır:

```shell
symfony console doctrine:query:sql 'SELECT * FROM droid'
```

👉 Bu komut, veritabanındaki `droid` tablosundaki tüm satırları listeler.

Beklendiği gibi: Oluşturduğumuz üç droid için üç satır var. Şimdi, `join table` olan `starship_droid`'a göz at.

```shell
symfony console doctrine:query:sql 'SELECT * FROM starship_droid'
```

👉 Bu komut, her droid-gemi eşleştirmesi için bir satır olacak şekilde `starship_droid` tablosunu listeler.

## The Magic of Doctrine / Doctrine'in Büyüsü

Gerçek büyü, Doctrine ile ilgilenmemiz gereken tek şeyin bir `Droid` nesnesini bir `Starship` nesnesine ilişkilendirmek olması. Sonrasını, join tablosundaki satırların eklenmesi ve silinmesini Doctrine hallediyor.

`flush` sonrası, join tablosunda üç satır olduğunu biliyoruz. Şimdi, `flush`'tan sonra bir atamayı kaldır: `$starship->removeDroid($droid1)`:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 11
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 16 - 39
        $manager->flush();
        $starship->removeDroid($droid1);
        $manager->flush();
// ... lines 44 - 69
    }
}
```

👉 Burada, bir droid yıldız gemisinden çıkarılıyor ve tekrar `flush` ile veritabanı güncelleniyor.

Fixtures'ı yeniden yükleyin ve join tablosuna tekrar bakın.

```shell
symfony console doctrine:query:sql 'SELECT * FROM droid'
```

👉 Bu komut, artık sadece iki satır kaldığını gösterir; Doctrine, çıkarılan droid için satırı kaldırdı.

## Owning vs Inverse Sides / Sahip Olan ve Ters Taraflar

`ManyToMany` ile ilgili son bir dokunuş — ilişki taraflarından hangisinin "sahip olan" hangisinin "ters" taraf olduğunu hatırlıyor musun? Gördüğümüz gibi, metotlarımız ilişkiyi senkronize ediyor, `addDroid()` çağrıldığında `Droid`'i `Starship`'e ekliyor:

```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 207
    public function addDroid(Droid $droid): static
    {
        if (!$this->droids->contains($droid)) {
            $this->droids->add($droid);
        }
        return $this;
    }
// ... lines 216 - 222
}
```

👉 Bu kodda, eğer ilgili droid mevcut değilse, droid yıldız gemisine ekleniyor.

Yani, sahip olan taraf çok da önemli değil.

Peki hangi taraf sahip olan taraf? `ManyToMany`'de, her iki taraf da sahip olabilir.

Kimin patron olduğunu öğrenmek için `inversedBy` seçeneğine bakın. Orada `ManyToMany` ve `inversedBy: starships` yazıyor, yani `Droid.starships` özelliği ters taraf.

Bu çoğunlukla önemsizdir ama kontrol manyağıysanız ve join tablosunun adını belirlemek istiyorsanız, bir `JoinTable` niteliği ekleyebilirsiniz. Ama unutmayın, bu sadece sahip olan tarafa eklenebilir. Onun dışında, endişelenmeye gerek yok.

Sonraki adımda, yeni ilişkiyi kullanarak her gemiye atanan droidleri göstereceğiz.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./15_Many-To-Many Relationship.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./17_Accessing Data on a ManyToMany.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
