# 🧙‍♂️ Autoconfiguration / Otomatik Yapılandırma

Bu gerçek zamanlı ISS konumu özelliği harika, ancak bunu sadece ana sayfada değil, her sayfada görebilsek daha da güzel olurdu. Bunu nasıl yapabiliriz? Verileri her action içinde iletebiliriz ama bu ideal değil. Bunun yerine, şablonda gerçek veriyi alacak özel bir Twig fonksiyonu oluşturacağız. Böylece, `base.html.twig` dosyamızda ISS konum verisini her denetleyiciden geçirmeye gerek kalmadan gösterebiliriz. Uygun mu? Hadi başlayalım!

## 🛠️ Creating a Twig Extension with MakerBundle / MakerBundle ile Twig Eklentisi Oluşturma

Öncelikle, bir Twig eklentisi oluşturmamız gerekiyor. Önceki bir derste `Symfony Maker Bundle` yüklemiştik. Bunun bazı şablon kodları oluşturup oluşturamayacağını görelim. Terminalinizde şu komutu çalıştırın:

```bash
php bin/console make
```

👉 Bu komut, mevcut olan tüm "make" komutlarının listesini gösterir.

Bir hata alıyoruz ama bu, elimizde hangi komutların olduğunu gösteriyor ve... bakın! Twig ile ilgili bir komut var - `make:twig-extension`. Aradığımız şey bu! Şunu çalıştırın:

```
php bin/console make:twig-extension
```

👉 Bu komut, bir Twig eklentisi sınıf adı sorar. Varsayılan adı (`AppExtension`) kullanabiliriz. Bu, iki dosya oluşturur: `AppExtension.php` ve `AppExtensionRuntime.php`.

İlk dosyayı açalım: `/src/Twig/Extension/AppExtension.php`.

## 📄 AppExtension.php File Structure / AppExtension.php Dosya Yapısı

```php
// ... lines 1 - 4
use App\Twig\Runtime\AppExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [AppExtensionRuntime::class, 'doSomething']),
        ];
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [AppExtensionRuntime::class, 'doSomething']),
        ];
    }
}
```

👉 Bu dosya, Twig filtreleri ve fonksiyonları eklemek için iki metot içeriyor.

## 🧪 Creating a Twig Filter / Twig Filtresi Oluşturmak

Şu anda sadece fonksiyonlarla ilgileniyoruz, bu yüzden `getFilters()` metodunu tamamen silebiliriz. `getFunctions()` içinde ise demo olan `function_name`'i daha alakalı bir şeyle değiştirelim. Örneğin `get_iss_location_data` olabilir. Bu, şablonlarda çağıracağımız gerçek Twig fonksiyon adı olacak.

```php
// ... lines 1 - 11
    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [AppExtensionRuntime::class, 'doSomething']),
        ];
    }
// ... lines 18 - 19
```

👉 Burada, `AppExtensionRuntime::class` içinde bir metoda çağrı yapılıyor. Şu an ismi `doSomething`. Bunu, fonksiyonumuzla eşleşmesi için `getIssLocationData()` olarak yeniden adlandıralım ve gereksiz parametreyi silelim.

## 🧩 AppExtensionRuntime.php - Method Refactoring / AppExtensionRuntime.php - Metot Yeniden Adlandırma

```php
// ... lines 1 - 4
use Twig\Extension\RuntimeExtensionInterface;
class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }
    public function getIssLocationData($value)
    {
        // ...
    }
}
```

👉 Bu kodda, ihtiyacımız olmayan `$value` parametresini kaldırıp metodu güncelleyebiliriz.

## 📝 AppExtension.php - Function Name Update / AppExtension.php - Fonksiyon İsmini Güncelleme

```php
// ... lines 1 - 9
class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_iss_location_data', [AppExtensionRuntime::class, 'getIssLocationData']),
        ];
    }
}
```

👉 Burada, fonksiyon adı ve ilgili metot ismi güncellenmiştir.

## 🚚 Data Fetching Code Move / Veri Alma Kodunu Taşıma

Şimdi ana sayfa `homepage()` aksiyonundaki veri alma kodunu kopyalayın, silin ve gerekirse bazı kodları da temizleyin. Artık bu veriyi şablona iletmeye gerek yok.

```php
// ... lines 1 - 17
class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(
        StarshipRepository $starshipRepository,
    ): Response {
        $ships = $starshipRepository->findAll();
        $myShip = $ships[array_rand($ships)];
        return $this->render('main/homepage.html.twig', [
            'myShip' => $myShip,
            'ships' => $ships,
        ]);
    }
}
```

👉 Bu kodda artık `issData` şablona iletilmiyor.

## 🔁 Move Fetch Logic to AppExtensionRuntime.php / Veri Alma Mantığını AppExtensionRuntime.php'ye Taşıma

```php
// ... lines 1 - 6
class AppExtensionRuntime implements RuntimeExtensionInterface
{
// ... lines 9 - 13
    public function getIssLocationData()
    {
        return $issLocationPool->get('iss_location_data', function () use ($client): array {
            $response = $client->request('GET', 'https://api.wheretheiss.at/v1/satellites/25544');
            return $response->toArray();
        });
    }
}
```

👉 Bu kodda `$issLocationPool` ve `$client` tanımlı değil, bu yüzden bağımlılık enjeksiyonu yapılmalı.

## 🧷 Injecting dependencies / Bağımlılıkları Enjekte Etme

Yönteme doğrudan bağımlılık enjekte edemeyiz, ancak kurucuya enjekte edebiliriz. PHP 8'in "Constructor Property Promotion" özelliğini kullanacağız.

```php
// ... lines 1 - 10
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly CacheInterface $issLocationPool,
    ) {
    }
// ... lines 16 - 26
```

👉 Burada, gerekli servisler kurucuya tip tanımlamasıyla enjekte ediliyor.

## 🧹 Method Update for Properties / Özelliklerle Metot Güncelleme

```php
// ... lines 1 - 16
    public function getIssLocationData()
    {
        return $this->issLocationPool->get('iss_location_data', function (): array {
            $response = $this->client->request('GET', 'https://api.wheretheiss.at/v1/satellites/25544');
            return $response->toArray();
        });
    }
// ... lines 25 - 26
```

👉 Bu kodda artık bağımlılıklar doğrudan nesne üzerinden çağrılıyor.

## 💥 Variable "issData" does not exist. / Değişken "issData" bulunamadı.

Bu hata, artık bu değişkeni denetleyiciden iletmediğimiz için oluşuyor. Ama şablonumuz hala bu değişkeni kullanıyor. `/templates/main/homepage.html.twig` dosyasını açın ve aşağıya özel Twig fonksiyonumuzu ekleyin:

```twig
// ... lines 1 - 4
{% block body %}
    <main class="flex flex-col lg:flex-row">
// ... lines 7 - 8
        <div class="px-12 pt-10 w-full">
// ... lines 10 - 54
            {% set issData = get_iss_location_data() %}
// ... lines 56 - 64
        </div>
    </main>
{% endblock %}
```

👉 Burada özel Twig fonksiyonu ile ISS verisi alınıyor.

## 🤔 How does this work? The black magic behind. / Nasıl Çalışıyor? Sihir Nerede?

Sayfayı tekrar yenilediğimizde... özel fonksiyonumuz çalışıyor. Ama Twig bu sınıfı nasıl buluyor? Hiçbir ek yapılandırma yapmadık. Bunun nedeni, `/config/services.yaml` içindeki `autoconfigure: true` seçeneğidir. Symfony, tüm servislerimizi otomatik olarak yapılandırır. Yani bir sınıf `AbstractExtension` gibi belirli bir temel sınıfı genişletiyorsa, Symfony onu ilgili sisteme entegre eder.

Kısacası, belirli bir sınıfı genişletir veya bir arayüz uygularsanız, Symfony ne yaptığınızı anlar ve entegrasyonu otomatik yapar.

İçeride, otomatik yapılandırma servislerimize özel bir etiket ekler (ör. `console.command`) veya bir öznitelik aracılığıyla çalışır.

## 🧩 Why do we need Twig Extension Runtime? / Neden Twig Extension Runtime'a İhtiyacımız Var?

Ayrı bir `AppExtensionRuntime` olması dikkat çekici! Twig'de extension runtimelar her zaman vardı, ancak yakın zamanda daha yaygın hale geldiler. Servisleri doğrudan Twig extension'a enjekte edebilirdik, ancak bu durumda eklenti ve tüm bağımlılıkları, eklenti fonksiyonunu/filtreyi kullanmasak bile yüklenirdi. Twig extension runtime, eklenti mantığını "tembel" hale getirir. Yani yalnızca gerektiğinde yüklenir. Bizim örnekte her sayfada ISS verisi gösterdiğimiz için pek fark etmiyor, ama yalnızca bazı sayfalarda kullanılan fonksiyonlar/filtreler için bu önemli bir avantajdır. En iyi uygulama, Twig extension'ı olabildiğince bağımsız ve hafif bırakmak ve tüm ağır işlemleri extension runtime'a taşımaktır.

## 🖼️ Rendering the Data on Every Page / Veriyi Her Sayfada Gösterme

Şimdi `homepage.html.twig` dosyasındaki ilgili HTML kodunu kopyalayıp silin ve `base.html.twig` dosyasını açıp, logonun altına yapıştırın. Biraz sadeleştirin. Yeni bir `<div>` oluşturun ve içine "ISS Location" yazın. Parantez içinde `{{ issData.visibility }}` ekleyin. `<div>`'e bir başlık (title) verin ve bu başlığı verinin güncellenme zamanı yapın. Gereksiz kodları temizleyin.

```twig
// ... lines 1 - 13
    <body class="text-white" style="background: radial-gradient(102.21% 102.21% at 50% 28.75%, #00121C 42.62%, #013954 100%);">
// ... line 15
            <div>
// ... lines 17 - 20
                    {% set issData = get_iss_location_data() %}
                    <div title="Updated at: {{ issData.timestamp|date }}">
                        ISS Location ({{ issData.visibility }})
                    </div>
                    <div>
                        <p>Altitude: {{ issData.altitude }}</p>
                        <p>Latitude: {{ issData.latitude }}</p>
                        <p>Longitude: {{ issData.longitude }}</p>
                    </div>
// ... lines 30 - 45
            </div>
// ... lines 47 - 50
    </body>
// ... lines 52 - 53
```

👉 Bu kodla, ISS bilgisi tüm sayfalarda üstte gösterilir.

Ve bu kadar! Symfony servisleri, yapılandırması ve ortamlarının temellerini kapsadık. Artık çok güçlüsünüz!

Bir sonraki eğitimde, PHP'de veritabanı ile çalışmanın endüstri standardı olan Doctrine'i tanıtacağız. O zamana kadar pratik yapın, bir şeyler inşa edin ve bizimle paylaşın. Sorunuz varsa, yorumlarda buradayız. Görüşmek üzere!

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./13_Environment Variables.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
</div>
