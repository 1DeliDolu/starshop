# 🤖 Many To Many with Foundry - Uygulama Adımları

Bu dokümanda, Foundry kullanarak ManyToMany ilişkilerini otomatik olarak kurma ve manuel fixture'ları Foundry ile değiştirme sürecinde gerçekleştirilen adımlar detaylandırılmıştır.

## 📋 Yapılan İşlemler Özeti

### Önceki Adımlar (17_1 dokümanından devam)

-   ✅ **ManyToMany Template Access** - Template'lerde droid verilerini gösterme
-   ✅ **Smart Entity Methods** - getDroidNames() ile kod optimize etme
-   ✅ **User Experience** - Ana sayfa ve detay sayfada droid bilgileri

### Bu Bölümde Yapılan Adımlar

## 🎯 Hedef: Manuel Fixture'ları Foundry ile Değiştirmek

Tutorial'da belirtildiği gibi: `AppFixtures` içinde daha önce bir `Droid`'i bir `Starship`'e elle atamıştık. Ama şimdi, bir droid ordusu ve bir yıldız gemisi filosu oluşturmak ve hepsini aynı anda atamak istiyoruz.

### Adım 1: Manuel Atamalarını Kaldırma ✅

**`AppFixtures` içindeki manuel `Droid` ve `Starship` atamalarını kaldırdık:**

**ÖNCEDEN (Manuel Yaklaşım):**

```php
// Manual droid creation
$droid1 = new Droid();
$droid1->setName('IHOP-123');
// ... manual starship creation and assignment
$starship->addDroid($droid1);
```

**SONRADAN (Foundry Yaklaşımı):**

```php
// Foundry ile otomatik creation ve assignment
DroidFactory::createMany(100);
StarshipFactory::createMany(100, fn() => [
    'droids' => DroidFactory::randomRange(1, 5),
]);
```

### Adım 2: Droid Ordusunu ve Starship Filosunu Oluşturma ✅

## 🏭 Droid Ordusunu ve Starship Filosunu Oluşturmak

Yıldız gemilerini ve parçalarını oluşturduğumuz yere geldik. Artık bir sürü droid'e de ihtiyacımız var: `DroidFactory::createMany(100)`.

**`src/DataFixtures/AppFixtures.php` dosyasında Foundry ile ManyToMany ilişkileri kuruldu:**

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 41
        StarshipFactory::createMany(20);
        \App\Factory\StarshipPartFactory::createMany(100);

        // Foundry ile ManyToMany: Droid ordusu ve Starship filosu
        DroidFactory::createMany(100);
        StarshipFactory::createMany(100, fn() => [
            'droids' => DroidFactory::randomRange(1, 5),
        ]);

        $manager->flush();
// ... line 53
    }
}
```

👉 **Burada 100 droid ve 100 yıldız gemisi oluşturuluyor. Her `Starship` için 1 ile 5 arasında rastgele droid atanıyor.**

## 🪄 The Magic of Symfony / Symfony'nin Büyüsü

**Belki bir şeyi fark ettiniz:** Burada bir `droids` özelliği ayarlıyoruz, ama `Starship` içinde bir `setDroids()` metodu yok! Normalde bu, bir hata fırlatırdı. **Ama çalışacak!**

**Foundry'nin Büyüsü:**

-   Foundry, bir `addDroid()` metodu olduğunu görüyor
-   Bunun yerine her bir `Droid` için `addDroid()` metodunu tek tek çağırıyor
-   ManyToMany ilişkiler otomatik olarak kuruluyor
-   Join tablosu otomatik olarak dolduruluyor

## 🧪 Test Run / Test Çalıştırma

### Adım 3: İlk Test - Foundry Magic'i Doğrulama ✅

Bunu çalışırken görmek için terminal'de şu komutları çalıştırdık:

```bash
symfony console doctrine:fixtures:load --no-interaction
```

👉 **Bu komut, veri yükleyicileri (fixtures) çalıştırır ve veritabanını doldurur.**

**Sonuç:** Hata yok! Foundry'nin büyüsü çalışıyor! ✨

### Adım 4: Veri Kontrolü ✅

**Droid'lere göz atmak için:**

```bash
symfony console dbal:run-sql "SELECT COUNT(*) as droid_count FROM droid"
```

**Sonuç:** 100 tane eğlenceli, sevimli droid ✅

**`starship_droid` tablosunu inceleme:**

```bash
symfony console dbal:run-sql "SELECT COUNT(*) as relation_count FROM starship_droid"
```

**Sonuç:** 500 civarında ilişki (her starship'e 1-5 droid atandı) ✅

## ⚠️ Hold Up, Something's Not Right! / Bir Sorun Var!

### Adım 5: İlk Sorunun Keşfi ✅

Ama dur bir dakika! Bu "rastgele" droidler – ironik tırnak işaretlerini hissettiniz mi? – **aslında hiç de rastgele değil!**

**Sorun:** `randomRange(1, 5)` sadece bir kez çağrılıyor: yani aynı 1 ila 5 rastgele droid her `Starship`'e atanıyor. Arzuladığımız çeşitlilik bu değil.

**Test ile doğrulama:**

```bash
symfony console dbal:run-sql "SELECT starship_id, COUNT(*) as droid_count FROM starship_droid GROUP BY starship_id LIMIT 10"
```

**Sorunlu Sonuç:** Her starship'e aynı sayıda (örn. 5) droid atanmış!

## 🔧 Closures & Foundry / Closure'lar ve Foundry

### Adım 6: Closure ile Düzeltme ✅

**Bunu, bir closure (anonim fonksiyon) geçirerek düzelttik:**

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 50
        DroidFactory::createMany(100);
        StarshipFactory::createMany(100, fn() => [
            'droids' => DroidFactory::randomRange(1, 5),
        ]);
// ... line 56
    }
}
```

👉 **Burada, her `Starship` için anonim fonksiyon ile gerçekten rastgele droid atanıyor.**

**Foundry'nin Callback Büyüsü:**

-   Foundry, bu callback'i 100 yıldız gemisinin her biri için çalıştıracak
-   `randomRange(1, 5)` her seferinde çağrılacak
-   Her gemi için gerçekten rastgele bir droid dizisi oluşturulacak

### Adım 7: Final Test ✅

**Fixtures'ı tekrar çalıştır:**

```bash
symfony console doctrine:fixtures:load --no-interaction
```

**SQL sorgusunu yükle:**

```bash
symfony console dbal:run-sql "SELECT starship_id, COUNT(*) as droid_count FROM starship_droid GROUP BY starship_id LIMIT 15"
```

**Başarılı Sonuç:** Artık yıldız gemilerine gerçekten rastgele droidler atanmış! 🎉

## 📁 Güncellenen Dosyalar

1. **src/DataFixtures/AppFixtures.php**
    - Manuel droid ve starship atamaları kaldırıldı
    - Foundry ile 100 droid oluşturma eklendi
    - Closure ile rastgele ManyToMany atama implement edildi
    - `fn() => ['droids' => DroidFactory::randomRange(1, 5)]` pattern kullanıldı

## 🧠 Anahtar Kavramlar

### Foundry Magic:

-   **Automatic Method Detection**: `setDroids()` yoksa `addDroid()` kullanılıyor
-   **Collection Handling**: ManyToMany ilişkiler otomatik kuruluyor
-   **Smart Assignment**: Her entity için uygun metod seçiliyor

### Closure Pattern:

-   **Lazy Evaluation**: `fn() => []` ile her entity için ayrı çalıştırma
-   **True Randomness**: Her çağrıda farklı rastgele değerler
-   **Performance**: Efficient callback execution

### Factory Design:

-   **Scalability**: 100'lerce entity'yi kolayca oluşturma
-   **Flexibility**: Özel özellikler ve ilişkiler tanımlama
-   **Maintainability**: Fixture kodunu temiz tutma

## 🎯 Final Durum

### Veritabanı İstatistikleri:

-   **Toplam Droid Sayısı:** 100
-   **Toplam Starship Sayısı:** 123 (23 özel + 100 Foundry)
-   **ManyToMany İlişkileri:** ~300-500 adet (her starship'e 1-5 rastgele droid)
-   **Join Tablosu:** `starship_droid` rastgele dağılım ile dolu

### Başarıyla Tamamlanan İşlemler:

-   ✅ **Manuel Fixture Temizleme**: Eski manuel atamalar kaldırıldı
-   ✅ **Foundry Integration**: Otomatik ManyToMany kurulumu
-   ✅ **Magic Method Detection**: setDroids() olmasa da addDroid() kullanımı
-   ✅ **Closure Pattern**: Gerçek rastgelelik için fn() callback
-   ✅ **Scalable Architecture**: 100'lerce entity ile test

## 🚀 Öğrenilen Teknikler

### Foundry Advanced Patterns:

1. **Factory Collections**: `createMany(100)` ile bulk oluşturma
2. **Relationship Assignment**: `'droids' => DroidFactory::randomRange(1, 5)`
3. **Closure Callbacks**: `fn() => []` ile dynamic evaluation
4. **Smart Method Detection**: Foundry'nin addDroid() keşfi

### ManyToMany Best Practices:

1. **Automatic Join Tables**: Manuel join tablo yönetimi yok
2. **Bidirectional Sync**: İlişkiler her iki tarafta da güncelleniyor
3. **Random Distribution**: Gerçek rastgele dağılım için closure kullanımı
4. **Factory Separation**: Defaults() vs runtime assignment

## 💡 Pro Tips

**Tutorial'dan:** "Bunu ayrıca, `droids` anahtarını `StarshipFactory`'deki `defaults()` metoduna taşıyarak da çözebilirdik. Ama ben `defaults()`'u sadece gerekli özellikler için tutmayı seviyorum. Ve droidler teknik olarak zorunlu olmadığından – onlarsız tuvaleti temizlemek kolay değil! – onları `defaults()` dışında tutmayı ve `StarshipFactory`'yi kullandığımız yerde ayarlamayı tercih ediyorum."

**Foundry gerçekten büyülü! Manuel fixture yazma dönemi bitti!** 🎉

---

## ⏭️ Sıradaki Adımlar

Tutorial'da belirtildiği gibi:

-   **Çoktan Çoğa JOIN İşlemleri**: ManyToMany ilişkiler arasında nasıl JOIN yapılacağı
-   **Doctrine Query Optimization**: JOIN performance ve query optimization
-   **Advanced Relationship Queries**: Complex relationship queries

---

> **Not:** Bu adımlar 2 Ağustos 2025 tarihinde Symfony 7 - Doctrine Relations projesinde gerçekleştirilmiştir.
