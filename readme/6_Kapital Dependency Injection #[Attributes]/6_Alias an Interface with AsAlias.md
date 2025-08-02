# 🏷️ Alias an Interface with AsAlias / AsAlias ile Bir Arayüzü Takma Ad Olarak Kullanma

Yeni bir özellik ekleme zamanı! Butonlara basıldığında log (kayıt) tutmak istiyorum, böylece minyonlarımızın neler yaptığını takip edebileceğiz!

## 🧑‍💻 Tek Sorumluluk İlkesi ve Dekoratör Deseni

`ButtonRemote` içine doğrudan logger servisi enjekte edip loglama yapabilirdik. Ama teknik olarak bu, "tek sorumluluk ilkesi"ni ihlal eder. Yani, bir sınıf yalnızca tek bir işi yapmalı. Şu anda bu sınıf butonlara basmayı yönetiyor. Log eklersek iki iş yapmış olur. Bu genellikle sorun değil, ama kendimize bir meydan okuma katalım!

Bunun yerine, "dekorasyon" adı verilen bir tasarım deseni kullanacağız. Yani `ButtonRemote`'u saran, onu "dekor" eden yeni bir sınıf oluşturacağız.

## 🧩 LoggerRemote Dekoratör Sınıfı

`src/Remote/` içinde `LoggerRemote` adlı yeni bir PHP sınıfı oluşturun. Bu, dekoratörümüz olacak ve süslediği sınıfla aynı metodlara sahip olmalı. `ButtonRemote`'daki iki metodu kopyalayıp buraya yapıştırın ve gövdelerini silin. Bir kurucu ekleyin; `private LoggerInterface $logger` (Psr\Log'dan) ve `private ButtonRemote $inner` olarak iki bağımlılık ekleyin.


```php
// src/Remote/LoggerRemote.php
// ... lines 1 - 6
final class LoggerRemote
{
    public function __construct(
        private LoggerInterface $logger,
        private ButtonRemote $inner,
    ) {
    }
// ... lines 14 - 34
}
```

👉 Bu sınıf, log tutma işlemini eklemek için `ButtonRemote`'u dekore eder.

Her metodda önce içteki nesneyi (`$this->inner`) çağırın. `press()`'te `$this->inner->press($name)` ve `buttons()`'da `return $this->inner->buttons()` yazın:


```php
// src/Remote/LoggerRemote.php
// ... lines 1 - 6
final class LoggerRemote
{
// ... lines 9 - 14
    public function press(string $name): void
    {
        $this->inner->press($name);
    }
// ... line 27
    /**
     * @return string[]
     */
    public function buttons(): iterable
    {
        return $this->inner->buttons();
    }
}
```

👉 Dekoratör temel metotları içteki nesneye devreder.

Şimdi log ekleyelim. İçteki press'ten önce `$this->logger->info('Pressing button {name}', ['name' => $name])`, sonrasında ise `'Pressed button {name}'` yazın:


```php
// src/Remote/LoggerRemote.php
// ... lines 1 - 6
final class LoggerRemote
{
// ... lines 9 - 14
    public function press(string $name): void
    {
        $this->logger->info('Pressing button {name}', [
            'name' => $name
        ]);
        $this->inner->press($name);
        $this->logger->info('Pressed button {name}', [
            'name' => $name
        ]);
    }
// ... lines 27 - 34
}
```

👉 Bu kod, hem basmadan önce hem de bastıktan sonra log mesajı yazar.

## 🏗️ Kontrolcüde LoggerRemote'u Enjekte Etme

Artık dekoratörümüz hazır! Gerçekten kullanmak için, `RemoteController`'da `ButtonRemote` yerine `LoggerRemote`'u enjekte edin:


```php
// src/Controller/RemoteController.php
// ... lines 1 - 12
final class RemoteController extends AbstractController
// ... line 14
    #[Route('/', name: 'home', methods: ['GET', 'POST'])]
    public function index(Request $request, LoggerRemote $remote): Response
    {
// ... lines 18 - 32
    }
}
```

👉 Kontrolcüde LoggerRemote doğrudan kullanılır.

Uygulamada "power" butonuna basıp profiler'daki logları kontrol edin. Artık loglar görünmelidir!

## 🤝 Ortak Arayüz: RemoteInterface

Her iki remote sınıfında aynı metotlar var. Bu, ortak bir arayüz kullanabileceğimizi gösterir. `src/Remote/`'da `RemoteInterface` adında yeni bir arayüz oluşturun, iki metodun stub'unu ekleyin:


```php
// src/Remote/RemoteInterface.php
// ... lines 1 - 4
interface RemoteInterface
{
    public function press(string $name): void;
    /**
     * @return string[]
     */
    public function buttons(): iterable;
}
```

👉 Her iki remote sınıfı için ortak bir arayüz.

Her iki remote sınıfını da bu arayüzü uygulayacak şekilde değiştirin:


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 8
final class ButtonRemote implements RemoteInterface
// ... lines 10 - 30
```


```php
// src/Remote/LoggerRemote.php
// ... lines 1 - 6
final class LoggerRemote implements RemoteInterface
// ... lines 8 - 36
```

LoggerRemote'da kurucuda artık `ButtonRemote` yerine `RemoteInterface` kullanın:


```php
// src/Remote/LoggerRemote.php
// ... lines 1 - 6
final class LoggerRemote implements RemoteInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private RemoteInterface $inner,
    ) {
    }
// ... lines 14 - 34
}
```

👉 Artık decorator zincirinde herhangi bir RemoteInterface enjekte edilebilir.

## 🪧 #\[AsAlias] ile Arayüz Alias'ı

Uygulamayı yenileyin. Hata!

`Cannot autowire service LoggerRemote: argument $inner of method __construct() references interface RemoteInterface but no such service exists.`

Bu, Symfony bir arayüzü otomatik bağlamaya çalıştığında birden fazla implementasyon olduğunda ortaya çıkar. Symfony’ye, RemoteInterface kullandığında hangi servisi kullanacağını söylememiz lazım. Hata mesajı da yol gösteriyor.

Bir arayüzü gerçek servise "alias" yapmak gerekir. Teknik olarak bu, `App\Remote\RemoteInterface` ID’sine sahip yeni bir servis oluşturur ama gerçekte bu bir alias, yani bir yönlendirmedir.

`#[AsAlias]` ile bunu sağlayabiliriz. `ButtonRemote`'un başına ekleyin:


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 5
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
// ... lines 7 - 9
#[AsAlias]
final class ButtonRemote implements RemoteInterface
{
// ... lines 13 - 30
}
```

👉 Bu attribute, RemoteInterface otomatik bağlandığında ButtonRemote'un kullanılmasını sağlar.

Uygulamayı yenileyin. Hata yok! "Channel up" tuşuna basın, profiler’da buton mantığı ve log mesajları görünüyor mu bakın.

## 🧑‍💻 Artık Controller’da RemoteInterface Kullanabilirsiniz

Kontrolcüde somut bir servis yerine arayüzü kullanabilirsiniz:


```php
// src/Controller/RemoteController.php
// ... lines 1 - 4
use App\Remote\RemoteInterface;
// ... lines 6 - 12
final class RemoteController extends AbstractController
{
// ... line 15
    public function index(Request $request, RemoteInterface $remote): Response
    {
// ... lines 18 - 32
    }
}
```

👉 Artık controller'da arayüz tipini kullandığınızda da uygulamanız çalışır.

Sayfayı yenileyin, bir butona basın... Mantık çalışıyor ama loglar artık görünmüyor!

Çünkü RemoteInterface'i ButtonRemote'a alias'ladık, Symfony dekorasyonumuzu bilmiyor! RemoteInterface tipini görünce doğrudan ButtonRemote'u enjekte ediyor, LoggerRemote'u değil.

## ⏭️ Sonraki Adım: Bunu, service decoration ve elbette başka bir attribute kullanarak düzelteceğiz.
