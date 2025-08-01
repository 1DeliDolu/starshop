# 🧠 The Clever Criteria System / Akıllı Kriter Sistemi

`$ship->getParts()` yöntemimiz var: bu yöntem, yıldız gemimiz için tüm parçaları döndürür. Ancak mali yılın sonuna yaklaşıyoruz ve bütçemizi planlamamız gerekiyor. Sıkıcı, ama gerekli: `Ferengi` patronlarımız bunu talep ediyor! Çoğu parça ucuz, yani her şeyi bir arada tutan somunlar, vidalar ve bantlar gibi şeyler. Bunlar için endişelenmiyoruz. Bunun yerine, gemimizin 50.000 krediden fazla maliyeti olan tüm parçalarını hızlıca döndürmek istiyorum.

Tabii ki, denetleyicimizde (controller) yıldız gemisiyle ilişkili, fiyatı 50.000'den fazla olan tüm parçalar için yeni bir sorgu yapabiliriz. Ama bunun neresi eğlenceli? Kolaylık sağlayan `$ship->getParts()` kısayolunu kullanmak istiyorum. Bu mümkün mü?

## 🏗️ Adding getExpensiveParts() / getExpensiveParts() Yöntemini Eklemek

`Starship` sınıfına geçin ve `getParts()` yöntemini bulun. Onu kopyalayın, aşağıya yapıştırın ve adını `getExpensiveParts()` olarak değiştirin. Şimdilik her şeyi döndürün:

```php
// src/Entity/Starship.php
// ... lines 1 - 16
class Starship
{
// ... lines 19 - 160
    /**
     * @return Collection<int, StarshipPart>
     */
    public function getParts(): Collection
    {
        return $this->parts;
    }

    /**
     * @return Collection<int, StarshipPart>
     */
    public function getExpensiveParts(): Collection
    {
        return $this->parts;
    }
// ... lines 175 - 192
}
```

👉 Şu anda, bu yöntem tüm parçaları döndürüyor.

`show` şablonumuza geri dönüp bunu deneyin. `parts`'ı `expensiveParts` ile değiştirin:

```twig
// templates/starship/show.html.twig
// ... lines 1 - 4
{% block body %}
// ... lines 6 - 19
    <div class="md:flex justify-center space-x-3 mt-5 px-4 lg:px-8">
// ... lines 21 - 25
        <div class="space-y-5">
            <div class="mt-8 max-w-xl mx-auto">
                <div class="px-8 pt-8">
// ... lines 29 - 35
                    <h4 class="text-xs text-slate-300 font-semibold mt-2 uppercase">
                        Expensive Parts ({{ ship.expensiveParts|length }})
                    </h4>
                    <ul class="text-sm font-medium text-slate-400 tracking-wide">
                        {% for part in ship.expensiveParts %}
                            <li class="flex justify-between py-1">
                                <span>{{ part.name }}</span>
                                <span>{{ part.price }} credits</span>
                            </li>
                        {% endfor %}
                    </ul>
// ... lines 47 - 52
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```

👉 Şablonda `expensiveParts` özelliği yok ama bu, biraz önce oluşturduğumuz `getExpensiveParts()` yöntemini çağıracak.

## 🧹 Filtering Out the Cheap Stuff / Ucuz Parçaları Filtrelemek

Yöntemimizin sadece pahalı parçaları döndürmesini sağlama zamanı. Unutmayın: `$this->parts` bir dizi değil – bazı avantajları olan özel bir `Collection` nesnesi. Bunlardan biri de `filter()` metodudur. Bu, her parça için bir geri çağırma (callback) çalıştırır. `true` dönerse, o parça son koleksiyonda yer alır. `false` dönerse, hariç tutulur. Yani şu şekilde yazabiliriz: `return $part->getPrice() > 50000;`:

```php
// src/Entity/Starship.php
// ... lines 1 - 16
class Starship
{
// ... lines 19 - 166
    public function getExpensiveParts(): Collection
    {
        return $this->parts->filter(function (StarshipPart $part) {
            return $part->getPrice() > 50000;
        });
    }
// ... lines 174 - 195
}
```

👉 Bu kod, yalnızca fiyatı 50.000'den fazla olan parçaları filtreler.

Hepsi tamam! Fakat… bu oldukça verimsiz. Yıldız gemimizle ilişkili her parçayı sorguluyor, ardından PHP'de filtreliyoruz. 50.000 parça olduğunu ama sadece 10 tanesinin fiyatı 50.000'den fazla olduğunu düşünün. Ne büyük israf! Peki, Doctrine'den sorguyu sadece yıldız gemisiyle ilişkili ve fiyatı 50.000'den fazla olan parçalar için yapmasını isteyebilir miyiz?

## 🧩 The Power of the Criteria Object / Criteria Nesnesinin Gücü

İşte `Criteria` nesnesi burada devreye giriyor. Bu oldukça güçlü bir nesne, ancak biraz da gizemli. Mevcut mantığı temizleyin ve bunun yerine `$criteria`'yı şu şekilde oluşturun: `Criteria::create()->andWhere(Criteria::expr()->gt('price', 50000))`. Bunu kullanmak için: `return $this->parts->matching($criteria);` döndürün:

```php
// src/Entity/Starship.php
// ... lines 1 - 7
use Doctrine\Common\Collections\Criteria;
// ... lines 9 - 17
class Starship
{
// ... lines 20 - 167
    public function getExpensiveParts(): Collection
    {
        $criteria = Criteria::create()->andWhere(Criteria::expr()->gt('price', 50000));
        return $this->parts->matching($criteria);
    }
// ... lines 173 - 194
}
```

👉 Bu kod, koleksiyon nesnesi üzerinden Doctrine'in Criteria nesnesiyle sorgulama yapar.

**Önemli Not:** `Criteria` nesnesi veritabanı seviyesinde filtreleme yapar, bu da performansı önemli ölçüde artırır çünkü sadece koşulları karşılayan kayıtlar veritabanından getirilir.

Beni tanıyorsanız, sorgu mantığımı repository sınıflarında düzenli tutmayı severim. Ancak şimdi bazı sorgu mantıkları entity içinde. Bu kötü mü? Gerekli değil, ama ben işleri düzenli tutmayı severim. O yüzden bu Criteria mantığını repository'e taşıyalım.

## 📦 Moving Criteria to the Repository / Criteria Mantığını Repository'e Taşımak

Öncelikle `StarshipPartRepository`'yi oluşturmamız gerekiyor. Repository dosyası mevcut değilse, şu şekilde oluşturun:

```php
// src/Repository/StarshipPartRepository.php
<?php

namespace App\Repository;

use App\Entity\StarshipPart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @extends ServiceEntityRepository<StarshipPart>
 */
class StarshipPartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StarshipPart::class);
    }

    public static function createExpensiveCriteria(): Criteria
    {
        return Criteria::create()->andWhere(Criteria::expr()->gt('price', 50000));
    }
}
```

👉 Bu statik yöntem, 50.000'den büyük fiyatlar için Criteria nesnesi oluşturur.

**Neden statik?** İki sebep:

1. Birincisi, kullanabiliyoruz (içeride `$this` değişkenini kullanmıyoruz)
2. İkincisi, bu yöntemi `Starship` entity'sinden çağıracağız ve entity'lere servis otomatik bağlanamaz (autowire edilemez), bu yüzden statik olmalı.

Ayrıca `StarshipPart` entity'sine repository referansını eklememiz gerekiyor:

```php
// src/Entity/StarshipPart.php
<?php

namespace App\Entity;

use App\Repository\StarshipPartRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: StarshipPartRepository::class)]
class StarshipPart
{
    // ... geri kalan kod
}
```

`Starship`'e geri dönelim, Criteria ile ilgili kısmı temizleyin ve yerine `StarshipPartRepository::createExpensiveCriteria()` yazın:

```php
// src/Entity/Starship.php
// ... lines 1 - 4
use App\Repository\StarshipPartRepository;
// ... lines 6 - 18
class Starship
{
// ... lines 21 - 168
    public function getExpensiveParts(): Collection
    {
        return $this->parts->matching(StarshipPartRepository::createExpensiveCriteria());
    }
// ... lines 173 - 194
}
```

👉 Artık Criteria mantığını doğrudan repository'de tanımladık ve kod daha düzenli.

## 🏗️ Combining Criteria with Query Builders / Criteria ve Query Builder'ı Birleştirmek

Her şey hala harika çalışıyor, şimdi bir adım ileri gidelim ve geliştirici kaslarımızı çalıştıralım. Criteria ile QueryBuilder'ı birleştiren bir yöntem oluşturalım.

Diyelim ki herhangi bir yıldız gemisi için tüm pahalı parçaların listesini almak istiyoruz. `StarshipPartRepository`'ye şu yöntemi ekleyin:

```php
// src/Repository/StarshipPartRepository.php
// ... lines 1 - 22
    /**
     * @return StarshipPart[]
     */
    public function getExpensiveParts(int $limit = 10): array
    {
        return $this->createQueryBuilder('sp')
            ->addCriteria(self::createExpensiveCriteria())
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
// ... lines 34 - 51
```

👉 Bu yöntem, Criteria nesnesini QueryBuilder ile birleştirerek veritabanından pahalı parçaları sınırla birlikte döndürür.

**Bu yöntemin avantajları:**

-   `createQueryBuilder('sp')` ile başlayarak standart QueryBuilder kullanıyoruz
-   `addCriteria()` ile önceden tanımladığımız Criteria'yı ekliyoruz
-   `setMaxResults($limit)` ile sonuç sayısını sınırlıyoruz
-   İsterseniz `orderBy()` veya `andWhere()` gibi ek koşullar ekleyebilirsiniz
-   `getQuery()->getResult()` ile sorguyu çalıştırıp sonuçları alıyoruz

## 🔧 Testing Our Implementation / Uygulamamızı Test Etmek

Şimdi uygulamamızı test edelim. Tarayıcınızda bir starship detay sayfasına gidin ve "Expensive Parts" bölümünü inceleyin. Sadece 50.000 krediden fazla değeri olan parçaları görmelisiniz.

Eğer test verileri yoksa, `StarshipPartFactory`'de bazı pahalı parçalar oluşturabilirsiniz:

```php
// src/Factory/StarshipPartFactory.php
protected function getDefaults(): array
{
    return [
        'name' => self::faker()->randomElement([
            'Quantum Flux Capacitor', // 75000 credits
            'Warp Core Matrix', // 120000 credits
            'Plasma Conduit Assembly', // 60000 credits
            'Basic Bolt', // 50 credits
            'Simple Screw', // 25 credits
        ]),
        'price' => self::faker()->numberBetween(25, 150000),
        // ... other fields
    ];
}
```

## 🚀 Performance Benefits / Performans Avantajları

Criteria sistemi kullanmanın önemli avantajları:

1. **Veritabanı Seviyesinde Filtreleme**: Kriterler SQL sorgusu olarak çevrilir
2. **Bellek Tasarrufu**: Sadece gerekli kayıtlar belleğe yüklenir
3. **Ağ Trafiği Azalması**: Daha az veri transfer edilir
4. **Yeniden Kullanılabilirlik**: Criteria nesneleri farklı yerlerde kullanılabilir

**Performans Karşılaştırması:**

-   ❌ PHP Filtreleme: 50.000 kayıt çek → PHP'de filtrele → 10 kayıt göster
-   ✅ Criteria Filtreleme: Veritabanında filtrele → 10 kayıt çek → 10 kayıt göster

## 🔍 Advanced Criteria Usage / Gelişmiş Criteria Kullanımı

Criteria nesneleri çok güçlüdür. İşte bazı gelişmiş örnekler:

```php
// Çoklu koşul
$criteria = Criteria::create()
    ->andWhere(Criteria::expr()->gt('price', 50000))
    ->andWhere(Criteria::expr()->contains('name', 'Quantum'))
    ->orderBy(['price' => 'DESC'])
    ->setMaxResults(5);

// OR koşulu
$criteria = Criteria::create()
    ->andWhere(
        Criteria::expr()->orX(
            Criteria::expr()->gt('price', 100000),
            Criteria::expr()->contains('name', 'Core')
        )
    );

// IN koşulu
$criteria = Criteria::create()
    ->andWhere(
        Criteria::expr()->in('name', [
            'Warp Core Matrix',
            'Quantum Flux Capacitor'
        ])
    );
```

## 📝 Criteria vs QueryBuilder Karşılaştırması

| Criteria                                   | QueryBuilder                           |
| ------------------------------------------ | -------------------------------------- |
| ✅ Entity koleksiyonlarında kullanılabilir | ✅ Repository'de güçlü sorgular        |
| ✅ Yeniden kullanılabilir                  | ✅ Kompleks JOIN'ler                   |
| ✅ Type-safe expression'lar                | ✅ Raw SQL ekleme imkanı               |
| ❌ Sınırlı JOIN desteği                    | ❌ Sadece repository'de kullanılabilir |

## 🎯 Best Practices / En İyi Uygulamalar

1. **Repository'de Tanımla**: Criteria mantığını repository'de static method olarak tanımlayın
2. **Anlamlı İsimler**: `createExpensiveCriteria()` gibi açıklayıcı isimler kullanın
3. **Combine Wisely**: Criteria ve QueryBuilder'ı birleştirirken mantıklı sınırlar koyun
4. **Test Performance**: Büyük veri setlerinde performansı test edin
5. **Document Complex Logic**: Karmaşık Criteria mantığını dokümante edin

## 🔗 Navigation Links / Navigasyon Bağlantıları

⬅️ **Önceki:** [9_1_Ordering a Relation and fetch type.md](./9_1_Ordering%20a%20Relation%20and%20fetch%20type.md) - İlişkileri Sıralama ve Fetch Tipleri

➡️ **Sonraki:** 11_Creating a New Page for Parts.md - Parçalar için Yeni Sayfa Oluşturma

📚 **Ana Menü:** [README.md](../README.md) - Symfony Starshop Eğitim Serileri

## 🎉 Sonuç

Criteria sistemi, Doctrine'in en güçlü özelliklerinden biridir. Entity koleksiyonlarında veritabanı seviyesinde filtreleme yapmanıza olanak tanır, performansı artırır ve kodu daha organize tutar.

QueryBuilder ile birleştirdiğinizde, hem basit hem de karmaşık sorguları elegant bir şekilde yapabilirsiniz. Bu sistem, özellikle büyük veri setleriyle çalışırken hayat kurtarıcıdır.

Sırada, her bir parçayı listeleyen tamamen yeni bir sayfa oluşturmak var. Artık JOIN'lere ihtiyaç duymaya başlıyoruz! 🚀
