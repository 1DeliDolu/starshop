# 🆕 New Autowiring Attributes / Yeni Autowiring Nitelikleri

Peki Symfony 7'de ne yeni? Hiçbir şey! Asıl soru, Symfony 6.4'te ne yeni? Ya da belki, 6.3 veya 6.2'de... belki kaçırdıklarımız?

## 🚀 Yeni Özelliklere Hızlı Bir Bakış / Yeni Özelliklere Hızlı Bakış

Bu tür yenilikleri bulmak için en iyi yer... Symfony blogudur. Javier, her sürümde en önemli özellikleri detaylıca açıklar.

Favorilerimden bazılarını aldım, mesela workflow profiler. Eğer `workflow` bileşenini kullanıyorsanız, artık profiler içinde workflow'unuzun harika bir görselleştirmesini görebilirsiniz.

Logout sisteminde hayatı kolaylaştıran bazı değişiklikler de var... Yeni kısıtlamalar geldi, örneğin `PasswordStrengthConstraint` ve sıfır genişlikli boşluk gibi şüpheli karakterleri engelleyen bir kısıtlama daha. Bu, birisinin başka birinin kullanıcı adına benzeyen bir ad oluşturmasını engellemek için kullanılabilir.

API geliştiriyorsanız, bir sınıfa ait tüm metadataları görmek için harika bir `debug:serializer` komutu var.

Son olarak, yeni Webhook ve RemoteEvent bileşenleri var, ki bunlar kendi başına bir eğitim konusunu hak ediyor. Onu başka zamana bırakıyoruz.

Bunlar sadece favori yeniliklerimden birkaçı. Tümünü görmek için blogun "Living on the Edge" bölümüne gidip sürüme göre filtreleyebilirsiniz. Gerçek bir bilgi kaynağı!

## 🧩 Autowire Niteliği / Autowire Niteliği

Ama birlikte birkaç yeni özelliğe göz atalım, özellikle de autowiring sistemindeki yeniliklere. Bunlar Symfony'nin son birkaç sürümünde geldi ve... pek çok işlev sağlıyor. Sonuç olarak, muhtemelen bir daha asla `services.yaml` dosyasına dokunmanız gerekmeyecek.

Hadi başlayalım! Eski bir eğitimde, `$isDebug` argümanı için şöyle bir `bind` tanımlamıştım.



```yaml
// config/services.yaml
// ... lines 1 - 12
services:
// ... line 14
    _defaults:
// ... lines 16 - 17
        bind:
            'bool $isDebug': '%kernel.debug%'
// ... lines 20 - 32
```

👉 Bu yapı, `$isDebug` parametresinin değerini ayarlamak için kullanılmıştır.

Bunun sebebi, `src/Controller/VinylController.php` dosyasında; bu controller'a `autowire` edilemeyen bir `$isDebug` parametresi vermemdi.



```php
// src/Controller/VinylController.php
// ... lines 1 - 13
class VinylController extends AbstractController
{
    public function __construct(
        private bool $isDebug
    )
    {}
// ... lines 20 - 56
}
```

👉 Burada, constructor'da doğrudan parametre olarak `$isDebug` alınır.

Şimdi `services.yaml` dosyasındaki bind'ı kaldırın.

Yenilediğinizde hata alırsınız:

Bu servis için `$isDebug` argümanı var ama ne atanacağı bilinmiyor.

Bu yüzden bind vardı. Symfony'nin son birkaç sürümünden itibaren, artık `Autowire` niteliği var. Autowire edilemeyen bir argümanınız varsa, bu yardımcı olur. Argümanın başına ekleyin ve istediğiniz değeri tanımlayın. Bu bir servis, expression, environment variable, parametre veya başka bir şey olabilir. Burada istediğimiz parametre: `kernel.debug`.



```php
// src/Controller/VinylController.php
// ... lines 1 - 8
use Symfony\Component\DependencyInjection\Attribute\Autowire;
// ... lines 10 - 14
class VinylController extends AbstractController
{
    public function __construct(
        #[Autowire('%kernel.debug%')]
        private bool $isDebug
    )
    {}
// ... lines 22 - 58
}
```

👉 Bu satır, `$isDebug`'a `kernel.debug` parametresini atar.

İçeride, çalıştığından emin olmak için `dump($this->isDebug)` ekleyin.

Ve... çalışıyor! Autowire artık favori niteliğim. Eğer bu sınıfı açarsanız... ve Attribute dizinini incelerseniz, başka pek çok bağımlılık enjeksiyonu niteliği göreceksiniz. `Exclude` bir sınıfın otomatik servis olarak kaydedilmesini engeller. `Autoconfigure` ve `AutoconfigureTag`, servisinize seçenekler eklemenin yollarıdır. Bunu sınıfın veya bir interface'in üstüne koyarsanız, seçenekler o servise ya da ilgili interface'i implemente eden tüm servislere uygulanır.

Ayrıca `AutowireIterator` ve `AutowireLocator` var. Eğer belirli bir etiketi taşıyan birden fazla servisi almak isterseniz, `AutowireIterator` ile bunları iterable olarak alabilirsiniz. `AutowireLocator` ile de bir anahtar-değer dizisi (service locator) olarak alırsınız.

## 🔁 AutowireIterator Denemesi / AutowireIterator Kullanımı

Örneğin, `VinylController` içinde, uygulamadaki tüm konsol komutlarını iterable olarak almak istediğimizi varsayalım. `private iterable $commands` diyelim. Çalıştığını kanıtlamak için, bunları döngüyle dolaşıp nesneyi dump'layın.



```php
// src/Controller/VinylController.php
// ... lines 1 - 15
class VinylController extends AbstractController
{
    public function __construct(
// ... lines 19 - 21
        private iterable $commands,
    )
    {
        foreach ($this->commands as $command) {
            dump($command);
        }
    }
// ... lines 29 - 65
}
```

👉 Burada, constructor ile tüm komutlar döngüye alınır.

Şimdi durursak klasik bir hata alırız:

Bu `$commands` argümanı için ne atanacağı bilinmiyor!

Belirli bir etikete sahip tüm servislerin iterable olarak alınmasını istiyoruz. Bunu `#[AutowireIterator]` ile ve etiketi belirterek yapın: `console.command`.



```php
// src/Controller/VinylController.php
// ... lines 1 - 9
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
// ... lines 11 - 15
class VinylController extends AbstractController
{
    public function __construct(
// ... lines 19 - 20
        #[AutowireIterator('console.command')]
        private iterable $commands,
    )
    {
        foreach ($this->commands as $command) {
            dump($command);
        }
    }
// ... lines 29 - 65
}
```

👉 Bu, `console.command` etiketiyle tanımlı tüm servisleri iterable olarak almanızı sağlar.

Ve işte hepsi! Uygulamanızdaki tüm komutları görürsünüz. Evet, örnek biraz basit, ama oldukça güzel bir özellik!

Controller'da bu kodu geri alın.


```php
// src/Controller/VinylController.php
// ... lines 1 - 14
class VinylController extends AbstractController
{
    public function __construct(
        #[Autowire('%kernel.debug%')]
        private bool $isDebug,
    )
    {}
// ... lines 22 - 58
}
```

👉 Yeniden, sadece `$isDebug` parametresiyle devam ediyoruz.

Sırada: İstek verilerini (query parametreleri ve request payload gibi) almanın yeni, güçlü yollarına göz atacağız.
