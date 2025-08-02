# 🚦 Cascade Persist / Kademeli Persist

Bu hataya göz at: tam bir baş belası!

`Starship.droids` ilişkisi üzerinden bulunan bir varlık, `StarshipDroid` varlığı için `cascade persist` işlemleriyle yapılandırılmamış.

Bunu senin için çevireyim:

Hey, bu `Starship`'i kaydediyorsun ve ona bağlı bir `StarshipDroid` var. Harika, ama bana `StarshipDroid`'i de kaydetmemi söylemeyi unuttun. Ne yapmamı istersin?

Ama yine de, `Starship` içinden, entity yöneticisini çağırıp `$manager->persist($starshipDroid)` diyemeyiz.

## cascade=['persist'] Gücünden Yararlanmak

Çözüm, `cascade persist` denen bir şeyi kullanmaktır.

`$starshipDroids` özelliğine yukarı kaydır, ve `OneToMany`'yi bul. Yeni bir seçenek ekle: `cascade`. Bunu manuel olarak yazacağım. `persist` içeren bir dizi olarak ayarla:

```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 53
    #[ORM\OneToMany(targetEntity: StarshipDroid::class, mappedBy: 'starship', orphanRemoval: true, cascade: ['persist'])]
    private Collection $starshipDroids;
// ... lines 56 - 260
}
```

👉 Bu kodda, `cascade: ['persist']` ile ilişkiye eklenen tüm nesnelerin de otomatik olarak kaydedilmesi sağlanır.

Burada bir domino etkisi oluşturuyoruz. Eğer biri bu `starship`'i kaydederse, bu işlemi ilişkilere de kademeli olarak uygulayacağız.

Ama dikkatli ol: Bu gücü akıllıca kullan. Kodu daha otomatik yapar, bu harika, ama hata tespitini de zorlaştırabilir.

Ama bu durumda, tam ihtiyacımız olan çözüm bu.

Tekrar fixture'ları çalıştır:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, uygulamanın fixture verilerini yükler.

## 🛠️ Back to Adding Droids / Droid Eklemeye Geri Dönüş

Tekrar iş başındayız. Artık tekrar `ship->addDroid()` kullanabiliriz. Ama ben yine de droidleriyle birlikte bir filo `starship` oluşturmak istiyorum.

Tüm manuel kodu kaldır ve `StarshipFactory`'deki `droids` özelliğini geri getir:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 13
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 18 - 51
        DroidFactory::createMany(100);
        StarshipFactory::createMany(100, fn() => [
            'droids' => DroidFactory::randomRange(1, 5),
        ]);
        \App\Factory\StarshipPartFactory::createMany(100);

        $manager->flush();
    }
}
```

👉 Bu kod, her bir `Starship` için 1 ile 5 arası rastgele droid ilişkilendirir.

Yeniden fixture'ları çalıştır:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, yeni veri setini veritabanına tekrar yükler.

Tahmin et ne oldu? Çalıştı!

Arka planda Foundry, her bir droid için her bir `Starship` üzerinde `addDroid()` çağırıyor. Ve az önce `addDroid()` fonksiyonunun tekrar çalıştığını kanıtladık.

Artık `StarshipDroid` join entity'sinin oluşturulması tüm kod tabanımızdan gizlendi!

## 🔄 Adding removeDroid() Method / removeDroid() Metodunu Eklemek

Foundry'nin `droids` özelliğini kullanabilmesi için `removeDroid()` metodunu da eklemek gerekti:

```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 247
    public function removeDroid(Droid $droid): static
    {
        $starshipDroidsToRemove = $this->starshipDroids->filter(function(StarshipDroid $starshipDroid) use ($droid) {
            return $starshipDroid->getDroid() === $droid;
        });

        foreach ($starshipDroidsToRemove as $starshipDroid) {
            $this->removeStarshipDroid($starshipDroid);
        }

        return $this;
    }
// ... lines 260 - 270
}
```

👉 Bu metod, belirli bir droid'i starship'ten kaldırmak için gerekli StarshipDroid join entity'lerini bulur ve kaldırır.

## 🎛️ Finer Control with assignedAt / assignedAt ile Daha Hassas Kontrol

Ama, bir droid'i bir starship'e ekleyip `assignedAt` özelliğini kontrol etmek istersen ne olacak? `Starship` içinde `addDroid()` metoduna bir `DateTimeImmutable` argümanı ekle. Esnek olsun diye bunu isteğe bağlı yap. Sonra, `StarshipDroid`'i oluşturduktan sonra `$assignedAt` gönderildiyse ayarla:

```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 233
    public function addDroid(Droid $droid, ?\DateTimeImmutable $assignedAt = null): static
    {
        if (!$this->getDroids()->contains($droid)) {
            $starshipDroid = new StarshipDroid();
            $starshipDroid->setDroid($droid);
            $starshipDroid->setStarship($this);
            if ($assignedAt) {
                $starshipDroid->setAssignedAt($assignedAt);
            }
            $this->starshipDroids->add($starshipDroid);
            $droid->getStarshipDroids()->add($starshipDroid);
        }
        return $this;
    }
// ... lines 248 - 270
}
```

👉 Bu kod, droid atanırken bir tarih belirlemenize olanak tanır.

Güzel... ama ufak bir sorun var. Foundry, `assignedAt` alanını kontrol etmemize izin vermiyor. Yani bazı droidleri belirli bir zamanda atamak istersen, bunu manuel olarak yapmalısın.

## 👁️ Displaying assignedAt / assignedAt'ı Görüntüleme

Son olarak, `assignedAt`'ı sitemizde görünür yapalım. Bunun için `StarshipDroid` join entity nesnesine ihtiyacımız olacak. Biraz daha fazla iş, ama kesinlikle yapılabilir.

Döngüyü değiştir; `for starshipDroid in ship.starshipDroids` şeklinde yap. Sonra `starshipDroid.droid.name` ve `starshipDroid.assignedAt` ile, biraz süs için `ago` filtresiyle göster:

```twig
{# templates/starship/show.html.twig #}
{# ... lines 1 - 4 #}
{% block body %}
{# ... lines 6 - 19 #}
    <div class="md:flex justify-center space-x-3 mt-5 px-4 lg:px-8">
{# ... lines 21 - 25 #}
        <div class="space-y-5">
            <div class="mt-8 max-w-xl mx-auto">
                <div class="px-8 pt-8">
{# ... lines 29 - 45 #}
                    <h4 class="text-xs text-slate-300 font-semibold mt-2 uppercase">
                        Droids ({{ ship.starshipDroids|length }})
                    </h4>
                    <p class="text-[22px] font-semibold">
                        {% for starshipDroid in ship.starshipDroids %}
                            {{ starshipDroid.droid.name }} (assigned {{ starshipDroid.assignedAt|ago }}){% if not loop.last %}, {% endif %}
                        {% endfor %}
                    </p>
{# ... lines 54 - 60 #}
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```

👉 Bu Twig kodunda, her droid'in atanma zamanı ekranda gösterilir.

Sayfayı yenile ve... artık her bir droid'in ne zaman atandığını görebiliyoruz.

## 🎯 Yapılan Tüm Değişiklikler Özeti

### 1. Cascade Persist Eklendi (Önceki Bölümde)

```php
#[ORM\OneToMany(targetEntity: StarshipDroid::class, mappedBy: 'starship', orphanRemoval: true, cascade: ['persist'])]
private Collection $starshipDroids;
```

### 2. addDroid() Metodu Güncellendi

-   İsteğe bağlı `$assignedAt` parametresi eklendi
-   `assignedAt` kontrolü ve atama işlemi eklendi
-   **PHP 8.4 Uyumluluğu**: Nullable type açık olarak belirtildi

```php
// Eski (Deprecated):
public function addDroid(Droid $droid, \DateTimeImmutable $assignedAt = null): static

// Yeni (PHP 8.4 Uyumlu):
public function addDroid(Droid $droid, ?\DateTimeImmutable $assignedAt = null): static
```

### 3. removeDroid() Metodu Eklendi

-   Foundry'nin PropertyAccessor sistemi için gerekli
-   İlgili `StarshipDroid` entity'lerini bulup kaldırır

### 4. DataFixtures Güncellendi

```php
DroidFactory::createMany(100);
StarshipFactory::createMany(100, fn() => [
    'droids' => DroidFactory::randomRange(1, 5),
]);
```

### 5. Starship Show Template Güncellendi

-   Droid listesi ve atanma zamanları gösterildi
-   `starshipDroid.droid.name` ve `starshipDroid.assignedAt|ago` kullanıldı

### 6. Çalıştırılan Komutlar

```bash
# Fixture'ları yükleme
symfony console doctrine:fixtures:load --no-interaction
```

### 7. PHP 8.4 Uyumluluk Düzeltmesi

**Sorun**: `Deprecated: Implicitly marking parameter $assignedAt as nullable is deprecated`

**Çözüm**:

```php
// Önceki (deprecated):
\DateTimeImmutable $assignedAt = null

// Sonrası (uyumlu):
?\DateTimeImmutable $assignedAt = null
```

👉 PHP 8.4'te implicit nullable parametreler deprecated oldu, açık nullable type (`?`) kullanılması gerekiyor.

### 8. Doctrine Collection matching() Düzeltmesi

**Sorun**: `Call to unknown method: Doctrine\Common\Collections\Collection::matching()`

**Çözüm**:

```php
// Import eklendi:
use Doctrine\Common\Collections\Selectable;

// Property type güncellendi:
/**
 * @var Collection<int, StarshipPart> & Selectable<int, StarshipPart>
 */
private Collection $parts;

// Method return type güncellendi:
/**
 * @return Collection<int, StarshipPart> & Selectable<int, StarshipPart>
 */
public function getParts(): Collection
{
    return $this->parts;
}

// getExpensiveParts() metodu düzeltildi:
public function getExpensiveParts(): Collection
{
    $criteria = StarshipPartRepository::createExpensiveCriteria();
    return $this->parts->matching($criteria);
}
```

👉 `matching()` metodu sadece `Selectable` interface'ini implement eden koleksiyonlarda mevcut. `Collection & Selectable` intersection type kullanarak sorunu çözdük.

Artık Foundry'nin PropertyAccessor sistemi hem `addDroid()` hem de `removeDroid()` metodlarını bulabilir ve `droids` özelliğini sorunsuz kullanabilir!

Hepsi bu kadar! Doctrine ilişkilerinin en derin köşelerini, hatta ek alanları olan çoktan-çoğa ilişkileri bile keşfettik. Her zaman olduğu gibi, sorularınız varsa yorumlara yazabilirsiniz. Hep birlikteyiz!
