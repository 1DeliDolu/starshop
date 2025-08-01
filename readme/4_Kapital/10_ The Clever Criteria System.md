# 🧠 The Clever Criteria System / Akıllı Kriter Sistemi

`$ship->getParts()` yöntemimiz var: bu yöntem, yıldız gemimiz için tüm parçaları döndürür. Ancak mali yılın sonuna yaklaşıyoruz ve bütçemizi planlamamız gerekiyor. Sıkıcı, ama gerekli: `Ferengi` patronlarımız bunu talep ediyor! Çoğu parça ucuz, yani her şeyi bir arada tutan somunlar, vidalar ve bantlar gibi şeyler. Bunlar için endişelenmiyoruz. Bunun yerine, gemimizin 50.000 krediden fazla maliyeti olan tüm parçalarını hızlıca döndürmek istiyorum.

Tabii ki, denetleyicimizde (controller) yıldız gemisiyle ilişkili, fiyatı 50.000’den fazla olan tüm parçalar için yeni bir sorgu yapabiliriz. Ama bunun neresi eğlenceli? Kolaylık sağlayan `$ship->getParts()` kısayolunu kullanmak istiyorum. Bu mümkün mü?

## 🏗️ Adding getExpensiveParts() / getExpensiveParts() Yöntemini Eklemek

`Starship` sınıfına geçin ve `getParts()` yöntemini bulun. Onu kopyalayın, aşağıya yapıştırın ve adını `getExpensiveParts()` olarak değiştirin. Şimdilik her şeyi döndürün:


```php
// src/Entity/Starship.php
// ... lines 1 - 13
class Starship
{
// ... lines 16 - 160
    /**
     * @return Collection<int, StarshipPart>
     */
    public function getExpensiveParts(): Collection
    {
        return $this->parts;
    }
// ... lines 168 - 189
}
```

👉 Şu anda, bu yöntem tüm parçaları döndürüyor.

`show` şablonumuza geri dönüp bunu deneyin. `parts`'ı `expensiveParts` ile değiştirin:


```twig
// templates/starship/show\.html.twig
// ... lines 1 - 4
{% block body %}
// ... lines 6 - 19
    <div class="md:flex justify-center space-x-3 mt-5 px-4 lg:px-8">
// ... lines 21 - 25
        <div class="space-y-5">
            <div class="mt-8 max-w-xl mx-auto">
                <div class="px-8 pt-8">
// ... lines 29 - 58
                    <h4 class="text-xs text-slate-300 font-semibold mt-2 uppercase">
                        Expensive Parts ({{ ship.expensiveParts|length }})
                    </h4>
                    <ul class="text-sm font-medium text-slate-400 tracking-wide">
                        {% for part in ship.expensiveParts %}
// ... lines 64 - 71
                        {% endfor %}
                    </ul>
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
// ... lines 1 - 13
class Starship
{
// ... lines 16 - 163
    public function getExpensiveParts(): Collection
    {
        return $this->parts->filter(function (StarshipPart $part) {
            return $part->getPrice() > 50000;
        });
    }
// ... lines 170 - 191
}
```

👉 Bu kod, yalnızca fiyatı 50.000’den fazla olan parçaları filtreler.

Hepsi tamam! Fakat… bu oldukça verimsiz. Yıldız gemimizle ilişkili her parçayı sorguluyor, ardından PHP’de filtreliyoruz. 50.000 parça olduğunu ama sadece 10 tanesinin fiyatı 50.000’den fazla olduğunu düşünün. Ne büyük israf! Peki, Doctrine’den sorguyu sadece yıldız gemisiyle ilişkili ve fiyatı 50.000’den fazla olan parçalar için yapmasını isteyebilir miyiz?

## 🧩 The Power of the Criteria Object / Criteria Nesnesinin Gücü

İşte `Criteria` nesnesi burada devreye giriyor. Bu oldukça güçlü bir nesne, ancak biraz da gizemli. Mevcut mantığı temizleyin ve bunun yerine `$criteria`'yı şu şekilde oluşturun: `Criteria::create()->andWhere(Criteria::expr()->gt('price', 50000))`. Bunu kullanmak için: `return $this->parts->matching($criteria);` döndürün:


```php
// src/Entity/Starship.php
// ... lines 1 - 7
use Doctrine\Common\Collections\Criteria;
// ... lines 9 - 14
class Starship
{
// ... lines 17 - 164
    public function getExpensiveParts(): Collection
    {
        $criteria = Criteria::create()->andWhere(Criteria::expr()->gt('price', 50000));
        return $this->parts->matching($criteria);
    }
// ... lines 171 - 192
}
```

👉 Bu kod, koleksiyon nesnesi üzerinden Doctrine’in Criteria nesnesiyle sorgulama yapar.

Beni tanıyorsanız, sorgu mantığımı repository sınıflarında düzenli tutmayı severim. Ancak şimdi bazı sorgu mantıkları entity içinde. Bu kötü mü? Gerekli değil, ama ben işleri düzenli tutmayı severim. O yüzden bu Criteria mantığını repository’e taşıyalım.

## 📦 Moving Criteria to the Repository / Criteria Mantığını Repository'e Taşımak

`StarshipPartRepository`’ye geçiyoruz. Burada herhangi bir yere şu şekilde bir `public static` fonksiyon ekleyin: `createExpensiveCriteria()`:


```php
// src/Repository/StarshipPartRepository.php
// ... lines 1 - 6
use Doctrine\Common\Collections\Criteria;
// ... lines 8 - 12
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 15 - 19
    public static function createExpensiveCriteria(): Criteria
    {
        return Criteria::create()->andWhere(Criteria::expr()->gt('price', 50000));
    }
// ... lines 24 - 48
}
```

👉 Bu statik yöntem, 50.000’den büyük fiyatlar için Criteria nesnesi oluşturur.

Neden statik? İki sebep: Birincisi, kullanabiliyoruz (içeride `this` değişkenini kullanmıyoruz), ikincisi, bu yöntemi `Starship` entity’sinden çağıracağız ve entity’lere servis otomatik bağlanamaz (autowire edilemez), bu yüzden statik olmalı.

`Starship`’e geri dönelim, Criteria ile ilgili kısmı tamamen silin ve yerine `StarshipPartRepository::createExpensiveCriteria()` yazın:


```php
// src/Entity/Starship.php
// ... lines 1 - 4
use App\Repository\StarshipPartRepository;
// ... lines 6 - 15
class Starship
{
// ... lines 18 - 165
    public function getExpensiveParts(): Collection
    {
        return $this->parts->matching(StarshipPartRepository::createExpensiveCriteria());
    }
// ... lines 170 - 191
}
```

👉 Artık Criteria mantığını doğrudan repository’de tanımladık.

## 🏗️ Combining Criteria with Query Builders / Criteria ve Query Builder’ı Birleştirmek

Her şey hala harika çalışıyor, şimdi bir adım ileri gidelim ve geliştirici kaslarımızı çalıştıralım. Criteria ile QueryBuilder’ı birleştiren bir yöntem oluşturalım.

Diyelim ki herhangi bir yıldız gemisi için tüm pahalı parçaların listesini almak istiyoruz. Öncelikle, `getExpensiveParts()` yöntemini `Starship`ten kopyalayın. `StarshipPartRepository`'ye yapıştırın. Ardından, `return $this->createQueryBuilder('sp')` ile başlayın. Varsayılan olarak 10 olan bir `$limit` argümanı ekleyin. Bunu bir Criteria ile birleştirmek için `addCriteria(self::createExpensiveCriteria())` yazın. Şimdi bir QueryBuilder içindeyiz, bu yüzden normal şeyleri yapabiliriz, örneğin `setMaxResults($limit)`. `orderBy` veya `andWhere` eklemek ister misiniz? Buyurun ekleyin. Tabii ki, bitirirken `getQuery()->getResult()` kullanabilirsiniz:


```php
// src/Repository/StarshipPartRepository.php
// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 25
    /**
     * @return Collection<StarshipPart>
     */
    public function getExpensiveParts(int $limit = 10): Collection
    {
        return $this->createQueryBuilder('sp')
            ->addCriteria(self::createExpensiveCriteria())
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
// ... lines 37 - 61
}
```

👉 Bu yöntem, Criteria nesnesini QueryBuilder ile birleştirerek veri tabanından pahalı parçaları sınırla birlikte döndürür.

Criteria ile Query Builder’ı birleştirmek güçlü bir hamle.

Tamam, bu kadar yeterli. Sırada, her bir parçayı listeleyen tamamen yeni bir sayfa oluşturmak var. Artık JOIN’lere ihtiyaç duymaya başlıyoruz!
