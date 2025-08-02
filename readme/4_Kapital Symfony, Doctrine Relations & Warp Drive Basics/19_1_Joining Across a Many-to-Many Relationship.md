# 🤖 Joining Across a Many-to-Many Relationship - Uygulama Adımları

Bu dokümanda, ManyToMany ilişkiler üzerinden JOIN işlemleri yaparak, starship'leri sahip oldukları droid sayısına göre sıralama sürecinde gerçekleştirilen adımlar detaylandırılmıştır.

## 📋 Yapılan İşlemler Özeti

### Önceki Adımlar (18_1 dokümanından devam)

-   ✅ **Foundry ile ManyToMany** - Otomatik droid-starship ilişkileri
-   ✅ **Closure Pattern** - Gerçek rastgele dağılım
-   ✅ **Factory Scalability** - 100'lerce entity oluşturma

### Bu Bölümde Yapılan Adımlar

## 🎯 Hedef: Starship'leri Droid Sayısına Göre Sıralamak

**Tutorial'ın sorusu:** Filo içindeki hangi yıldız gemisinin en fazla `droid` ile dolu olduğunu hiç merak ettin mi? Ben de! Haydi, her bir gemiyi sahip oldukları `droid` sayısına göre artan şekilde listeleyelim.

### Adım 1: StarshipRepository'de Yeni Metod Oluşturma ✅

`src/Controller/MainController.php` dosyasında sorgu: `$ships = $repository->findIncomplete();` vardı.

**O metoda tıkladık ve ona yeni, havalı bir isim verdik: `findIncompleteOrderedByDroidCount()`**

**`src/Repository/StarshipRepository.php` dosyasında yeni metod eklendi:**

```php
// src/Repository/StarshipRepository.php
// ... lines 1 - 14
class StarshipRepository extends ServiceEntityRepository
{
// ... lines 17 - 24
    public function findIncompleteOrderedByDroidCount(): Pagerfanta
    {
        $query = $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.status != :status')
            ->leftJoin('s.droids', 'droid')
            ->groupBy('s.id')
            ->orderBy('COUNT(droid.id)', 'ASC')
            ->setParameter('status', StarshipStatusEnum::COMPLETED)
            ->getQuery();
        return new Pagerfanta(new QueryAdapter($query));
    }
// ... lines 36 - 65
}
```

👉 **Bu, `findIncompleteOrderedByDroidCount()` adlı yeni metodu ekledik.**

### Adım 2: MainController'da Metod Çağrısını Güncelleme ✅

**Bunu yaptıktan sonra, kontrolcüye geri döndük ve eski metodu yenisiyle değiştirdik:**

**`src/Controller/MainController.php` dosyasında güncelleme:**

```php
// src/Controller/MainController.php
// ... lines 1 - 10
class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(
        StarshipRepository $repository,
        Request $request
    ): Response {
        $ships = $repository->findIncompleteOrderedByDroidCount();
        $ships->setMaxPerPage(5);
        $ships->setCurrentPage($request->query->get('page', 1));
// ... lines 19 - 27
    }
}
```

👉 **Bu kodda, artık yeni metodu çağırıyoruz.**

### Adım 3: ManyToMany JOIN Query Implementation ✅

## 🔗 ManyToMany JOIN Sihri

**Şu ana kadar henüz bir değişiklik yapmadık, bu yüzden sayfayı yenilediğinde aynı şeyleri görürsün.**

`starship`leri `droid` sayılarına göre sıralamak için, birleştirme (join) işlemiyle önce ilişki tablosuna, ardından da `droid` tablosuna ulaşmamız gerekiyor. **Kulağa karmaşık gelse de aslında gayet güzel!**

### JOIN Query Anatomy:

**`StarshipRepository` içinde JOIN işlemleri:**

1. **`leftJoin('s.droids', 'droid')`** - İlişki tablosunu ve droid tablosunu birleştir
2. **`groupBy('s.id')`** - Her starship için gruplama yap
3. **`orderBy('COUNT(droid)', 'ASC')`** - Droid sayısına göre artan sıralama

```php
$query = $this->createQueryBuilder('s')
    ->select('s')                       // Explicit SELECT for GROUP BY compatibility
    ->where('s.status != :status')
    ->leftJoin('s.droids', 'droid')     // ManyToMany ilişki ile JOIN
    ->groupBy('s.id')                   // Her starship için gruplama
    ->orderBy('COUNT(droid.id)', 'ASC') // Droid sayısına göre sıralama
    ->setParameter('status', StarshipStatusEnum::COMPLETED)
    ->getQuery();
```

👉 **Burada, `droid`lerle birleştirip (`leftJoin`), gruplayıp (`groupBy`) ve sayıya göre sıralıyoruz (`orderBy('COUNT(droid.id)', 'ASC')`).**

#### 🔧 MySQL GROUP BY Fix

**Önemli Not:** MySQL'in strict mode'unda `GROUP BY` kullanırken:

-   `select('s')` explicit olarak eklendi (GROUP BY compatibility için)
-   `COUNT(droid.id)` kullanıldı (`COUNT(droid)` yerine)
-   Bu değişiklikler MySQL `sql_mode=only_full_group_by` hatalarını önler

## 🔍 JOIN Query Detayları

### Doctrine'in Büyüsü:

**İlişki tablosunu ya da veritabanını düşünmene gerek yok. Sadece Doctrine içindeki ilişkilere odaklan:**

-   **`s`** - starship alias'ı
-   **`droids`** - ManyToMany ilişkisi olan property
-   **`droid`** - droids için verdiğimiz takma ad (alias)

### SQL Arka Plan:

**Doctrine otomatik olarak şu JOIN'leri oluşturuyor:**

1. `starship` tablosundan başla
2. `starship_droid` join tablosuna LEFT JOIN
3. `droid` tablosuna LEFT JOIN
4. `COUNT(droid.id)` ile droid sayısını hesapla
5. `GROUP BY starship.id` ile her starship için grupla

## 🎯 Sonuçlar ve Test

### Adım 4: Sonuçları Test Etme ✅

**Bundan sonra sayfayı yenile ve işte!**

**Beklenen Sonuçlar:**

-   **En üstte:** `droid` olmayan gemiler göreceksin
-   **Aşağıya indikçe:** `droid` sayısı artar
-   **İleride birkaç sayfa:** İki, üç, hatta dört `droid`i olan `starship`ler bile göreceksin!

### Sıralama Mantığı:

```
🚀 USS Ship1 (0 droids)  ← En üstte
🚀 USS Ship2 (1 droid)
🚀 USS Ship3 (2 droids)
🚀 USS Ship4 (3 droids)
🚀 USS Ship5 (4 droids)  ← En altta
```

## 📁 Güncellenen Dosyalar

1. **src/Repository/StarshipRepository.php**

    - `findIncomplete()` → `findIncompleteOrderedByDroidCount()` olarak yeniden adlandırıldı
    - `leftJoin('s.droids', 'droid')` eklendi
    - `groupBy('s.id')` eklendi
    - `orderBy('COUNT(droid)', 'ASC')` ile droid sayısına göre sıralama

2. **src/Controller/MainController.php**
    - `findIncomplete()` → `findIncompleteOrderedByDroidCount()` metod çağrısı güncellendi

## 🧠 Anahtar Kavramlar

### ManyToMany JOIN Patterns:

-   **Entity-Based JOIN**: Veritabanı tablolarını değil, entity ilişkilerini kullan
-   **LEFT JOIN**: Droid'i olmayan starship'ler de dahil edilsin
-   **GROUP BY**: Her starship için ayrı satır
-   **COUNT Aggregation**: İlişkili entity'leri sayma

### Doctrine Query Builder Magic:

-   **Automatic JOIN Table Handling**: starship_droid tablosu otomatik
-   **Property-Based Navigation**: `s.droids` ile ilişki navigasyonu
-   **Alias Management**: `droid` alias'ı ile clean query yazımı
-   **SQL Generation**: Complex JOIN'ler otomatik oluşturuluyor

### Performance Considerations:

-   **Efficient Aggregation**: COUNT() database seviyesinde
-   **Proper Grouping**: Duplicate starship'ler yok
-   **Index Usage**: Foreign key'ler ile optimize edilmiş

## 🎯 Final Durum

### Ana Sayfa Sıralaması:

-   **Droid sayısına göre ASC sıralama** active
-   **En az droid'li starship'ler üstte**
-   **En çok droid'li starship'ler altta**
-   **Pagination çalışmaya devam ediyor**

### Database Query Optimization:

-   **Tek sorgu ile sonuç**: N+1 problem yok
-   **JOIN efficiency**: İlişki tablosu optimal kullanım
-   **COUNT aggregation**: Database seviyesinde hesaplama

## 🚀 Öğrenilen Teknikler

### Advanced Repository Patterns:

1. **Method Naming**: Descriptive repository method names
2. **Query Composition**: Complex JOIN + GROUP BY + ORDER BY
3. **Alias Management**: Clean query builder usage
4. **Aggregation Functions**: COUNT, SUM, AVG kullanımı

### ManyToMany Best Practices:

1. **Entity-First Approach**: Database tablolarını görmezden gel
2. **Property Navigation**: `entity.relationship` pattern
3. **LEFT JOIN Usage**: Optional relationships için
4. **Grouping Strategy**: Proper result aggregation

## 💡 Doctrine'in Gücü

**Buradaki anahtar nokta şu:** Bu birleştirmenin özel bir yanı yok. İlişki üzerinden birleştiriyoruz, gerisini Doctrine hallediyor.

**Sayfadaki sorguya bakarsan**, tüm detayları nasıl ele aldığını görebilirsin:

-   Sorguda `starship_droid` ifadesini arayabilirsin
-   Bu kısım karmaşık görünebilir, ama sorguyu formatlarsan aslında:
    1. `starship`ten başlıyor
    2. İlişki tablosuna geçiyor
    3. Sonra da tekrar `droid` tablosuna geçiyor
-   Böylece `droid` tablosundaki sayıya göre sıralama yapılabiliyor

**Doctrine gerçekten etkileyici!** 🎉

---

## ⏭️ Sıradaki Adımlar

**Tutorial'da belirtildiği gibi:**

> "Teknik olarak `ManyToMany` kısmı bu kadar! Ama sırada daha gelişmiş, ama yaygın bir kullanım var: ilişki (join) tablosuna veri eklemek, örneğin bir `droid`in bir `starship`e katıldığı tarih gibi."

-   **Join Table Data**: İlişki tablosuna ekstra veriler ekleme
-   **Temporal Relationships**: Tarih/zaman bilgileri ile ilişkiler
-   **Advanced ManyToMany**: İlişki tablosunu entity olarak modelleme

---

> **Not:** Bu adımlar 2 Ağustos 2025 tarihinde Symfony 7 - Doctrine Relations projesinde gerçekleştirilmiştir.
