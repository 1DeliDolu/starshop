# 🏞️ Symfony Environments / Symfony Ortamları

Bazen, farklı senaryolarda geliştirme yapmamıza yardımcı olacak bir dizi yapılandırmaya gerçekten ihtiyaç duyarız. Neyse ki Symfony'nin tam da bu işe yarayan bir özelliği var: ortamlar (environments).

## The APP\_ENV Değişkeni

Proje dizinimizin kökünde bulunan `.env` dosyasında bazı ortam değişkenlerimiz var.

```
APP_ENV=dev
APP_SECRET=930f26d714e6fa9188943d7e037a63fa
```

👉 Bunlar, uygulamamız için senaryoya (veya *environment*) göre değiştirebileceğimiz yapılandırma kümeleridir. Symfony, hangi değişkenleri kullandığımızı görmek için bu dosyayı okur ve ilgili ortamı oluşturur.

Şu anda burada yalnızca birkaç ortam değişkenimiz var, örneğin `dev` olarak ayarlanmış `APP\_ENV` değişkeni gibi. Bu, Symfony'ye uygulamanın geliştirme modunda yüklenmesi gerektiğini bildirir. Uygulamamızı yayına (`production`) aldığımızda ise bunu `prod` olarak değiştirmek isteriz; prod modu performans için optimize edilmiştir ve hassas verilerin sızmasını engeller. Peki bu tam olarak nerede kullanılıyor?

`/public/index.php` dosyasını açın. Bu dosya, her istekte çalışan ve uygulamamızı başlatan ön denetleyicidir (front controller).

```
use App\Kernel;
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
```

👉 Burada `App\Kernel` sınıfının bir örneği oluşturuluyor ve bu sınıf üzerinde bazı metodlar var. "command" tuşuna basılı tutup `Kernel()` üzerine tıklarsanız bu sınıfı açabilirsiniz. Bu sınıf neredeyse boş, yalnızca use `MicroKernelTrait`; satırı var ve asıl kodun çoğu bu trait'ten geliyor.

```
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
```

👉 Bunu açarsak... işte burada! Burada örneğin `configureContainer()` gibi bir dizi metod bulunuyor, bu metodlar yapılandırma dosyalarımızı içe aktarıyor. İçerisinde, aşağıda, \`$this->environment`değişkeni var; biraz incelerseniz bunun **APP\_ENV** değişkenimizin değeri olduğunu görebilirsiniz. Yani, ortam özelinde bir yapılandırma eklemek istersek, bunu **config/packages/** dizininde, ortam adını (örneğin **dev** veya **prod**) ve ardından yapılandırma dosya adını (örneğin **framework.yaml**) kullanarak yapabiliriz.

## 🏷️ The when@ {ENV} Config / when@ {ORTAM} Yapılandırması

Bu şekilde çalışır, ancak yakın zamanda Symfony, bunu yapmanın çok daha havalı bir yolunu sundu: `**when@**` söz dizimi. Bunu yeni yapılandırma dosyalarında sıkça görebilirsiniz. Örneğin **framework.yaml** dosyasını açarsak, en sonda... işte burada – **when\@test!** Bu kod yalnızca **test** ortamı için yüklenir.

```
when@test:
    framework:
        test: true
        session:
            storage_factory_id: session.storage.factory.mock_file
```

👉 Bu bölüm, sadece test ortamı için framework yapılandırmasını geçerli kılar.

**monolog.yaml** dosyamızda da, **when\@dev** altında ortam özelinde yapılandırmaların olduğunu görebiliriz. Bu, Symfony'ye yalnızca dev ortamında bu yapılandırmayı yüklemesini söyler. Aşağıya kaydırırsak, **test** ve **prod** ortamları için de farklı yapılandırmalar olduğunu görebiliriz.

```
when@dev:
    monolog:
        handlers:
            main:
                type: stream
                path: "%kernel.logs_dir%/%kernel.environment%.log"
                level: debug
                channels: ["!event"]
```

👉 Bu bölüm, yalnızca dev ortamı için monolog yapılandırmasını ayarlar.

**MicroKernelTrait**'e geri dönersek, burada **configureRoutes()** metodunda da aynı şey geçerli. Ayrıca c**onfig/routes/framework.yaml** dosyasında **when\@dev** olduğunu görebiliriz; yani bu rotalar yalnızca dev ortamında içe aktarılır. **web\_profiler.yaml** dosyasında da aynısı mevcut. Symfony, varsayılan olarak, uygulamamızda kullanabileceğimiz üç ortam (veya "**modes**") ile gelir: **dev**, **prod** ve **test**. İsterseniz kendi özel ortamınızı da oluşturabilirsiniz, ancak genellikle bu üçü işinizi fazlasıyla görecektir.

Şimdi zaten aşina olduğumuz bir dosyayı açalım – config/bundles.php.

```
return [
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
];
```

👉 Burada, uygulamamızda etkinleştirilen paketlerin bir dizisi bulunuyor. Anahtar paket sınıfı, değer ise bu paketin kullanılabileceği ortamların bir dizisidir. Örneğin **WebProfilerBundle** sadece **dev** ve **test** ortamlarında kullanılabilir. **DebugBundle** ve **MakerBundle** ise yalnızca **dev** (envirement) ortamında etkinleştirilmiştir. Geliştirme sırasında çok kullanışlıdırlar ama kesinlikle **prod** ortamında kullanılmamalıdırlar, çünkü hassas bilgiler sızabilir.

Sonraki: Şimdi uygulamamızı prod ortamında yüklemeyi deneyelim.
