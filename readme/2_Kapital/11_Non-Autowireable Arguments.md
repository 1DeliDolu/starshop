## 🧩 Non-Autowireable Arguments / Otomatik Bağlanamayan Argümanlar

Daha önce, denetleyicimizde `getParameter()` ile kapsayıcıdan parametreleri nasıl alabileceğimizi öğrendik. Kendi servislerimizi oluşturmanın ne kadar kolay olduğunu da gördük. Ve tahmin et ne oldu? Özelleştirebileceğimiz tek şey bu değil! Kendi parametrelerimizi de oluşturabiliriz. Nasıl mı? Sana göstereceğim!

Kendi Parametrelerini Oluştur ve Kullan
`config/services.yaml` dosyasını aç. Burada boş bir `parameters` bölümü görüyoruz. İçine yeni bir parametre oluşturalım – mesela `iss_location_cache_ttl` – ve bunu 5 olarak ayarlayalım.
> config/services.yaml
```
parameters:
    iss_location_cache_ttl: 5
```

👉 Bu kodda, `iss_location_cache_ttl` adında yeni bir parametre oluşturuluyor ve değeri 5 olarak ayarlanıyor.

Bu parametreyi konfigürasyonda kullanacağız, böylece hiçbir değeri sabit olarak yazmamıza gerek kalmayacak. Ama önce, `MainController.php` dosyasına geri dön ve artık `kernel.project_dir` değerini dökmek yerine yeni parametremiz olan `iss_location_cache_ttl`'i dökelim.

```
class MainController extends AbstractController
{
    public function homepage(
    ): Response {
        dd($this->getParameter('iss_location_cache_ttl'));
    }
};
```

👉 Bu komut, denetleyicide `iss_location_cache_ttl` parametresinin değerini (yani 5'i) ekrana yazdırır.

Tarayıcıda sayfayı yenile, işte burada – 5!

Artık biliyoruz ki, denetleyicilerde `getParameter()` ile bu parametreyi alabiliyoruz. Peki ya bir denetleyici içinde değilsek? `getParameter()` yöntemi olmadan servislerde parametreleri nasıl kullanabiliriz? Bakalım… Eğer `homepage` fonksiyonuna yeni bir argüman – `$issLocationCacheTtl` – ekler ve `getParameter()` yerine bunu dökersek, sayfayı yenilediğimizde… hata!

```
public function homepage(
    $issLocationCacheTtl,
): Response {
    // ...
}
```

👉 Bu kodda, `$issLocationCacheTtl` argümanı fonksiyona eklendiğinde ve otomatik bağlanmaya çalışıldığında Symfony hata verecektir. Çünkü bu bir servis değil, bir parametredir ve Symfony bunu otomatik olarak bağlayamaz.

Symfony bu argümanı otomatik olarak bağlayamaz. Servisleri otomatik bağlayabilir, ama bu bir servis değil; bir parametre. Peki bunu nasıl yaparız? Cevap: Otomatik bağlat! Parametreleri tıpkı servisler gibi otomatik bağlayabiliriz ve bu, yapıcıda veya denetleyicide normal otomatik bağlamada olduğu gibi çalışır. Şimdi bakalım!

`#[Autowire()]` PHP Özelliği
Kodumuza dönelim ve argümanın üstüne autowire özelliğini ekleyelim. Şöyle yaz: `#[Autowire(param: 'iss_location_cache_ttl')]`.

```
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MainController extends AbstractController
{
    public function homepage(
        #[Autowire(param: 'iss_location_cache_ttl')]
        $issLocationCacheTtl,
    ): Response {
        // ...
    }
}
```

👉 Bu kodda, `$issLocationCacheTtl` argümanına `iss_location_cache_ttl` parametresi otomatik olarak bağlanır.

Tarayıcıya dönüp sayfayı yenilersen… 5! Çalışıyor! Tamam, bunu kaldırıp şimdi yeni parametremizi konfigürasyon dosyamızda nasıl kullanacağımızı görelim.

`config/packages/cache.yaml` dosyasını aç. Sabit değer yerine `%iss_location_cache_ttl%` yazalım.

```
framework:
    cache:
        pools:
            iss_location_pool:
                default_lifetime: '%iss_location_cache_ttl%'
```

👉 Bu kodda, `iss_location_pool` önbellek havuzunun ömrü `iss_location_cache_ttl` parametresi ile belirleniyor.

Bunu tarayıcıda kontrol ettiğimizde… her şey hala çalışıyor! Harika!

Argümanları Global Olarak Bağla
Devam etmeden önce, parametreleri otomatik bağlamanın bir yolunu daha göstermek istiyorum: parametre bağlama (parameter binding). `services.yaml` dosyasını aç ve `services` altında, `_defaults`’un hemen altına yeni bir bölüm ekle: `bind`. İçine değişken adımızı – `$issLocationCacheTtl` – ve değer olarak `%iss_location_cache_ttl%` ekleyelim.

```
services:
    _defaults:
        autowire: true
        autoconfigure: true
    bind:
        $issLocationCacheTtl: '%iss_location_cache_ttl%'
```

👉 Bu kodda, `$issLocationCacheTtl` adındaki tüm argümanlar otomatik olarak `%iss_location_cache_ttl%` parametresi ile bağlanır.

Argüman ile bind içindeki ad eşleştiğinde, Symfony bu parametreyi otomatik olarak ona bağlar. İstersek tür belirtimi – `int` – de ekleyebiliriz. `MainController.php` dosyasında da argümanda `int` belirtmemiz gerekir.

```
public function homepage(
    int $issLocationCacheTtl,
): Response {
    // ...
}
```

👉 Bu kodda, `$issLocationCacheTtl` değişkeni tür olarak `int` tanımlanmıştır ve global bind ile otomatik olarak parametre değeri atanır.

Bunu denediğimizde… bu da çalışıyor! Ve global olarak otomatik bağlama yaptığımız için PHP özelliklerini birden çok yerde tekrarlamaktan kurtuluyoruz. Çok pratik! Şu anda bu parametreyi yalnızca konfigürasyonda kullandığımız için şimdilik bunu kaldırabiliriz.

Sonraki: Otomatik bağlanamayan servisleri nasıl otomatik bağlatabileceğimizi görelim. Şaşırtıcı derecede kolay.
