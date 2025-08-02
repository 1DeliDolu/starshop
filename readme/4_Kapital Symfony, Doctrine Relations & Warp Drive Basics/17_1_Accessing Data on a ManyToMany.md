# 🤖 Accessing Data on a ManyToMany - Uygulama Adımları

Bu dokümanda, ManyToMany ilişkilerindeki verilere erişim ve bunları template'lerde gösterme sürecinde gerçekleştirilen adımlar detaylandırılmıştır.

## 📋 Yapılan İşlemler Özeti

### Önceki Adımlar (16_1 dokümanından devam)

-   ✅ **ManyToMany İlişkisi Kuruldu** - Starship ↔ Droid arasında
-   ✅ **Droidler Oluşturuldu** - 3 özel droid (IHOP-123, D-3P0, BONK-5000)
-   ✅ **Starship Oluşturuldu** - USS DroidCarrier ile droid ataması
-   ✅ **Doctrine Büyüsü Test Edildi** - removeDroid() ile otomatik join tablo yönetimi

### Bu Bölümde Yapılan Adımlar

## 🎯 Hedef: Starship'lere Atanan Droidleri Template'lerde Göstermek

### Adım 1: Starship Show Template'inde Droid Listesi ✅

**`templates/starship/show.html.twig` dosyasında "Arrived At" bölümünden sonra droid listesi eklendi:**

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

#### 🌀 The loop.last / Döngüde Virgül Kullanımı

**Açıklama:** Virgül koymak istiyoruz, ama sonda fazladan virgül olmasın. `{% if not loop.last %}, {% endif %}` ile bunu sağlıyoruz. Daha havalı yolları var, ama şimdilik basit tutalım.

**Boş Durum:** Hiç droid yoksa, `{% else %}` etiketiyle "No droids on board (clean up your own mess)" yazıyoruz. Kaba ama etkili! 😄

**Teknik Mantık:**

-   `loop.last`: Twig'in döngüdeki son elemanı tespit eden değişkeni
-   `{% if not loop.last %}`: Son eleman değilse virgül ekle
-   `{% else %}`: Collection boşsa alternatif mesaj göster

### Adım 2: Ana Sayfada Droid İsimlerini Gösterme ✅

#### 🏠 Droids on the Homepage / Ana Sayfada Droidler

Ana sayfada da droidleri göstermek istiyoruz. `templates/main/homepage.html.twig` şablonunu açıp, parçalardan hemen sonra yeni bir div ekleyip şunu yazıyoruz: `Droids: {{ ship.droidNames ?: 'none' }}`

**`templates/main/homepage.html.twig` dosyasında Parts bilgisinden sonra droid bilgisi eklendi:**

```twig
// templates/main/homepage.html.twig
// ... lines 1 - 4
{% block body %}
    <main class="flex flex-col lg:flex-row">
// ... lines 7 - 8
        <div class="px-12 pt-10 w-full">
// ... lines 10 - 17
            <div class="space-y-5">
                {% for ship in ships %}
                    <div class="bg-[#16202A] rounded-2xl pl-5 py-5 pr-11 flex flex-col min-[1174px]:flex-row min-[1174px]:justify-between">
                        <div class="flex justify-center min-[1174px]:justify-start">
                            <img class="h-[83px] w-[84px]" src="{{ asset(ship.statusImageFilename) }}" alt="Status: {{ ship.statusString }}">
                            <div class="ml-5">
                            // ... lines 24 - 36
                                <div>
                                    Parts: {{ ship.parts|length }}</div>
                                <div>
                                    Droids: {{ ship.droidNames ?: 'none' }}
                                </div>
                            </div>
                        </div>
                    // ... lines 44 - 54
                    </div>
                {% endfor %}
            </div>
        // ... lines 58 - 73
        </div>
    </main>
{% endblock %}
```

👉 **Bu kod, her yıldız gemisi için droid isimlerini ana sayfada gösterir; yoksa "none" yazar.**

**Uygulanan kod:**

```twig
<div class="text-slate-400 text-sm">
    Parts: {{ ship.parts|length }}
</div>
<div class="text-slate-400 text-sm">
    Droids: {{ ship.droidNames ?: 'none' }}
</div>
```

### Adım 3: Starship Entity'sine getDroidNames() Metodu Ekleme ✅

#### 🧠 The Smart Method / Akıllı Yöntem

Virgül ekleme işini tekrar döngüyle yapabilirdik, ama droid isimlerine iki yerde ihtiyaç duyduğumuz için, bunun için bir akıllı metod ekliyoruz. Bu metodu `Starship` sınıfının altına ekliyoruz: `public function getDroidNames(): string`. Droid isimlerini virgülle ayırıp string olarak döndürmek için şunu kullanıyoruz:

**`src/Entity/Starship.php` dosyasına akıllı metod eklendi:**

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
}
```

👉 **Bu metod, droid isimlerini virgülle ayrılmış şekilde string olarak döndürür.**

**Kısa açıklama:**

-   Önce `$this->droids`, elimizdeki droid nesnelerinin koleksiyonu
-   `map()`, koleksiyondaki her bir droid için fonksiyon çalıştırır
-   `fn(Droid $droid) => $droid->getName()`, her droidin adını alır
-   `toArray()`, koleksiyonu diziye çevirir
-   Son olarak `implode(', ', ...)`, o diziyi virgül ile ayrılmış bir string haline getirir

Artık `getDroidNames()` metodu sayesinde, `{{ ship.droidNames ?: 'none' }}` diyebiliyoruz.

**Hepsi bu kadar! Sayfayı yenile... ve ana sayfada droid isimlerinin tadını çıkar.** 🎉

**Uygulanan kod:**

```php
public function getDroidNames(): string
{
    return implode(', ', $this->droids->map(fn(Droid $droid) => $droid->getName())->toArray());
}
```

#### 📝 Final Sonuç ve Özet

**Hepsi bu kadar! Sayfayı yenile... ve ana sayfada droid isimlerinin tadını çıkar.**

**Neler Başardık:**

-   ✅ **Template Integration**: ManyToMany verilerini template'lerde gösterdik
-   ✅ **Smart Entity Methods**: getDroidNames() ile kod tekrarını önledik
-   ✅ **User Experience**: Hem detay hem ana sayfada droid bilgilerini gösterdik
-   ✅ **Clean Code**: Virgül yönetimi ve boş durum handling

**Sıradaki Hedef:**

> **Foundry ile ManyToMany ilişkisinin fixture'larda nasıl ayarlanacağını göreceğiz. Foundry burada da parlıyor!** 🚀

## 🔍 Teknik Detaylar

### ManyToMany Veri Erişimi:

1. **Template'de Direkt Erişim**: `ship.droids` ile Collection'a erişim
2. **Döngü Kullanımı**: `{% for droid in ship.droids %}` ile her droid'e erişim
3. **Virgül Yönetimi**: `{% if not loop.last %}, {% endif %}` ile son elemanda virgül yok
4. **Boş Durum**: `{% else %}` ile droid yoksa özel mesaj

### getDroidNames() Metodunun Mantığı:

```php
// 1. $this->droids → Collection<Droid>
// 2. ->map(fn(Droid $droid) => $droid->getName()) → Collection<string>
// 3. ->toArray() → array<string>
// 4. implode(', ', ...) → string (virgülle ayrılmış)
```

## 🎨 Template İyileştirmeleri

### Starship Show Sayfası:

-   **Droid Başlığı**: "Droids" section eklendi
-   **Droid Listesi**: Virgülle ayrılmış droid isimleri
-   **Boş Durum Mesajı**: "No droids on board (clean up your own mess)"

### Ana Sayfa:

-   **Kısa Bilgi**: "Droids: [isimler]" formatında
-   **Fallback**: Droid yoksa "none" gösterimi
-   **Ternary Operator**: `{{ ship.droidNames ?: 'none' }}` kullanımı

## 📁 Güncellenen Dosyalar

1. **templates/starship/show.html.twig**

    - Droid listesi bölümü eklendi
    - Twig döngüsü ve virgül yönetimi implement edildi

2. **templates/main/homepage.html.twig**

    - Droid bilgisi satırı eklendi
    - getDroidNames() metodu kullanımı

3. **src/Entity/Starship.php**
    - getDroidNames() metodu eklendi
    - Collection mapping ve string manipülasyonu

## 🧠 Anahtar Kavramlar

### Collection İşlemleri:

-   **Collection Access**: Entity ilişkilerindeki collection'lara direkt erişim
-   **Lazy Loading**: Droidler sadece gerektiğinde yüklenir
-   **Map Function**: Collection elemanlarını transform etme
-   **Array Conversion**: Collection'dan array'e dönüştürme

### Template Teknikleri:

-   **Loop Variables**: `loop.last` ile döngü kontrolü
-   **Conditional Output**: `{% if %}` ile koşullu çıktı
-   **Fallback Values**: `?:` operator ile varsayılan değerler
-   **Empty Handling**: `{% else %}` ile boş durum yönetimi

### Code Reusability:

-   **Entity Methods**: Business logic'i entity'de tutma
-   **Template Reuse**: Aynı veriyi farklı sayfalarda kullanma
-   **DRY Principle**: getDroidNames() ile kod tekrarını önleme

## 🎯 Çalışma Sonuçları

### Starship Show Sayfası:

-   **USS DroidCarrier**: "D-3P0, BONK-5000" şeklinde gösterim
-   **Diğer Starship'ler**: "No droids on board (clean up your own mess)" mesajı

### Ana Sayfa:

-   **USS DroidCarrier**: "Droids: D-3P0, BONK-5000"
-   **Diğer Starship'ler**: "Droids: none"

### Database Durumu:

-   **Join Tablosu**: starship_droid tablosunda 2 aktif ilişki
-   **Droid Count**: Toplam 103 droid (100 factory + 3 özel)
-   **Active Relations**: USS DroidCarrier ↔ [D-3P0, BONK-5000]

## 🚀 Öğrenilen Teknikler

### ManyToMany Template Access:

1. **Direct Collection Access**: `entity.collection` syntax
2. **Loop Control**: Twig döngü değişkenleri kullanımı
3. **Conditional Rendering**: Veri durumuna göre farklı çıktılar
4. **String Formatting**: Temiz ve kullanıcı dostu gösterim

### Entity Design Patterns:

1. **Computed Properties**: getDroidNames() gibi hesaplanmış değerler
2. **Collection Helpers**: Collection manipülasyon metodları
3. **Template-Friendly Methods**: UI için optimize edilmiş metodlar
4. **Business Logic Encapsulation**: Entity'de iş mantığını saklama

**ManyToMany ilişkilerinden veri okuma artık çok kolay! OneToMany'den hiç farkı yok!** 🎉

---

## ⏭️ Sıradaki Adımlar

Tutorial'da belirtildiği gibi:

-   **Foundry ile ManyToMany**: Fixture'larda ManyToMany ilişkilerinin nasıl kurulacağı
-   **Advanced Collection Operations**: Daha gelişmiş collection işlemleri
-   **Performance Optimization**: Lazy loading ve query optimization

---

> **Not:** Bu adımlar 2 Ağustos 2025 tarihinde Symfony 7 - Doctrine Relations projesinde gerçekleştirilmiştir.
