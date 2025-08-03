# 🧩 Persisting the More Complex Many-to-Many Relationship / Daha Karmaşık Çoktan-Çoka İlişkiyi Kalıcı Hale Getirme

Çoktan-çoka ilişkimizi, Doctrine’in bizim için bir join tablo oluşturm## 🎩 Hide that Join Entity / Join Entity'yi Gizlemek

Şimdi, join entity'yi gizleyecek ve bunun tam olarak eskiden sahip olduğumuz `ManyToMany` ilişkisi gibi çalışmasını sağlayacağız. Sihir gibi!

---

## ✅ Uygulanan Değişiklikler / Applied Changes

Bu eğitim sırasında aşağıdaki değişiklikler başarıyla uygulandı:

### 1. AppFixtures.php - Join Entity İlişkisi Oluşturuldu

```php
// src/DataFixtures/AppFixtures.php

use App\Factory\StarshipFactory;
use App\Factory\DroidFactory;
use App\Entity\Droid;
use App\Entity\StarshipDroid;  // ✅ Eklendi
use App\Entity\Starship;
use App\Model\StarshipStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ... diğer fixture kodları ...

        StarshipFactory::createMany(100, fn() => [
            //'droids' => DroidFactory::randomRange(1, 5), // ✅ Yoruma alındı
        ]);

        // ✅ Manuel join entity oluşturma
        $ship = StarshipFactory::random()->_real();
        $droid = DroidFactory::random()->_real();
        $starshipDroid = new StarshipDroid();
        $starshipDroid->setStarship($ship);
        $starshipDroid->setDroid($droid);
        $manager->persist($starshipDroid);

        $manager->flush();
    }
}
```

### 2. StarshipRepository.php - Query Sorunu Düzeltildi

```php
// src/Repository/StarshipRepository.php

// ... lines 1 - 14
class StarshipRepository extends ServiceEntityRepository
{
    // ... lines 17 - 24
    public function findIncompleteOrderedByDroidCount(): Pagerfanta
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.status != :status')
            ->setParameter('status', StarshipStatusEnum::COMPLETED)
            ->orderBy('COUNT(starshipDroid)', 'ASC')           // ✅ Güncellendi
            ->leftJoin('s.starshipDroids', 'starshipDroid')    // ✅ Güncellendi
            ->groupBy('s.id')
            ->getQuery();

        return new Pagerfanta(new QueryAdapter($query));
    }
    // ... lines 38 - 67
}
```

👉 Bu kodda, `s.droids` yerine `s.starshipDroids` kullanılarak join sorunu çözüldü.

### 3. Starship.php - getDroidNames() Metodu Eklendi

```php
// src/Entity/Starship.php

// ... lines 1 - 15
class Starship
{
    // ... lines 18 - 223

    // ✅ Problemli metod eklendi (sonraki derste düzeltilecek)
    public function getDroidNames(): string
    {
        return implode(', ', $this->droids->map(fn(Droid $droid) => $droid->getName())->toArray());
    }
    // ... lines 228 - 257
}
```

👉 Bu metod henüz eski `$droids` özelliğine referans veriyor ve hata üretiyor.

### 4. homepage.html.twig - Template Güncellemesi

```twig
{# templates/main/homepage.html.twig #}

{# ... diğer template kodları ... #}
<div class="text-slate-400 text-sm">
    Parts: {{ ship.parts|length }}
</div>
<div class="text-slate-400 text-sm">
    Droids: {{ ship.droidNames }}  {# ✅ Eklendi #}
</div>
{# ... #}
```

👉 Bu satır `ship.droidNames` metodunu çağırarak hatayı tetikliyor.

### 5. Komutlar ve Test Sonuçları

```shell
# Fixtures yükleme
symfony console doctrine:fixtures:load --no-interaction
# ✅ Başarılı: > loading App\DataFixtures\AppFixtures

# Veritabanı kontrolü
symfony console doctrine:query:sql "SELECT * FROM starship_droid"
# ✅ Sonuç:
# ---- ---------- ------------- ---------------------
#  id   droid_id   starship_id   assigned_at
# ---- ---------- ------------- ---------------------
#  2    664        442           2025-08-02 18:05:54
# ---- ---------- ------------- ---------------------
```

### 6. Beklenen Hatalar

Ana sayfaya gittiğinizde şu hatalar görülecek:

1. **Repository Hatası** (düzeltildi):

    ```
    [Semantical Error] line 0, col 55 near 'droids WHERE':
    Class App\Entity\Starship has no association named droids.
    ```

2. **Entity Hatası** (henüz mevcut):
    ```
    Warning: Undefined property: App\Entity\Starship::$droids.
    ```

### 7. Sonraki Adım: Join Entity'yi Gizleme

Bir sonraki derste, `getDroidNames()` metodunu düzelterek join entity'yi gizleyeceğiz ve eski `ManyToMany` ilişkisi gibi çalışmasını sağlayacağız.na güvenmek yerine, bir join entity olan `StarshipDroid` ekleyerek yeniden yapılandırdık. Şimdi `fixtures` dosyalarımızı tekrar yükleyelim, ama sıkı durun:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, veritabanındaki test verilerini tekrar yükler.

Hata!

Tanımsız özellik: `App\Entity\Starship::$droids`

Bu hata, `Starship` sınıfının 205. satırından geliyor. Suçlu? `getDroids()` metodu. Tabii ki, az önce `droids` özelliğini kaldırdık! Hızlı çözüm: Yorum satırına almak!

---

## ✏️ AppFixtures'te Droids Satırını Yoruma Almak / AppFixtures İçinde Droids Satırını Yoruma Almak

```php
// src/DataFixtures/AppFixtures.php

// ... lines 1 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 52
        StarshipFactory::createMany(100, fn() => [
            //'droids' => DroidFactory::randomRange(1, 5),
        ]);
// ... line 56
    }
}
```

👉 Bu kodda, `droids` ile ilgili satır yoruma alındı. Böylece hata alınmaz.

Ve işte! Fixtures tekrar çalışıyor:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, test verilerini tekrar yazar.

---

## 🏗️ Creating the Join Entity / Join Entity Oluşturmak

Doğru çözümü bulmak için bazı şeyleri elle yapalım: `$ship = StarshipFactory`, `createOne()` kullanabilirdik, ama rastgele bir tane seçelim. Ayrıca gerçek nesneyi almak için `_real()` yöntemini kullanalım. Aynısını `$droid = DroidFactory` için de yapalım, rastgele seçip `_real()` ile gerçek nesneye erişelim:

```php
// src/DataFixtures/AppFixtures.php

// ... lines 1 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 57
        $ship = StarshipFactory::random()->_real();
        $droid = DroidFactory::random()->_real();
// ... line 60
    }
}
```

👉 Bu kodda, rastgele bir `Starship` ve bir `Droid` seçiyoruz.

---

## 🔗 Relating via the Join Entity / Join Entity ile İlişkilendirme

Önceden bir `Droid`i bir `Starship`e eklemek için `$ship->addDroid($droid)` kullanabiliyorduk. Ama artık kullanamayız! Çünkü bu, kaldırılan `droids` özelliğine referans veriyor. Artık adı `starshipDroids` ve bir `StarshipDroid` nesnesi koleksiyonu.

`$ship->addDroid()` yerine, yeni bir `StarshipDroid` nesnesi oluşturuyoruz, sonra `$starshipDroid->setDroid($droid)` ve `$starshipDroid->setStarship($ship)` çağırıyoruz.

Elle oluşturduğumuz için, bunları kaydetmek için `$manager->persist($starshipDroid)` ve ardından `$manager->flush()` kullanıyoruz:

```php
// src/DataFixtures/AppFixtures.php

// ... lines 1 - 5
use App\Entity\StarshipDroid;
// ... lines 7 - 13
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 18 - 58
        $ship = StarshipFactory::random()->_real();
        $droid = DroidFactory::random()->_real();
        $starshipDroid = new StarshipDroid();
        $starshipDroid->setStarship($ship);
        $starshipDroid->setDroid($droid);
        $manager->persist($starshipDroid);
        $manager->flush();
    }
}
```

👉 Bu kodda, yeni bir `StarshipDroid` nesnesi oluşturup hem `Starship` hem de `Droid` ile ilişkilendiriyoruz ve kaydediyoruz.

---

Bu kesinlikle daha fazla iş, ama yeterince basit. Fixtures dosyasını tekrar çalıştırın:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, yeni ilişki ile test verilerini yazar.

Ve veritabanına bakmak için:

```shell
symfony console doctrine:query:sql "SELECT * FROM starship_droid"
```

👉 Bu komut, `starship_droid` join tablosundaki tüm kayıtları listeler.

Join tablosundan seçiyoruz ve evet! Bir Starship ve bir Droid için bir kayıt var. Şimdilik her şey yolunda. Ana sayfayı yenileyin. Başka bir hata!

```
[Semantical Error] line 0, col 55 near 'droids WHERE': Class App\Entity\Starship has no association named droids.
```

Görünüşe göre bir sorgu problemi var.

---

## 🛠️ Fixing the Query Issue / Sorgu Sorununu Düzeltmek

Şimdi kolları sıvayalım ve `src/Repository/StarshipRepository` dosyasına dalalım. Join’imiz biraz bozulmuş. `s.droids` üzerinde join yapıyoruz, ama `droids` özelliği artık yok. `s.starshipDroids` olarak değiştirin. Ve daha açıklayıcı olması için buna `starshipDroid` diyelim, çünkü aslında o. Şimdi mevcut olmayan `droids` yerine onları sayıyoruz:

```php
//src/Repository/StarshipRepository.php

// ... lines 1 - 14
class StarshipRepository extends ServiceEntityRepository
{
// ... lines 17 - 24
    public function findIncompleteOrderedByDroidCount(): Pagerfanta
    {
        $query = $this->createQueryBuilder('s')
// ... line 28
            ->orderBy('COUNT(starshipDroid)', 'ASC')
            ->leftJoin('s.starshipDroids', 'starshipDroid')
// ... lines 31 - 36
    }
// ... lines 38 - 67
}
```

👉 Bu kodda, sorguda `droids` yerine `starshipDroids` ile join yapıyoruz ve onları sayıyoruz.

---

Bunu hallettikten sonra ana sayfayı yeniliyoruz ve... başka bir hata!

```
Warning: Undefined property: App\Entity\Starship::$droids.
```

Bu, `ship.droidNames`'ten geliyor. Biliyoruz ki, `ship.droidNames` çağrıldığında `$starship->getDroidNames()` çağrılıyor ve hâlâ `droids` özelliğine referans veriyor:

```php
// src/Entity/Starship.php

// ... lines 1 - 15
class Starship
{
// ... lines 18 - 223
    public function getDroidNames(): string
    {
        return implode(', ', $this->droids->map(fn(Droid $droid) => $droid->getName())->toArray());
    }
// ... lines 228 - 257
}
```

👉 Bu kodda, eski `droids` özelliğine erişiliyor ve hata veriyor.

---

## 🎩 Hide that Join Entity / Join Entity'yi Gizlemek

Şimdi, join entity’yi gizleyecek ve bunun tam olarak eskiden sahip olduğumuz `ManyToMany` ilişkisi gibi çalışmasını sağlayacağız. Sihir gibi!
---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./20_Many-to-Many but with Extra Data.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./22_Hiding the Join Entity.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
