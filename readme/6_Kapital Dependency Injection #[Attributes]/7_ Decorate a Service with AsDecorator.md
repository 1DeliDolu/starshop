# 🎨 Decorate a Service with AsDecorator / Bir Servisi AsDecorator ile Süslemek

Son bölümde, `#[AsAlias]` özniteliğini kullanarak `RemoteInterface`'i `ButtonRemote` ile eşlemiştik; böylece `RemoteInterface` tip ipucu verdiğimizde bize `ButtonRemote` servisi geliyordu. Fakat bu, bizim günlükleme (logging) işlemimizi bozdu! Symfony'ye, bize `LoggerRemote` servisini vermesini fakat `LoggerRemote`'a da `ButtonRemote` servisini iletmesini söylememiz gerekiyor.

## 🏷️ #\[AsDecorator]

Temelde Symfony'ye, `ButtonRemote`'un `LoggerRemote` tarafından dekore edildiğini söylememiz gerekiyor. Bunu yapmak için, `LoggerRemote` içinde başka bir öznitelik kullanıyoruz: `#[AsDecorator]` ve süslediği servisi (`ButtonRemote::class`) parametre olarak veriyoruz:


```php
// src/Remote/LoggerRemote.php
// ... lines 1 - 5
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
// ... line 7
#[AsDecorator(ButtonRemote::class)]
final class LoggerRemote implements RemoteInterface
// ... lines 10 - 38
```

👉 Bu kodda, `LoggerRemote` sınıfı `ButtonRemote` servisini dekore eder şekilde tanımlanmıştır.

Bu, Symfony'ye şunu söyler:

Hey, eğer birisi `ButtonRemote` servisini isterse, ona bunun yerine `LoggerRemote` ver.

Symfony esasen servisleri değiştirir ve ardından `ButtonRemote`'u `LoggerRemote`'un "iç" servisi haline getirir. Bu da, daha önce oluşturduğumuz `RemoteInterface`'in gerekliliğini netleştirir. Eğer doğrudan `ButtonRemote` tip ipucu verseydik, tip hatası alırdık çünkü Symfony, `LoggerRemote`'u enjekte etmeye çalışacaktı.

## 🧩 Service Decoration / Servis Süsleme

Şimdi şunu takip et: Biz `RemoteInterface` için otomatik bağımlılık eklemeyi (autowiring) kullanıyoruz. Bu, `ButtonRemote` ile eşleştirildiğinden, Symfony onu vermeye çalışıyor. Ama, `#[AsDecorator]` sayesinde, bunu `LoggerRemote` ile değiştiriyor... fakat `LoggerRemote`'a da `ButtonRemote`u iletiyor. Kısacası, `AsDecorator`, mevcut bir servisi başka bir servisle dekore etmemizi sağlar.

Uygulamaya geri dön, sayfayı yenile ve... "volume up" tuşuna bas. "Logs" profiler panelini kontrol et ve... tekrar günlükleme yaptığımızı göreceksin!

## 🧱 Multiple Decorators / Birden Fazla Dekoratör

`#[AsDecorator]` kullanmak, birden fazla dekoratör eklemeyi çok kolaylaştırır. Belki çocukların düğmelere basmasını engellemek için bir oran sınırlama (rate limiting) dekoratörü eklemek istiyoruz. Sadece `RemoteInterface`'i uygulayan bir `RateLimitingRemote` sınıfı oluşturup, ona da `#[AsDecorator(ButtonRemote::class)]` eklememiz yeterli.


```php
// src/Remote/RateLimitingRemote.php
#[AsDecorator(ButtonRemote::class)]
class RateLimitingRemote implements RemoteInterface
{
    public function __construct(
        private RateLimiter $rateLimiter,
        private RemoteInterface $inner,
    ) {
    }

    // ...
}
```

👉 Bu kod, `ButtonRemote` servisini oran sınırlama dekoratörüyle dekore eder.

Sıradaki adımda: Özel bir günlükleme kanalı ekleyeceğiz ve "adlandırılmış otomatik bağımlılık ekleme" (named autowiring) konusunu keşfedeceğiz!
