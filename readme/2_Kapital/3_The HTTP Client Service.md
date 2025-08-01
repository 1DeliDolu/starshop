### 📡 HTTP İstemci Servisi

Symfony'nin, "bileşenler" olarak adlandırılan, birbirinden bağımsız ve çok küçük PHP kütüphanelerinden oluşan bir koleksiyon olduğunu biliyoruz. Şu anda yalnızca birkaç tanesini yükledik, ancak daha fazla özelliğe ihtiyaç duydukça, daha fazla bileşen yükleyeceğiz. Son eğitimde, nesneleri JSON’a dönüştürmemize yardımcı olan `serializer` bileşenini yükledik. `StarshipApiController.php` dosyasını açın. Aşağıda, Mac'te "cmd" veya Windows'ta "control" tuşuna basılı tutarak `json()` metoduna tıklayın. Burada, `serializer` bileşenimizi görüyoruz. Bu, bu servise sahip olup olmadığımızı kontrol eder ve eğer varsa, `serialize()` metodu çağrılır.

Tamam, sitemiz oldukça güzel, ama diyelim ki... Uluslararası Uzay İstasyonu’nun (ISS) gerçek zamanlı konumunu gösterse daha da harika olmaz mıydı? Tabii ki olurdu! Ve ne güzel ki, bu bilgileri gösteren bir web sitesi zaten var. `wheretheiss.at` adresine gidiyoruz ve... işte karşınızda! Görünüşe göre ISS şu anda Pasifik Okyanusu üzerinde ve – güzel haber – bu bilgileri almak için kullanabileceğimiz bir API'leri de var. Oldukça kullanışlı! Bu URL'yi kopyalayıp yeni bir sekmede açarak JSON çıktısını görebilirsiniz.

### 🧰 HTTP Client Bileşenini Yüklemek

Ama önce, uygulamamızda API istekleri yapmamıza yardımcı olacak bir HTTP istemcisi olup olmadığını kontrol edelim. Terminalde şu komutu çalıştırın:

```
bin/console debug:autowiring http
```

Ve... bazı HTTP ile ilgili servislerimiz var, ama bir HTTP istemcimiz yok. Doğru! Uygulamamızda şu an HTTP istekleri yapabilen bir servis yok. Ama bunu yükleyebiliriz. Bunun için, dış HTTP istekleri yapma konusunda oldukça iyi olan `http-client` bileşenine ihtiyacımız var. Terminalde şu komutu çalıştırın:

```
composer require symfony/http-client
```

Bu paket adının nereden geldiğini merak ediyorsanız, iyi bir soru! Tarayıcınızda "symfony http client" diye ararsanız, en üstte çıkan sonuçlardan biri Symfony HTTP Client dokümantasyonu olacaktır. "Installation" (Kurulum) başlığı altında bu terminal komutunu ve bileşenle ilgili bazı faydalı bilgileri bulabilirsiniz.

Şimdi tekrar terminalde şu komutu çalıştırın:

```
bin/console debug:autowiring http
```

Ve işte karşınızda: `HttpClient` servisi! Artık bu yeni servisi uygulamamızda kullanmak için `type hint` olarak belirtebiliriz. Ama... bekleyin... bu işlem herhangi bir bundle yüklemedi. Eğer şu komutu çalıştırırsanız:

```
git status
```

Yalnızca `composer.json` ve `composer.lock` dosyalarının değiştiğini göreceksiniz. Sorun değil! Yüklediğimiz şey saf bir PHP paketidir ve servis sınıfları (yani bir işi gerçekleştiren sınıflar) içerse de, örneğin:

> “Hey! `http_client` adında bir servis istiyorum, bu `HttpClientInterface` örneği olmalı ve şu özel argümanlarla oluşturulmalı.”

şeklinde bir yapılandırma içermez.

Peki bu servis nereden geldi? Cevap: `FrameworkBundle`. `config/bundles.php` dosyasını açın. İlk bundle burada `FrameworkBundle`. Bu, Symfony'nin temel bundle’ıdır ve uygulamamızın başından beri yüklüdür. Bu bundle’ın süper gücü, yeni yüklenen Symfony bileşenlerini izlemek ve servislerini otomatik olarak kaydetmektir. Oldukça kullanışlı!

```php
// ... lines 1 - 2
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
// ... lines 5 - 14
];
```

### 📤 HTTP İsteği Göndermek

Artık yeni `HttpClient` servisimizi kullanabiliriz! `MainController.php` dosyasını açın ve `homepage()` metodunda yeni servisi `type hint` olarak belirtin. Bunu birkaç satıra böleceğiz... `HttpClientInterface` yazın ve `$client` olarak adlandırın:

```php
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MainController extends AbstractController
{
    public function homepage(
        HttpClientInterface $client,
    ): Response {
        $response = $client->request('GET', 'https://api.wheretheiss.at/v1/satellites/25544');
        $issData = $response->toArray();
        dump($issData);
    }
}
```

Tarayıcıda ana sayfayı yenileyin ve aşağıdaki ikona imleci getirin... harika! İşte verimiz! Yanında gördüğünüz başka bir ikon daha var. Bu HTTP İstemcisi simgesi, bu sayfada gerçekleştirilen toplam HTTP isteği sayısını gösterir. Bu Debug ikonuna tıklayarak Symfony Profiler’ı açabilir ve isteği inceleyebilirsiniz. HTTP İstemcimiz, web debug araç çubuğu ile entegredir ve isteğin başarıyla yapıldığını görebilirsiniz. Harika!

Şimdi geri dönüp `dump()` satırını kaldırın ve veriyi şablona gönderin:

```php
return $this->render('main/homepage.html.twig', [
    'issData' => $issData,
]);
```

### 🖼️ Veriyi Şablonda Görüntülemek

`homepage.html.twig` dosyasında, sayfanın sonuna başka bir `<div>` ekleyin. İçine bir `<h2>` yerleştirin ve başlığı "ISS Location" yapın. Görsellik için bazı sınıflar da ekleyelim. Aşağıya `<p>` etiketleri içinde verilerimizi yazalım: Zaman, Yükseklik, Enlem, Boylam ve Görünürlük.

```twig
{% block body %}
    <main class="flex flex-col lg:flex-row">
        <div class="px-12 pt-10 w-full">
            <div>
                <h2 class="text-4xl font-semibold my-8">ISS Location</h2>
                <p>Time: {{ issData.timestamp|date }}</p>
                <p>Altitude: {{ issData.altitude }}</p>
                <p>Latitude: {{ issData.latitude }}</p>
                <p>Longitude: {{ issData.longitude }}</p>
                <p>Visibility: {{ issData.visibility }}</p>
            </div>
        </div>
    </main>
{% endblock %}
```

Tarayıcıda sayfayı yenileyin ve... işte karşınızda! Uluslararası Uzay İstasyonu’nun gerçek zamanlı konumunu, az önce eklediğimiz tüm verilerle birlikte görüntüledik! Görüntü harika!

Bu ne kadar harika olsa da, biri ana sayfayı her ziyaret ettiğinde API’ye bir HTTP isteği gönderiyoruz, ve HTTP istekleri yavaş olabilir. Bunu düzeltmek için Symfony'nin bir başka servisi olan **önbellek** servisini kullanacağız.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./2_KnpTimeBundle Install the Bundle, Get its Service.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./4_Cache Service and Cache Pools.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
