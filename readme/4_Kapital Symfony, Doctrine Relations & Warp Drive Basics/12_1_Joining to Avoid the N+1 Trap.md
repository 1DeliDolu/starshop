# 🏗️ Joining to Avoid the N+1 Trap / N+1 Tuzağından Kaçınmak için Join Kullanmak

Bir `parts` tablomuz var ve artık onu kullanıyoruz! Ama şimdi parçaları, fiyatına göre azalan şekilde sıralamak istiyoruz; çünkü satış yapacaksak, en pahalı olanlardan başlamak iyi olur, değil mi? Bu basit bir işlem, ama bunu biraz daha heyecanlı hale getirmek için özel bir sorgu oluşturacağız. `src/Repository/StarshipPartRepository.php` dosyasını açın.

O hazırdaki method gövdesini görüyor musunuz? Onu kopyalayın, ardından yorumu kaldırın; çünkü bu PHP dokümantasyonu faydalı ve kaybetmek istemeyiz. Son stubu silin ve adını `findAllOrderedByPrice()` yapın. `$value` parametresini kaldırın, buna ihtiyacımız yok:

```php
// src/Repository/StarshipPartRepository.php
// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 40
    /**
     * @return StarshipPart[] Returns an array of StarshipPart objects ordered by price descending
     */
    public function findAllOrderedByPrice(): array
    {
        // ... method body will go here
    }
}
```

👉 Bu kod, yeni bir `findAllOrderedByPrice` fonksiyonunun iskeletini oluşturur.

## 🔨 Building the Basic Query / Temel Sorguyu Oluşturmak

Basit bir sorgu oluşturun: `StarshipPart` için takma ad olarak `sp` kullanacağım. Aşağıdaki `andWhere()` ve `setParameter()` fonksiyonlarını kaldırın. Ama `orderBy()` fonksiyonuna ihtiyacımız var: `orderBy('sp.price', 'DESC')` olarak. `setMaxResults()` da kaldırılabilir:

```php
// src/Repository/StarshipPartRepository.php
// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 40
    public function findAllOrderedByPrice(): array
    {
        return $this->createQueryBuilder('sp')
            ->orderBy('sp.price', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
```

👉 Bu kod, parçaları fiyatına göre azalan şekilde getirir.

Özel sorgu hazır! Method adını kopyalayın, sonra `PartController` dosyasına gidin. Bunu, `findAll()` yerine kullanın:

```php
// src/Controller/PartController.php
// ... lines 1 - 9
final class PartController extends AbstractController
{
    #[Route('/parts', name: 'app_part_index')]
    public function index(StarshipPartRepository $repository): Response
    {
        $parts = $repository->findAllOrderedByPrice();

        return $this->render('part/index.html.twig', [
            'parts' => $parts,
        ]);
    }
}
```

👉 Bu kod, kontrolcüde parçaları fiyatına göre sıralı şekilde çeker.

## 🔎 Examining Our Queries / Sorgularımızı İncelemek

Bu sayfa için sorguları kontrol edin: 9 tane sorgu var. İlki tam tahmin ettiğimiz gibi: tüm `starship_parts` verilerini fiyatına göre azalan şekilde sorguluyor. Ama diğer bu ek sorgular ne? Her bir yıldız gemisi için ekstra bir sorgu var. Neler oluyor?

**Sorgu Analizi:**

-   1. Ana sorgu: `SELECT * FROM starship_part ORDER BY price DESC`
-   2-9. Ek sorgular: Her parça için `SELECT * FROM starship WHERE id = ?`

Bu durum, önemli bir performans problemini işaret ediyor.

## 🕵️‍♂️ The N + 1 Problem / N + 1 Problemi

Tüm parçaları sorguluyoruz, sonra şablonda parçaların üzerinde döngü kurarken `part.starship` kullandığımızda, Doctrine'nin aklına bir fikir geliyor. Parça verisine sahip ama bu parçaya ait `Starship` verisine sahip değil. Onu sorguluyor. Sonuç olarak bir sorgu parça için, her bir `Starship` için de ekstra sorgu oluşuyor. Bu, kötü ünlü **N + 1 problemi**.

**Problem Açıklaması:**

-   N = Parça sayısı (örn: 8 parça)
-   1 = Ana sorgu (parçaları getir)
-   Total = 1 + N = 9 sorgu

Şöyle düşünün: 10 parçamız varsa, parçalar için bir sorgu, her bir parça için de `Starship` verisini almak için toplamda 10 ek sorgu yapıyoruz. Bu bir performans sorunudur. Belki şu an önemli gözükmeyebilir, ama dikkat etmemiz gereken bir durum. Ve bunu bir `join` ile çözebiliriz.

**N+1 Probleminin Etkileri:**

-   Yavaş sayfa yükleme
-   Veritabanı sunucusuna fazla yük
-   Ağ trafiğinin artması
-   Kötü kullanıcı deneyimi

## 🔗 Joining Across the Relationship / İlişki Üzerinden Join Yapmak

Tekrar `StarshipPartRepository` dosyasına dönelim ve `findAllOrderedByPrice()` fonksiyonunu bir join ile güçlendirelim. `innerJoin('sp.starship', 's')` ekleyin. Tek yapmamız gereken, property üzerinden join yapmak. Doctrine hangi kolonların join yapılacağını otomatik belirler. Böylece `starship` tablosunu `s` takma adıyla bağlamış oluyoruz:

```php
// src/Repository/StarshipPartRepository.php
// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 40
    public function findAllOrderedByPrice(): array
    {
        return $this->createQueryBuilder('sp')
            ->innerJoin('sp.starship', 's')
            ->orderBy('sp.price', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
```

👉 Bu kod, parçalara ait yıldız gemilerini de join ile dahil eder.

**Join Türleri:**

-   `innerJoin()`: Sadece ilişkili kayıtlar
-   `leftJoin()`: Sol tablodaki tüm kayıtlar + eşleşenler

Daha önce 9 veritabanı sorgumuz vardı. Sayfayı yenileyin ve... hala 9 sorgu var. Neden? Zaten `starship` tablosuna join eklemedik mi?

Evet, ama join kullanmanın **iki sebebi** var:

1. **N + 1 sorununu önlemek** (bizim şu anki hedefimiz)
2. **Join yapılan tabloda `where()` veya `orderBy()` uygulamak** (birazdan göreceğiz)

## ➕ addSelect ile N+1 Çözümü / addSelect ile N+1 Problemine Çözüm

N+1 sorununu çözmek için, join'e ek olarak, `Starship` verisini de seçmemiz gerekir. Bunun için tek yapmamız gereken `addSelect('s')` eklemek:

```php
// src/Repository/StarshipPartRepository.php
// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 40
    public function findAllOrderedByPrice(): array
    {
        return $this->createQueryBuilder('sp')
            ->innerJoin('sp.starship', 's')
            ->addSelect('s')
            ->orderBy('sp.price', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
```

👉 Bu kod, hem parçaları hem de ilişkili yıldız gemisi verisini tek sorguda getirir.

`Starship` tablosunun tamamını `s` takma adıyla seçiyoruz. `addSelect()` ile tek tek kolonlarla uğraşmıyoruz. Sadece şöyle diyoruz:

**"Hey, tüm verileri istiyorum!"**

## ✨ The Magic of join and addSelect() / join ve addSelect() Büyüsü

Artık **9 sorgudan 1 sorguya** düştük. Gerçekten sihirli bir çözüm!

**Oluşturulan SQL:**

```sql
SELECT sp.*, s.*
FROM starship_part sp
INNER JOIN starship s ON sp.starship_id = s.id
ORDER BY sp.price DESC
```

Gördüğünüz gibi, `StarshipPart` tablosundan seçiyoruz, hem `Starship` hem de `StarshipPart` verilerini alıyoruz ve ortada güzel bir `innerJoin()` var. En güzel yanı ise, hangi kolonların birleştirileceğiyle uğraşmak zorunda olmamamız. Sadece ilişki property'sini belirtiyoruz, gerisini Doctrine hallediyor.

## 🔧 Understanding the Join Process / Join Sürecini Anlamak

**JOIN Olmadan:**

1. `SELECT * FROM starship_part ORDER BY price DESC` (8 parça)
2. `SELECT * FROM starship WHERE id = 1` (1. parça için)
3. `SELECT * FROM starship WHERE id = 2` (2. parça için)
4. ... (her parça için tekrar)

**JOIN İle:**

1. `SELECT sp.*, s.* FROM starship_part sp INNER JOIN starship s ON sp.starship_id = s.id ORDER BY sp.price DESC`

**Sonuç:** 9 sorgu → 1 sorgu = %89 performans artışı!

## 🚀 Performance Benefits / Performans Avantajları

| Metric                 | JOIN Olmadan | JOIN İle    | İyileşme        |
| ---------------------- | ------------ | ----------- | --------------- |
| Sorgu Sayısı           | 9            | 1           | 89% azalma      |
| Veritabanı Round-trips | 9            | 1           | 89% azalma      |
| Network Latency        | 9x           | 1x          | 89% azalma      |
| Memory Usage           | Normal       | Biraz fazla | Makul trade-off |

## 🎯 Best Practices / En İyi Uygulamalar

### 1. **Lazy Loading vs Eager Loading**

```php
// ❌ Lazy Loading (N+1 Problem)
$parts = $repository->findAll();
foreach ($parts as $part) {
    echo $part->getStarship()->getName(); // Her döngüde yeni sorgu
}

// ✅ Eager Loading (JOIN ile)
$parts = $repository->findAllOrderedByPrice();
foreach ($parts as $part) {
    echo $part->getStarship()->getName(); // Ek sorgu yok
}
```

### 2. **JOIN vs addSelect Kombinasyonu**

```php
// ❌ Sadece JOIN (hala N+1 problem var)
->innerJoin('sp.starship', 's')

// ✅ JOIN + addSelect (problem çözüldü)
->innerJoin('sp.starship', 's')
->addSelect('s')
```

### 3. **Memory Considerations**

-   JOIN kullanımı bellek kullanımını artırır
-   Büyük veri setlerinde dikkatli olun
-   Gerektiğinde pagination kullanın

## 🔍 Debugging Queries / Sorguları Debug Etmek

**Symfony Profiler'da Sorguları İncelemek:**

1. Sayfayı yenileyin
2. Alt taraftaki Symfony toolbar'da Database ikonuna tıklayın
3. Query sayısını ve detaylarını inceleyin
4. Duplicate queries'i kontrol edin

**Doctrine Query Logger ile:**

```php
// Repository'de debug için
public function findAllOrderedByPrice(): array
{
    $query = $this->createQueryBuilder('sp')
        ->innerJoin('sp.starship', 's')
        ->addSelect('s')
        ->orderBy('sp.price', 'DESC')
        ->getQuery();

    // SQL'i görmek için
    dump($query->getSQL());

    return $query->getResult();
}
```

## 🧪 Testing the Performance / Performansı Test Etmek

**Önce-Sonra Karşılaştırması:**

```php
// Öncesi: findAll() kullanımı
$start = microtime(true);
$parts = $repository->findAll();
$queryTime = microtime(true) - $start;
echo "Query time: " . $queryTime . " seconds, Queries: 9";

// Sonrası: findAllOrderedByPrice() kullanımı
$start = microtime(true);
$parts = $repository->findAllOrderedByPrice();
$queryTime = microtime(true) - $start;
echo "Query time: " . $queryTime . " seconds, Queries: 1";
```

## 🔮 Advanced JOIN Techniques / Gelişmiş JOIN Teknikleri

### 1. **Conditional Joins**

```php
public function findPartsWithActiveShips(): array
{
    return $this->createQueryBuilder('sp')
        ->innerJoin('sp.starship', 's')
        ->addSelect('s')
        ->andWhere('s.status = :status')
        ->setParameter('status', 'ACTIVE')
        ->getQuery()
        ->getResult();
}
```

### 2. **Multiple Level Joins**

```php
public function findPartsWithShipCaptains(): array
{
    return $this->createQueryBuilder('sp')
        ->innerJoin('sp.starship', 's')
        ->leftJoin('s.captain', 'c')
        ->addSelect('s', 'c')
        ->getQuery()
        ->getResult();
}
```

### 3. **Partial Object Loading**

```php
public function findPartsWithPartialShipData(): array
{
    return $this->createQueryBuilder('sp')
        ->innerJoin('sp.starship', 's')
        ->addSelect('partial s.{id, name, class}') // Sadece belirli alanlar
        ->getQuery()
        ->getResult();
}
```

## 🔗 Navigation Links / Navigasyon Bağlantıları

⬅️ **Önceki:** [11_1_Listing Parts.md](./11_1_Listing%20Parts.md) - Parçaları Listeleme

➡️ **Sonraki:** 13_Search Functionality with JOIN.md - JOIN ile Arama Fonksiyonalitesi

📚 **Ana Menü:** [README.md](../README.md) - Symfony Starshop Eğitim Serileri

## 🎉 Sonuç

Tebrikler! N+1 problemini başarıyla çözdük. Bu bölümde öğrendiklerimiz:

-   **N+1 Probleminin Tanımı**: Bir ana sorgu + her ilişkili kayıt için ek sorgu
-   **JOIN Kullanımı**: `innerJoin()` ile tabloları birleştirmek
-   **addSelect() Önemi**: İlişkili verileri tek sorguda çekmek
-   **Performance Optimization**: 9 sorgudan 1 sorguya düşürmek
-   **Debugging Techniques**: Sorguları analiz etme yöntemleri

Sırada sayfamıza arama eklemek var. O zaman `JOIN` kullanımının ikinci sebebini göreceğiz ve son olarak `Request` objesiyle de oynayacağız. 🚀

## 📋 Quick Reference / Hızlı Referans

**Basic JOIN:**

```php
->innerJoin('sp.starship', 's')
```

**Eager Loading:**

```php
->innerJoin('sp.starship', 's')
->addSelect('s')
```

**Performance Check:**

-   Symfony Profiler → Database bölümü
-   Query sayısını kontrol et
-   N+1 pattern'ini ara

**Common Mistakes:**

-   ❌ JOIN without addSelect
-   ❌ Forgetting to alias joined table
-   ❌ Using SELECT when WHERE would suffice
