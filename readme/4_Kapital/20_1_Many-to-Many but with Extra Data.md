# 🔄 Many-to-Many but with Extra Data - Uygulama Adımları

Bu dokümanda, ManyToMany ilişkileri kaldırıp join entity'si oluşturma sürecinde gerçekleştirilen adımlar detaylandırılmıştır.

## 📋 Yapılan İşlemler Özeti

### Önceki Adımlar (19_1 dokümanından devam)

-   ✅ **Joining Across ManyToMany** - Droid sayısına göre sıralama
-   ✅ **Complex JOIN Queries** - LEFT JOIN ve COUNT aggregation
-   ✅ **MySQL Compatibility** - GROUP BY strict mode çözümleri

### Bu Bölümde Yapılan Adımlar

## 🎯 Hedef: ManyToMany İlişkisini Kaldırıp Join Entity Hazırlığı

**Tutorial'ın problemi:** ManyToMany ilişkiler otomatik join table oluşturur ama bu tabloya ekstra sütun (örn. `assignedAt`) ekleyemeyiz. Çözüm: Join table'ı kendi entity'miz haline getirmek.

**Tutorial direktifi:** "We'll stop using the many-to-many relationship entirely. Instead, we're going to generate a new entity that represents the join table. First, undo the many-to-many relationship (but only worry about the properties, not the methods)."

### Adım 1A: Starship Entity'sinden ManyToMany Property'sini Kaldırma ✅

**Tutorial talimatı:** "In Starship, wave goodbye to the droids property:"

**`src/Entity/Starship.php` dosyasında yapılan değişiklik:**

**Kaldırılan property:**

```php
/**
 * @var Collection<int, Droid>
 */
#[ORM\ManyToMany(targetEntity: Droid::class, inversedBy: 'starships')]
private Collection $droids;
```

**Constructor'dan kaldırılan satır:**

```php
$this->droids = new ArrayCollection();
```

👉 **Tutorial'a uygun şekilde sadece property kaldırıldı, metotlar bırakıldı (lint hatalar normal)**

### Adım 1B: Droid Entity'sinden ManyToMany Property'sini Kaldırma ✅

**Tutorial talimatı:** "And over in Droid, do the same for the starships many-to-many property:"

**`src/Entity/Droid.php` dosyasında yapılan değişiklikler:**

**Kaldırılan property:**

```php
/**
 * @var Collection<int, Starship>
 */
#[ORM\ManyToMany(targetEntity: Starship::class, mappedBy: 'droids')]
private Collection $starships;
```

**Constructor temizlendi:**

```php
public function __construct()
{
    // ArrayCollection başlatması kaldırıldı - "Clear out the constructor in both"
}
```

**Kullanılmayan import'lar kaldırıldı:**

```php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
```

👉 **Constructor "cleared out" edildi, tutorial'da belirtildiği gibi**

### Adım 1C: Schema Değişikliklerini Kontrol Etme ✅

**Tutorial komutu:**

```bash
symfony console doctrine:schema:update --dump-sql
```

**Tutorial beklentisi:** "This shows you what your migration would look like if you generated it right now. It's what we expect: no more starship_droid table."

**Sonuç:** ✅ starship_droid tablosunun DROP edileceği görüldü - tam tutorial'da beklenen sonuç

👉 **"It's what we expect: no more starship_droid table."**

## 💡 Bu Aşamanın Tutorial'a Uygunluğu

### Yapılan İşlemler Exactly Tutorial'a Göre:

1. **Property-Only Removal**: "but only worry about the properties, not the methods" ✅
2. **Constructor Clearing**: "Clear out the constructor in both" ✅
3. **Schema Verification**: doctrine:schema:update --dump-sql ile kontrol ✅
4. **Expected Result**: "no more starship_droid table" ✅

### Beklenen Lint Hatalar Normal:

-   `$droids` property referansları metotlarda hata verir - bu bekleniyor
-   `$starships` property referansları metotlarda hata verir - bu bekleniyor
-   Tutorial metotları şimdilik bırakmamızı söylüyor

---

## 🧱 Creating a New Join Entity / Yeni Bir Birleştirme (Join) Entity'si Oluşturmak

**Tutorial direktifi:** "But don't generate that migration just yet! We do want the join table, but now we need to create an entity to represent it."

### Adım 2A: StarshipDroid Entity Oluşturma ✅

**Tutorial komutu:**

```bash
symfony console make:entity StarshipDroid
```

**Tutorial açıklaması:** "DroidAssignment might be a more fitting name, but StarshipDroid helps us visualize what we're doing: recreating the same exact database relationship via two ManyToOnes"

**Interaktif komut süreci:**

```bash
D:\symfony\starshop>php bin/console make:entity StarshipDroid
 created: src/Entity/StarshipDroid.php
 created: src/Repository/StarshipDroidRepository.php

 Entity generated! Now let's add some fields!
 You can always add more fields later manually or by re-running this command.

 New property name (press <return> to stop adding fields):
 > assignedAt

 Field type (enter ? to see all types) [datetime_immutable]:
 > datetime_immutable

 Can this field be null in the database (nullable) (yes/no) [no]:
 > no

 updated: src/Entity/StarshipDroid.php
```

👉 **StarshipDroid entity oluşturuldu - join table'ı temsil eden entity**

### Adım 2B: assignedAt Field Ekleme ✅

**Tutorial talimatı:** "Add assignedAt along with two more properties..."

**Eklenen field:**

-   **Tip:** `datetime_immutable`
-   **Nullable:** `no`
-   **Amaç:** Droid'in starship'e atanma zamanını takip etmek

**Constructor'da otomatik set:**

```php
public function __construct()
{
    $this->assignedAt = new \DateTimeImmutable();
}
```

👉 **assignedAt field ile ekstra veri tutma kabiliyeti eklendi**

### Adım 2C: Droid ManyToOne İlişkisi Oluşturma ✅

**Tutorial talimatı:** "These are going to be ManyToOne relationships, and they'll connect StarshipDroid to Starship and Droid."

**Droid ilişkisi interaktif süreci:**

```bash
 Add another property? Enter the property name (or press <return> to stop adding fields):
 > droid

 Field type (enter ? to see all types) [string]:
 > relation

 What class should this entity be related to?:
 > Droid

What type of relationship is this?
 ------------ ---------------------------------------------------------------------------
  Type         Description
 ------------ ---------------------------------------------------------------------------
  ManyToOne    Each StarshipDroid relates to (has) one Droid.
               Each Droid can relate to (can have) many StarshipDroid objects.

  OneToMany    Each StarshipDroid can relate to (can have) many Droid objects.
               Each Droid relates to (has) one StarshipDroid.

  ManyToMany   Each StarshipDroid can relate to (can have) many Droid objects.
               Each Droid can also relate to (can also have) many StarshipDroid objects.

  OneToOne     Each StarshipDroid relates to (has) exactly one Droid.
               Each Droid also relates to (has) exactly one StarshipDroid.
 ------------ ---------------------------------------------------------------------------

 Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
 > ManyToOne

 Is the StarshipDroid.droid property allowed to be null (nullable)? (yes/no) [yes]:
 > no

 Do you want to add a new property to Droid so that you can access/update StarshipDroid objects from it - e.g. $droid->getStarshipDroid
s()? (yes/no) [yes]:
 > yes

 A new property will also be added to the Droid class so that you can access the related StarshipDroid objects from it.

 New field name inside Droid [starshipDroids]:
 > starshipDroids

 Do you want to activate orphanRemoval on your relationship?
 A StarshipDroid is "orphaned" when it is removed from its related Droid.
 e.g. $droid->removeStarshipDroid($starshipDroid)

 NOTE: If a StarshipDroid may *change* from one Droid to another, answer "no".

 Do you want to automatically delete orphaned App\Entity\StarshipDroid objects (orphanRemoval)? (yes/no) [no]:
 > yes

 updated: src/Entity/StarshipDroid.php
 updated: src/Entity/Droid.php
```

### Adım 2D: Starship ManyToOne İlişkisi Oluşturma ✅

**Starship ilişkisi interaktif süreci:**

```bash
 Add another property? Enter the property name (or press <return> to stop adding fields):
 > starship

 Field type (enter ? to see all types) [string]:
 > relation

 What class should this entity be related to?:
 > Starship

What type of relationship is this?
 ------------ ------------------------------------------------------------------------------
  Type         Description
 ------------ ------------------------------------------------------------------------------
  ManyToOne    Each StarshipDroid relates to (has) one Starship.
               Each Starship can relate to (can have) many StarshipDroid objects.

  OneToMany    Each StarshipDroid can relate to (can have) many Starship objects.
               Each Starship relates to (has) one StarshipDroid.

  ManyToMany   Each StarshipDroid can relate to (can have) many Starship objects.
               Each Starship can also relate to (can also have) many StarshipDroid objects.

  OneToOne     Each StarshipDroid relates to (has) exactly one Starship.
               Each Starship also relates to (has) exactly one StarshipDroid.
 ------------ ------------------------------------------------------------------------------

 Relation type? [ManyToOne, OneToMany, ManyToMany, OneToOne]:
 > ManyToOne

 Is the StarshipDroid.starship property allowed to be null (nullable)? (yes/no) [yes]:
 > no

 Do you want to add a new property to Starship so that you can access/update StarshipDroid objects from it - e.g. $starship->getStarshi
pDroids()? (yes/no) [yes]:
 > yes

 A new property will also be added to the Starship class so that you can access the related StarshipDroid objects from it.

 New field name inside Starship [starshipDroids]:
 > starshipDroids

 Do you want to activate orphanRemoval on your relationship?
 A StarshipDroid is "orphaned" when it is removed from its related Starship.
 e.g. $starship->removeStarshipDroid($starshipDroid)

 NOTE: If a StarshipDroid may *change* from one Starship to another, answer "no".

 Do you want to automatically delete orphaned App\Entity\StarshipDroid objects (orphanRemoval)? (yes/no) [no]:
 > yes

 updated: src/Entity/StarshipDroid.php
 updated: src/Entity/Starship.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 >



  Success!


 Next: When you're ready, create a migration with php bin/console make:migration
```

### Adım 2E: Oluşturulan İlişki Yapısı ✅

**Oluşturulan ilişkiler:**

1. **StarshipDroid → Droid (ManyToOne)**

    ```php
    #[ORM\ManyToOne(inversedBy: 'starshipDroids')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Droid $droid = null;
    ```

2. **StarshipDroid → Starship (ManyToOne)**
    ```php
    #[ORM\ManyToOne(inversedBy: 'starshipDroids')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Starship $starship = null;
    ```

**Otomatik eklenen OneToMany ilişkileri:**

3. **Droid → StarshipDroid (OneToMany)**

    - Property: `starshipDroids`
    - orphanRemoval: `true`

4. **Starship → StarshipDroid (OneToMany)**
    - Property: `starshipDroids`
    - orphanRemoval: `true`

👉 **Bidirectional ManyToOne/OneToMany ilişkileri kuruldu**

## 💡 Join Entity Pattern'in Avantajları

### Eski ManyToMany vs Yeni Join Entity:

**Eski sistem:**

-   Otomatik starship_droid tablosu
-   Sadece foreign key'ler
-   Ekstra veri eklenemez
-   Entity kontrolü yok

**Yeni sistem:**

-   Kontrol edilen StarshipDroid entity
-   assignedAt ekstra field'ı
-   Gelecekte yeni field'lar eklenebilir
-   Tam ORM kontrolü

### Database Schema Comparison:

**ManyToMany (eski):**

```sql
starship_droid (
    starship_id,
    droid_id,
    PRIMARY KEY (starship_id, droid_id)
)
```

**Join Entity (yeni):**

```sql
starship_droid (
    id PRIMARY KEY,
    starship_id,
    droid_id,
    assigned_at,
    -- future fields possible
)
```

### Interactive Command Learning Points:

**Entity Generator Best Practices:**

-   `datetime_immutable` tercih edilir (immutable objects)
-   `nullable: false` ile veri bütünlüğü
-   Bidirectional relationships otomatik kurulur
-   orphanRemoval cascade silme işlemleri için

**ManyToOne Selection Logic:**

-   StarshipDroid → Droid: "Each StarshipDroid has one Droid"
-   StarshipDroid → Starship: "Each StarshipDroid has one Starship"
-   OneToMany geri ilişkileri otomatik eklenir

---

## ⏭️ Bir Sonraki Tutorial Aşaması

Tutorial'ın devamında:

-   Migration oluşturulacak ("The Migration that Does Nothing")
-   assigned_at için DEFAULT değer problemi çözülecek
-   Migration çalıştırılacak

**Tutorial beklentisi:** "Sadece foreign key kısıtlamalarını kaldırıyor, birincil anahtar ekliyor ve foreign key'i yeniden oluşturuyor"

### Eski Adım 1A: Starship Entity'sinden ManyToMany Property'sini Kaldırma ✅

**`src/Entity/Starship.php` dosyasında değişiklikler:**

**Kaldırılan property:**

```php
/**
 * @var Collection<int, Droid>
 */
#[ORM\ManyToMany(targetEntity: Droid::class, inversedBy: 'starships')]
private Collection $droids;
```

**Constructor'dan kaldırılan satır:**

```php
$this->droids = new ArrayCollection();
```

**Kaldırılan metotlar:**

-   `getDroids(): Collection`
-   `addDroid(Droid $droid): static`
-   `removeDroid(Droid $droid): static`
-   `getDroidNames(): string`

👉 **Starship entity'si artık droids ile doğrudan ilişkisi yok**

### Adım 2: Droid Entity'sinden ManyToMany İlişkisini Kaldırma ✅

**`src/Entity/Droid.php` dosyasında değişiklikler:**

**Kaldırılan property:**

```php
/**
 * @var Collection<int, Starship>
 */
#[ORM\ManyToMany(targetEntity: Starship::class, mappedBy: 'droids')]
private Collection $starships;
```

**Constructor'dan kaldırılan satır:**

```php
$this->starships = new ArrayCollection();
```

**Kaldırılan import'lar:**

```php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
```

**Kaldırılan metotlar:**

-   `getStarships(): Collection`
-   `addStarship(Starship $starship): static`
-   `removeStarship(Starship $starship): static`

👉 **Droid entity'si artık starships ile doğrudan ilişkisi yok**

### Adım 3: Repository'deki Droid Count Metodu Güncellemesi ✅

**`src/Repository/StarshipRepository.php` dosyasında güncelleme:**

**Eski kod (artık çalışmaz):**

```php
usort($starships, function($a, $b) {
    $aDroidCount = $a->getDroids()->count();
    $bDroidCount = $b->getDroids()->count();
    return $aDroidCount <=> $bDroidCount;
});
```

**Yeni kod (geçici çözüm):**

```php
public function findIncompleteOrderedByDroidCount(): Pagerfanta
{
    // Geçici çözüm: ManyToMany ilişki kaldırıldığı için basit filtreleme yapalım
    $query = $this->createQueryBuilder('s')
        ->where('s.status != :status')
        ->setParameter('status', StarshipStatusEnum::COMPLETED)
        ->getQuery();

    return new Pagerfanta(new QueryAdapter($query));
}
```

👉 **Droid count sıralaması geçici olarak devre dışı**

### Adım 4: Template'lerde Droid Referanslarını Kaldırma ✅

**`templates/main/homepage.html.twig` güncellemesi:**

**Kaldırılan satır:**

```twig
<div class="text-slate-400 text-sm">
    Droids: {{ ship.droidNames ?: 'none' }}
</div>
```

**`templates/starship/show.html.twig` güncellemesi:**

**Kaldırılan blok:**

```twig
<h4 class="text-xs text-slate-300 font-semibold mt-2 uppercase">
    Droids
</h4>
<p class="text-[22px] font-semibold">
    {% for droid in ship.droids %}
        {{ droid.name }}{% if not loop.last %}, {% endif %}
    {% else %}
        No droids on board (clean up your own mess)
    {% endfor %}
</p>
```

👉 **Template'lerde artık droid bilgisi gösterilmiyor (geçici)**

### Adım 5: Schema Değişikliklerini Kontrol Etme ✅

**Komut çalıştırılması:**

```bash
symfony console doctrine:schema:update --dump-sql
```

**Beklenen sonuç:**

-   `starship_droid` tablosunun DROP edilmesi
-   Foreign key constraint'lerin kaldırılması

👉 **Schema değişiklikleri join entity oluşturulmadan önce kontrol edildi**

## 🔗 ManyToMany Removal Impact

### Database Structure Changes:

**Kaldırılan ilişki:**

-   `starship_droid` join table (yakında yeniden oluşturulacak)
-   Automatic foreign key management
-   Cascade operations

**Entity Relationship Changes:**

-   Bidirectional ManyToMany → Hiç ilişki yok (geçici)
-   Collection management kaldırıldı
-   Synchronization metotları silindi

### Application Impact:

**Geçici olarak çalışmayan özellikler:**

-   Droid count sıralaması
-   Droid names gösterimi
-   Starship-Droid ilişki yönetimi
-   Template'lerde droid listeleme

## 📁 Güncellenen Dosyalar

1. **src/Entity/Starship.php**

    - `$droids` property silindi
    - Constructor'dan ArrayCollection kaldırıldı
    - Droid-related metotlar silindi

2. **src/Entity/Droid.php**

    - `$starships` property silindi
    - Collection import'ları kaldırıldı
    - Starship-related metotlar silindi

3. **src/Repository/StarshipRepository.php**

    - `findIncompleteOrderedByDroidCount()` basitleştirildi
    - ArrayAdapter import kaldırıldı

4. **templates/main/homepage.html.twig**

    - Droid count gösterimi kaldırıldı

5. **templates/starship/show.html.twig**
    - Droid listesi kaldırıldı

## 🧠 Anahtar Kavramlar

### ManyToMany Limitations:

-   **Fixed Schema**: Join table'a ekstra sütun eklenemez
-   **No Entity Representation**: Join table için entity yok
-   **Limited Metadata**: İlişki hakkında zaman/durum bilgisi tutulamaz

### Transition Strategy:

-   **Step-by-Step Removal**: Önce ilişkileri kaldır, sonra yenisini ekle
-   **Temporary Functionality Loss**: Geçici olarak bazı özellikler çalışmaz
-   **Clean Slate Approach**: Tamamen temiz başlangıç

### Database Migration Planning:

-   **Schema Preview**: Migration'dan önce SQL'i görüntüleme
-   **Data Preservation**: Mevcut veri korunmalı
-   **Constraint Management**: Foreign key'leri doğru sırayla güncelleme

## 🎯 Sıradaki Adımlar

### Join Entity Creation:

1. **StarshipDroid Entity**: Yeni join entity oluşturulacak
2. **assignedAt Field**: Ekstra veri alanı eklenecek
3. **ManyToOne Relations**: Her iki tarafa da OneToMany ilişkisi
4. **Migration Strategy**: Veri korunarak migration

### Relationship Restoration:

1. **New Entity Management**: Join entity üzerinden ilişki yönetimi
2. **Helper Methods**: Convenience metotları yeniden ekleme
3. **Template Integration**: Droid gösterimini geri getirme
4. **Repository Queries**: Yeni entity ile JOIN sorguları

## 💡 Bu Aşamanın Önemi

**ManyToMany'den Join Entity'ye geçiş:**

1. **Flexibility**: Ekstra veriler ekleyebilme kabiliyeti
2. **Control**: İlişki tablosunu tam kontrol
3. **Metadata**: İlişki hakkında zaman/durum bilgisi
4. **Future-Proof**: İleride yeni alanlar ekleyebilme

**Geçici kayıplar normal:** Bu yaklaşım, gelecekte çok daha esnek bir sistem kurmanın bedeli.

---

## ✨ The Finishing Touches / Son Dokunuşlar

**Tutorial direktifi:** "Let's add a final touch to StarshipDroid"

### Adım 3A: StarshipDroid Constructor'ı Otomatik assignedAt ile ✅

**Tutorial açıklaması:** "This assignedAt isn't really something we should have to worry about. Create a constructor and set it automatically:"

**Eklenen constructor:**

```php
// src/Entity/StarshipDroid.php
class StarshipDroid
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $assignedAt = null;

    #[ORM\ManyToOne(inversedBy: 'starshipDroids')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Droid $droid = null;

    #[ORM\ManyToOne(inversedBy: 'starshipDroids')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Starship $starship = null;

    public function __construct()
    {
        $this->assignedAt = new \DateTimeImmutable();
    }
}
```

👉 **assignedAt otomatik olarak entity oluşturulurken set ediliyor**

### Adım 3B: Join Entity Pattern'in Tamamlanması ✅

**Tutorial'ın büyük açıklaması:**

> "Hold up, because this is huge! We now have the exact same relationship in the database as before. But since we've taken control of the join entity, we can add new fields to it."

**Kazanılan avantajlar:**

1. **Same Database Relationship**: Önceki ManyToMany ile aynı veritabanı yapısı
2. **Full Control**: Join table'ın tam kontrolü bizde
3. **Extensible**: Yeni field'lar eklenebilir
4. **Entity Management**: ORM ile tam kontrol

**Tutorial'ın promise'i:**

> "Next, we'll see how to assign droids to Starships with this new entity setup. And eventually, we'll get fancy and hide this implementation detail entirely!"

👉 **Join Entity Pattern başarıyla implementasyon tamamlandı**

## 🔧 Post-Implementation Hata Giderimleri

### Hata 1: "Undefined array key 0" in StarshipRepository ✅

**Sorun:**

```php
public function findMyShip(): Starship
{
    return $this->findAll()[0]; // Array boşsa hata
}
```

**Çözüm:**

```php
public function findMyShip(): ?Starship
{
    $starships = $this->findAll();
    return $starships ? $starships[0] : null;
}
```

### Hata 2: "Impossible to access attribute 'slug' on null variable" ✅

**Sorun:** Template'de `myShip` null olabiliyordu ama kontrol edilmiyordu.

**Çözüm:**

```twig
{% if myShip %}
    <h3 class="tracking-tight text-[22px] font-semibold">
        <a class="hover:underline" href="{{ path('app_starship_show', {
            slug: myShip.slug
        }) }}">{{ myShip.name }}</a>
    </h3>
{% else %}
    <div class="text-center text-slate-400 mt-8">
        <p class="text-lg">No ship assigned</p>
        <p class="text-sm mt-2">Create a starship to see your status</p>
    </div>
{% endif %}
```

### Hata 3: "Warning: Undefined property: $droids" in Fixtures ✅

**Sorunlar:**

1. `Starship::getDroids()`, `addDroid()`, `removeDroid()` metotları hala mevcut
2. `Droid::getStarships()`, `addStarship()`, `removeStarship()` metotları hala mevcut
3. `AppFixtures` içinde `'droids' => DroidFactory::randomRange(1, 5)` kullanımı
4. `StarshipRepository::findIncompleteOrderedByDroidCount()` içinde `$a->getDroids()->count()`

**Çözümler:**

```php
// 1. Starship.php - eski droid metotları kaldırıldı ✅
// 2. Droid.php - eski starship metotları kaldırıldı ✅

// 3. AppFixtures.php güncellendi:
DroidFactory::createMany(100);
// TODO: StarshipDroid entity ile ilişkilendirme yapılacak
StarshipFactory::createMany(100);

// 4. StarshipRepository.php güncellendi:
$aDroidCount = $a->getStarshipDroids()->count();
$bDroidCount = $b->getStarshipDroids()->count();
```

### Hata 4: "Neither the property 'droidNames' nor one of the methods exist" ✅

**Sorun:** Template'lerde hala eski droid referansları bulunuyordu.

**Çözümler:**

```twig
<!-- templates/main/homepage.html.twig - kaldırılan satır: -->
<div class="text-slate-400 text-sm">
    Droids: {{ ship.droidNames ?: 'none' }}
</div>

<!-- templates/starship/show.html.twig - kaldırılan blok: -->
<h4 class="text-xs text-slate-300 font-semibold mt-2 uppercase">
    Droids
</h4>
<p class="text-[22px] font-semibold">
    {% for droid in ship.droids %}
        {{ droid.name }}{% if not loop.last %}, {% endif %}
    {% else %}
        No droids on board (clean up your own mess)
    {% endfor %}
</p>
```

### Template Güvenliği: Null Control Pattern

**Best Practice:** Her entity reference öncesi null kontrolü yapılması

```twig
{% if entity %}
    {{ entity.property }}
{% else %}
    <!-- fallback content -->
{% endif %}
```

## 🎯 Bu Aşamayla Kazanılanlar

### Database Evolution:

**From (ManyToMany):**

```sql
starship_droid (
    starship_id,  -- FK to starship
    droid_id,     -- FK to droid
    PRIMARY KEY (starship_id, droid_id)
)
```

**To (Join Entity):**

```sql
starship_droid (
    id,           -- Auto-increment PK
    starship_id,  -- FK to starship
    droid_id,     -- FK to droid
    assigned_at,  -- DateTime when assigned
    -- future: more fields possible
)
```

### Code Control Evolution:

**Before (Automatic):**

-   Doctrine otomatik join table yönetimi
-   Sadece ID mapping
-   Ekstra veri imkansız
-   Entity kontrolü yok

**After (Manual Control):**

-   StarshipDroid entity ile tam kontrol
-   assignedAt metadata
-   Gelecekte yeni field'lar eklenebilir
-   Business logic eklenebilir

### Benefits Achieved:

1. **Metadata Support**: `assignedAt` ile zaman bilgisi
2. **Future Extensibility**: Yeni field'lar için hazır
3. **Business Logic**: Entity'de custom metotlar
4. **Full ORM Integration**: Query, Repository, Relations

## 💡 Pattern Learning Points

### Join Entity vs ManyToMany:

**When to use ManyToMany:**

-   Basit ID mapping yeterli
-   Ekstra veri gerekmez
-   Hızlı implementation

**When to use Join Entity:**

-   Ekstra metadata gerekli
-   İlişki hakkında bilgi tutulacak
-   Gelecekte genişleme planı var
-   Business rules ilişki üzerinde

### Constructor Pattern:

**Automatic Field Setting:**

```php
public function __construct()
{
    $this->assignedAt = new \DateTimeImmutable();
    // Future: $this->status = AssignmentStatus::ACTIVE;
    // Future: $this->assignedBy = $currentUser;
}
```

**Benefits:**

-   Consistent data
-   No null assigned_at values
-   Automatic timestamp tracking
-   Developer-friendly API

---

## ⏭️ Bir Sonraki Tutorial Bölümü

**Tutorial'ın devamında:**

> "Next, we'll see how to assign droids to Starships with this new entity setup. And eventually, we'll get fancy and hide this implementation detail entirely!"

**Beklenen konular:**

-   StarshipDroid entity ile droid assignment
-   Helper methods oluşturma
-   Implementation detail'leri gizleme
-   User-friendly API tasarımı

### Eski Adım: Schema Değişikliklerini Kontrol Etme ✅

---

> **Not:** Bu adımlar 2 Ağustos 2025 tarihinde Symfony 7 - Doctrine Relations projesinde gerçekleştirilmiştir.
