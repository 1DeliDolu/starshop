# 🔄 Ordering a Relation and "fetch" type / İlişki Sıralama ve "fetch" Türü

"Devam eden" bir yıldız gemisine tıklayın. Sonra açın: `templates/starship/show.html.twig`. Parçaları listelemek için `for part in ship.parts` kullanın.

Bu mükemmel çalışacak. Ama bir yakalama var: parçaların sırası garanti değil. Veritabanından istedikleri sırada çıkıyorlar!

Bunları isme göre sıralanmış olarak almayı tercih ederim. Bu, özel bir sorgu yazmamız gerektiği ve kullanışlı `ship.parts`'ımızı artık kullanamayacağımız anlamına mı geliyor?

Korkmayın, dostlar! Bazı numaralar öğrenelim!

## 🔢 Parçaları Yeniden Düzenleme

Starship entity'sine gidin ve parts property'sini bulun. parts'ın üstüne yeni bir öznitelik ekleyin: `#[ORM\OrderBy(['name' => 'ASC'])]`:

```php
// src/Entity/Starship.php
class Starship
{
    // ...
    /**
     * @var Collection<int, StarshipPart>
     */
    #[ORM\OneToMany(targetEntity: StarshipPart::class, mappedBy: 'starship', orphanRemoval: true)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $parts;
    // ...
}
```

Sayfayı yenileyin ve anladınız!

Eğer T'nin neden c'den önce geldiğini merak edip kafanızı kaşıyorsanız, ABC'lerinizi unutmamışsınızdır. Sadece Postgres büyük/küçük harfe duyarlı bir veritabanıdır. Yani büyük harf T görünüşe göre alfabetik sırada küçük harf c'den önce geliyor.

## 🧠 Akıllı Sorgular

Bu sayfa için sorguları kontrol edin ve biçimlendirilmiş SQL'i görüntüleyin. `starship_part`'tan sorgular, `starship_id` ID'mize eşit olan, name'e göre artan sırada: tam olarak istediğimiz sorgu!

## ⚠️ N+1 Problemi

Ana sayfaya geri dönün ve şablonunu açın: `templates/main/homepage.html.twig`. "arrived"'den sonra bir div ekleyin sonra parça sayısını yazdırın: `ship.parts|length`:

```twig
// templates/main/homepage.html.twig
{% block body %}
    <main class="flex flex-col lg:flex-row">
        <!-- ... -->
        <div class="px-12 pt-10 w-full">
            <!-- ... -->
            <div class="space-y-5">
                {% for ship in ships %}
                    <div class="bg-[#16202A] rounded-2xl pl-5 py-5 pr-11 flex flex-col min-[1174px]:flex-row min-[1174px]:justify-between">
                        <div class="flex justify-center min-[1174px]:justify-start">
                            <!-- ... -->
                            <div class="ml-5">
                                <!-- ... -->
                                <div class="text-slate-400 text-sm">
                                    {{ ship.class }}
                                    {% if ship.arrivedAt %}
                                        <span class="ml-2 text-xs text-slate-400">—
                                            {{ ship.arrivedAt|ago }}</span>
                                    {% endif %}
                                </div>
                                <div class="text-slate-400 text-sm">
                                    Parts: {{ ship.parts|length }}
                                </div>
                            </div>
                        </div>
                        <!-- ... -->
                    </div>
                {% endfor %}
            </div>
            <!-- ... -->
        </div>
    </main>
{% endblock %}
```

Ana sayfaya geri döndüğünüzde, mükemmel çalışıyor. Bu sayfa için sorguları kontrol edin, ilginçler. Sayfalamamız nedeniyle bunlardan bazıları biraz vahşi görünüyor, ama esasen starship için bir sorgumuz var ve `starship_part` ararsanız, her starship için parçalar için 5 ekstra sorgu var.

İşte neler oluyor: starship'leri alıyoruz, sonra `ship.parts`'ı saydığımız anda, Doctrine o veriye henüz sahip olmadığını fark ediyor. Bu yüzden her gemi için tüm parçaları tek tek alıp sayıyor. Bu yaygın bir durum: gemiler için bir sorgu ve sonra her geminin parçaları için bir ekstra sorgu. Bu N+1 problemi olarak bilinir: starship'ler için 1 sorgu ve her geminin parçaları için N sorgu. Sonra ele alacağımız küçük bir performans problemi.

## 🎯 Verimli Sorgulama

Ama burada daha büyük bir problem var! Sadece onları saymak için her `starship_part`'ı sorguluyoruz. Parça verisine ihtiyacımız yok, sadece kaç tane olduğunu bilmemiz gerekiyor. Bu küçük... bir tonla parçası olan bir geminiz olana kadar.

Bunu düzeltmek için, Starship entity'sindeki OneToMany'de, `EXTRA_LAZY`'ye ayarlanmış bir fetch seçeneği ekleyin:

```php
// src/Entity/Starship.php
class Starship
{
    // ...
    #[ORM\OneToMany(targetEntity: StarshipPart::class, mappedBy: 'starship', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $parts;
    // ...
}
```

Gidip bunun ne yaptığını görelim!

## 📊 Parçaları Sayma

Ana sayfaya geri dönün. Daha önce dokuz sorgumuz vardı... Şimdi??? Hala dokuz sorgu, ama parçalar için sorgu değişti. Tüm verilerini sorgulamak yerine, sadece onları sayıyor. Çok daha akıllı, değil mi?

Merak ediyor olabilirsiniz - kesinlikle ben ettim - neden `fetch="EXTRA_LAZY"`'yi her zaman kullanmıyoruz? Birincisi, bu parça dolu bir geminiz olduğu ve sadece onları saymak istediğiniz sürece endişelenmenize gerek olmayan küçük bir performans optimizasyonu. Daha da önemlisi, parçaları önce sayıp saymadığınıza veya üzerlerinde döngü yapıp yapmadığınıza bağlı olarak, bu ekstra bir sorguya neden olabilir.

## 🔍 Fetch Type Seçenekleri

### 📚 **LAZY (Varsayılan):**

-   İlişkili veriler ilk erişimde yüklenir
-   En yaygın kullanılan seçenek
-   Genellikle en iyi performansı sağlar

### ⚡ **EAGER:**

-   İlişkili veriler ana sorgu ile birlikte yüklenir
-   N+1 problemini önler ama büyük veri setlerinde bellek sorununa yol açabilir
-   Dikkatli kullanılmalı

### 🦥 **EXTRA_LAZY:**

-   Sadece gerekli operasyonlar (count, contains, etc.) için minimal sorgular
-   Count işlemleri için idealdir
-   Tüm koleksiyonu yüklemeden sayma yapar

### 📄 **SELECT:**

-   İkinci bir SELECT sorgusu ile yüklenir
-   Büyük koleksiyonlar için uygundur

## 🎯 Ne Zaman Hangi Fetch Type Kullanılır?

### ✅ **EXTRA_LAZY Kullanın:**

-   Sadece count, contains işlemleri yapıyorsanız
-   Büyük koleksiyonlarda bellek tasarrufu istiyorsanız
-   İlişkili verileri nadiren kullanıyorsanız

### ✅ **EAGER Kullanın:**

-   Her zaman ilişkili verilere ihtiyaç duyuyorsanız
-   Küçük veri setleriyle çalışıyorsanız
-   N+1 problemini kesin olarak önlemek istiyorsanız

### ✅ **LAZY Kullanın (Varsayılan):**

-   Çoğu durumda en iyi seçenek
-   Esnek kullanım senaryolarında
-   Orta boyutlu veri setlerinde

## 🧩 Kriteria Sistemi

Sıradaki meydan okumumuza! Sadece belirli bir fiyatın üzerindeki bir gemi için ilgili parçaları istesek ne olur? Hala `ship.parts` kısayolunu kullanabilir miyiz yoksa özel bir sorgu yapmamız mı gerekiyor? Bir sonraki konuda kriteria sistemini keşfedeceğiz.

## 🎯 Öğrenilen Dersler

1. **OrderBy**: İlişkili veriler sıralı olarak gelir
2. **N+1 Problem**: Her entity için ayrı sorgu çalışması
3. **EXTRA_LAZY**: Count işlemleri için optimize edilmiş fetch type
4. **Smart Queries**: Doctrine gereken minimum veriyi alır
5. **Performance Trade-offs**: Her fetch type'ın kendine özgü avantaj/dezavantajları var

### 🔍 **Önemli Detaylar:**

-   `OrderBy` sadece veritabanı seviyesinde sıralama yapar
-   `EXTRA_LAZY` count işlemlerini optimize eder ama tam koleksiyona erişimde ek sorgu gerektirebilir
-   N+1 problemi küçük veri setlerinde sorun olmayabilir ama büyüdükçe kritik hale gelir

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./8_Orphan Removal.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./10_Criteria System.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
