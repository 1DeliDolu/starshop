# 🧠 Cache Service and Cache Pools / Önbellek Servisi ve Önbellek Havuzları

Tamam, `HttpClientInterface`'i enjekte ettik ve bir HTTP isteği yaparak bazı JSON verilerini aldık ve bunları sitemizde görüntüledik. Ancak her sayfa yüklemesinde bir HTTP isteği yapmak iyi bir fikir değil. HTTP istekleri yavaştır ve ana sayfamızın eskisinden daha yavaş yüklendiğini görebiliyoruz. Ayrıca ISS hızlı hareket eder, bu nedenle bu bilgileri sürekli güncellemek verimli değildir. Bu veriyi önbelleğe alabilecek bir servis var mı? Kesinlikle var!

## Finding the Cache Service / Önbellek Servisini Bulmak

Terminalinizi açın ve şu komutu çalıştırın:

```bash
php bin/console debug:autowiring cache
```

👉 Bu komut, uygulamamızda önbellekle ilgili servislerin olup olmadığını gösterir.

Ve... var! Bu `cache.app` takma adları uygulamamızda kullanıma hazır. Ayrıca `CacheItemPoolInterface`'e de dikkat edin. Havuzlar, önbelleğe alınmış öğeler için benzersiz ad alanlarıdır. Bunları küresel önbellek dizinindeki "alt klasörler" gibi düşünebilirsiniz. Bu, bir önbellek havuzunu temizlediğinizde diğerlerinin etkilenmeyeceği anlamına gelir. Bunun hakkında daha sonra konuşacağız.

Şimdilik basit tutacağız ve `CacheInterface` kullanacağız. Kodumuza geri dönelim ve `homepage()` metodunun içine, `CacheInterface` (Contracts paketinden olanı) yazın ve adına `$cache` diyelim.

```php
// ... lines 1 - 9
use Symfony\Contracts\Cache\CacheInterface;
// ... lines 11 - 13
class MainController extends AbstractController
{
// ... line 16
    public function homepage(
// ... lines 18 - 19
        CacheInterface $cache,
    ): Response {
// ... lines 22 - 37
    }
}
```

👉 Bu kodda, `CacheInterface` tipinde bir `$cache` servisi tanımlanır.

Şimdi aşağıda, şu iki satırı kopyalayın, silin ve `$issData = $cache->get()` yazın. İlk argüman önbellek anahtarı olmalı, örneğin `iss_location_data`. İkinci argüman ise anonim bir fonksiyon olmalı `()`. Kopyaladığınız iki satırı bu fonksiyonun içine yapıştırın. Ancak artık değişkene atamak yerine sadece `return` kullanın. Fakat anonim fonksiyonda `$client` değişkenini kullanabilmek için `use($client): array` yazmalısınız.

```php
// ... lines 1 - 16
    public function homepage(
// ... lines 18 - 20
    ): Response {
// ... lines 22 - 24
        $issData = $cache->get('iss_location_data', function (ItemInterface $item) use ($client): array {
// ... lines 26 - 27
            $response = $client->request('GET', 'https://api.wheretheiss.at/v1/satellites/25544');
            return $response->toArray();
        });
// ... lines 32 - 37
    }
// ... lines 39 - 40
```

👉 Bu kodda, ISS verileri ilk seferde HTTP isteğiyle alınır ve önbelleğe kaydedilir; sonraki seferlerde doğrudan önbellekten alınır.

## Debugging with the Cache Profiler / Önbellek Profiler ile Hata Ayıklama

Tarayıcımıza dönüp sayfayı yenilersek... hâlâ HTTP Client isteğini yaptık. Ve burada artık bir önbellek simgesi var; bu simge, önbelleğe bir şey yazılıp yazılmadığını gösterir. Yazılmış! Bu önbellek simgesine tıklayarak profiler'ı açalım... harika, değil mi? Bunun için özel bir havuz oluşturmadık, bu yüzden varsayılan havuz kullanılıyor ama özel havuzlar da oluşturabiliriz, bunu birazdan yapacağız.

Ana sayfaya geri dönüp tekrar yenilersek... artık HTTP isteği yok. Ve fareyi önbellek simgesinin üzerine getirirsek... bir şey yazılmadı. Ve şimdi sayfa yüklemeleri gözle görülür şekilde daha hızlı. Şu anda bu veri sonsuza kadar önbellekte kalır, ta ki önbelleği temizleyene kadar. Ancak geliştirme amacıyla, bu süreyi sınırlayalım. Fonksiyonumuzda ilk argüman olarak `ItemInterface` yazın ve adına `$item` deyin. İçeride `$item->expiresAfter()` yazarak süreyi 5 olarak ayarlayın.

```php
// ... lines 1 - 16
    public function homepage(
// ... lines 18 - 20
    ): Response {
// ... lines 22 - 24
        $issData = $cache->get('iss_location_data', function (ItemInterface $item) use ($client): array {
            $item->expiresAfter(5);
// ... lines 27 - 30
        });
// ... lines 32 - 37
    }
// ... lines 39 - 40
```

👉 Bu kodda, önbelleğe alınan veri 5 saniye sonra geçerliliğini yitirir.

Bu sayı saniye cinsindendir, yani 5 saniye sonra önbellek sona erer. Tarayıcımıza dönüp sayfayı yenilesek bile bir şey değişmez çünkü değer zaten önbellekteydi. Değişikliği görebilmek için önbelleği manuel olarak temizlememiz gerekir, böylece yeni 5 saniyelik süreyle tekrar önbelleğe alınabilir.

## Clearing the Cache / Önbelleği Temizleme

Varsayılan önbellek adaptörü bir dosya sistemidir, yani önbellek `var/cache/dev/pools/` dizininde depolanır. Burada `/app` klasörünü görebiliriz, bu da uygulama önbelleğine karşılık gelir. Bu dizini manuel olarak silebiliriz ama daha iyi bir yol var. Terminalde şu komutu çalıştırın:

```bash
php bin/console cache:pool:list
```

👉 Bu komut, uygulamadaki mevcut önbellek havuzlarını listeler.

`cache.app` havuzunu temizlemek için şu komutu kullanabiliriz:

```bash
php bin/console cache:pool:clear cache.app
```

👉 Bu komut, `cache.app` havuzunu temizler.

Ve... önbellek temizlendi! Tarayıcıya dönüp sayfayı yenilersek... işte HTTP isteğimiz! Hızlıca tekrar yenilersek... artık veriler önbellekten geliyor. Ve beş saniye sonra bir kez daha yenilediğimizde... yine HTTP isteği yapılıyor!

Sırada: Önbellek servisimizi nasıl yapılandıracağımızı öğrenelim.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./3_The HTTP Client Service.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./5_Bundle Config Configuring the Cache Service.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
