# 🕰️ New Component: Scheduler / Yeni Bileşen: Scheduler

En havalı yeni bileşenlerden biri de `Scheduler`. Bu bileşen Symfony 6.3 ile geldi. Haftalık rapor oluşturmak, her 10 dakikada bir bir tür heartbeat göndermek, rutin bakım yapmak... ya da özel ve ilginç bir iş tetiklemek istiyorsanız, bu bileşen tam size göre. Gerçekten çok güzel! Kendi eğitimini hak ediyor ama şimdi küçük bir test sürüşü yapacağız.

## 📦 Installing Scheduler / Scheduler Kurulumu

Komut satırında şunu çalıştırın:

```bash
composer require symfony/scheduler symfony/messenger
```

👉 Bu komut, `Scheduler` ve ona bağlı olan `Messenger` paketini kurar.

`Scheduler`, `Messenger` ile birlikte çalışır! İşleyiş şu şekilde: Normalde Messenger ile olduğu gibi bir mesaj sınıfı ve handler oluşturursunuz. Sonra Symfony’ye şunu söylersiniz:

Ben, bu mesajın her yedi günde bir, her bir saatte bir... veya daha ilginç aralıklarla işlenmesini istiyorum.

## 📨 Creating the Message Class & Handler / Mesaj Sınıfı ve Handler Oluşturma

Yani ilk adım, bir Messenger mesajı oluşturmaktır. Şunu çalıştırın:

```shell
php bin/console make:message
```

Mesajın adı olarak `LogHello` yazın. Harika! Burada mesaj sınıfı oluşturuldu — `LogHello`:

```php
// src/Message/LogHello.php

// ... lines 1 - 2
namespace App\Message;
final class LogHello
{
    public function __construct()
    {
    }
}
```

👉 Bu kod, basit bir Messenger mesaj sınıfı oluşturur.

Ve handler’ı, yani `LogHello` mesajı Messenger üzerinden gönderildiğinde çağrılacak olan `__invoke()` metoduna sahip sınıfı:

```php
// src/MessageHandler/LogHelloHandler.php

// ... lines 1 - 2
namespace App\MessageHandler;
use App\Message\LogHello;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
#[AsMessageHandler]
final class LogHelloHandler
{
    public function __construct()
    {
    }
    public function __invoke(LogHello $message)
    {
    }
}
```

👉 Bu kod, mesaj işleyici (handler) sınıfını oluşturur.

Şimdi `LogHello` içine `public int $length` içeren bir kurucu ekleyin:

```php
// src/Message/LogHello.php

// ... lines 1 - 2
namespace App\Message;
final class LogHello
{
    public function __construct(public int $length)
    {
    }
}
```

👉 Bu kod, mesaja `length` parametresi ekler.

Handler’da da kurucuya `LoggerInterface $logger`’ı autowire edin:

```php 
// src/MessageHandler/LogHelloHandler.php

// ... lines 1 - 5
use Psr\Log\LoggerInterface;
// ... lines 7 - 9
final class LogHelloHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }
// ... lines 15 - 19
}
```

👉 Bu kodda logger, handler’a dependency injection ile eklenir.

Metotta ise `$this->logger->warning()` ile logları kolayca görebilmek için, `str_repeat()` ile gitar ikonunu `$message->length` kadar log’a yazın. Sonunda da bu sayıyı log’layın:

```php
// src/MessageHandler/LogHelloHandler.php

// ... lines 1 - 5
use Psr\Log\LoggerInterface;
// ... lines 7 - 9
final class LogHelloHandler
{
    public function __construct(private LoggerInterface $logger)
    {
    }
    public function __invoke(LogHello $message)
    {
        $this->logger->warning(str_repeat('🎸', $message->length).' '.$message->length);
    }
}
```

👉 Bu kod, mesaj geldiğinde belirlenen sayıda gitar ikonu ve uzunluk bilgisini loglar.

Mesaj ve handler hazır!

## ⏲️ Creating the Schedule / Takvim (Schedule) Oluşturma

Şimdi Symfony’ye şunu söyleyeceğiz:

Ben yine geldim. Lütfen her 7 günde bir Messenger üzerinden bir `LogHello` mesajı gönder.

Biz burada örnek olması için her birkaç saniyede bir tetikleyelim.

`src/` dizininde (yapmak zorunda değilsiniz ama) bir `Scheduler` klasörü oluşturun. İçine de `MainSchedule` adlı bir PHP sınıfı yazın. Bunu `ScheduleProviderInterface`’i implemente ederek yazın:

```php
// src/Scheduler/MainSchedule.php

// ... lines 1 - 2
namespace App\Scheduler;
// ... lines 4 - 6
use Symfony\Component\Scheduler\ScheduleProviderInterface;
// ... lines 8 - 9
class MainSchedule implements ScheduleProviderInterface
{
// ... lines 12 - 14
}
```

👉 Bu sınıf, Scheduler için takvim sağlayıcı olarak görev yapar.

Birden fazla schedule sağlayıcı tanımlayabilirsiniz... veya tüm tekrarlayan mesajlarınızı tek bir sınıfta toplayabilirsiniz.

Ayrıca bu sınıfa bir `#[AsSchedule]` özniteliği ekleyin. Bunun bir opsiyonel argümanı var: takvim adı. Varsayılan olarak `default` gelir. Burada da `default` kullanalım:

```php 
// src/Scheduler/MainSchedule.php

// ... lines 1 - 2
namespace App\Scheduler;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
// ... line 6
use Symfony\Component\Scheduler\ScheduleProviderInterface;
// ... line 8
#[AsSchedule]
class MainSchedule implements ScheduleProviderInterface
{
// ... lines 12 - 14
}
```

👉 Bu öznitelik, sınıfı bir takvim sağlayıcı olarak işaretler.

Şimdi tek ihtiyacımız olan metodu, `getSchedule()` metodunu implemente edin:

```php
// src/Scheduler/MainSchedule.php

// ... lines 1 - 2
namespace App\Scheduler;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
#[AsSchedule]
class MainSchedule implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
    }
}
```

👉 Bu kod, takvimi döndüren metodu içerir.

Buradaki kod oldukça basit ve anlaşılır. Yeni bir `Schedule()` döndürüp, buna ->add() ile zamanlanacak işleri ekliyorsunuz. Her “tekrarlanan” iş için `RecurringMessage::`. Birden fazla yöntemi var; en kolay olanı `every()` ile “her 7 gün” veya “her 5 dakika” diyebilirsiniz. Ayrıca cron sözdizimi de geçebilirsiniz.

Biz `every()` ile 4 saniye kullanalım. Her 4 saniyede bir, yeni bir `LogHello(4)` mesajı Messenger’a gönderilsin. Aynısını 3 saniye için de kopyalayın.

```php
// src/Scheduler/MainSchedule.php

// ... lines 1 - 4
use App\Message\LogHello;
// ... line 6
use Symfony\Component\Scheduler\RecurringMessage;
// ... lines 8 - 11
class MainSchedule implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::every('4 seconds', new LogHello(4)),
            RecurringMessage::every('3 seconds', new LogHello(3)),
        );
    }
}
```

👉 Bu kod, iki adet tekrarlanan mesajı takvime ekler.

Hepsi bu kadar!

## 🏃 Consuming the Scheduler Transport / Scheduler Transport’unu Tüketmek

Bir takvim sağlayıcı oluşturduğunuzda, yeni bir Messenger transport’u oluşur. Bu tekrarlayan mesajların işlenmesi için, çalışan bir worker’a ihtiyacınız var: `messenger:consume` komutu.

Terminalde, handler’dan gelen log mesajlarını görebilmek için `-v` ile çalıştırın. Sonra, yeni otomatik eklenen transport’un adını verin: `scheduler_default`... Buradaki `default`, `#[AsSchedule]` özniteliğinde kullandığımız isim.

```shell
php bin/console messenger:consume -v scheduler_default
```

👉 Bu komut, takvimdeki tekrarlanan mesajları işler ve loglara yansıtır.

3 saniye kadar bekleyin... İşte orada! Dört! Sonra üç tekrar çıkar ve dört, sonra üç. 12 saniye sonra, neredeyse aynı anda çalışacaklardır.

Çalışıyor! Harika!

## ⚙️ How does Scheduler Work? / Scheduler Nasıl Çalışır?

Nasıl çalışıyor? Worker komutu başladığında, tüm `RecurringMessage`’leri dolaşır, her birinin bir sonraki çalıştırılma zamanını hesaplar ve “heap” adı verilen bir liste oluşturur. Sonra sonsuz bir döngüye girer. Mevcut zaman, listedeki bir sonraki mesajın zamanına geldiğinde veya geçtiğinde, o mesajı alıp Messenger’a yollar. Sonra tekrar bir sonraki zamanı hesaplar ve heap’e ekler.

Ve bu işlem... sonsuza kadar devam eder.

## 🗄️ Make your Schedule Stateful / Takvimi Durumlu (Stateful) Hale Getirin

Fakat burada bir problem var: Komutu yeniden başlatırsak, takvim baştan oluşturulur. Yani mesajların tekrar 3 ve 4 saniye beklemesi gerekir.

Gerçek bir uygulamada, bu sorun yaratır. Mesajınız 7 günde bir çalışıyorsa ve worker komutu 5 gün sonra kapanıp tekrar başlatılırsa, mesaj 7 gün sonra tekrar çalışır: yani 12. günde. Sürekli yeniden başlatılırsa, mesaj hiç çalışmayabilir!

Çözüm: Takvimi durumlu (stateful) yapmak. Bu da kolay. Bir `__construct` metodu oluşturun ve Symfony cache’den bir `CacheInterface` autowire edin:

```php
// src/Scheduler/MainSchedule.php

// ... lines 1 - 9
use Symfony\Contracts\Cache\CacheInterface;
// ... lines 11 - 12
class MainSchedule implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    )
    {
    }
// ... lines 21 - 29
}
```

👉 Bu kod, önbellek servisini takvim sağlayıcıya ekler.

Aşağıda, `->stateful($this->cache)` çağırın:

```php
// src/Scheduler/MainSchedule.php

// ... lines 1 - 9
use Symfony\Contracts\Cache\CacheInterface;
// ... lines 11 - 12
class MainSchedule implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
    )
    {
    }
// ... line 21
    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
// ... lines 25 - 26
        )
            ->stateful($this->cache);
    }
}
```

👉 Bu kod, takvimi durumlu yapar ve geçmişi kaydeder.

Ayrıca, `services.yaml` dosyasını açın. Önceki bir eğitimde, `dev` ortamında önbelleği fiilen devre dışı bırakan bir yapılandırma eklemiştik. Onu kaldırın ki düzgün bir önbelleğimiz olsun.

Worker’ı durdurup tekrar başlatın. İlk başlatmada yine 3 ve 4 saniye bekleyecek. Ama şimdi tekrar durdurup birkaç saniye bekleyip tekrar başlatırsanız, anında yakalar! Mesajlar hemen işlenir.

State, Scheduler’ın mesajları en son ne zaman kontrol ettiğini takip eder. Yani worker bir süre kapanırsa, yeniden başlatıldığında o zamanı okur ve oradan başlar, böylece kaçırılan mesajların hepsini yakalar.

Bu, bazı mesajların tekrar tekrar çalışmasına sebep olabilir ama hiçbir mesajı kaçırmaz.

## 🔒 Multiple Workers: Lock your Schedule / Birden Fazla Worker: Takvime Kilit Ekleyin

Ve eğer scheduler transport’u için birden fazla worker çalıştırmayı planlıyorsanız, takvime bir kilit eklemeniz gerekir. Bu kolay, belgelerde anlatılıyor: Lock Factory’yi autowire edin, ardından `->lock()` ile yeni bir kilit verin. Böylece iki worker aynı anda aynı mesajı alıp işlemez.

Hepsi bu kadar! Eğer yükseltme ile ilgili sorunuz olursa veya burada değinmediğimiz bir sorunla karşılaşırsanız, yorumlara yazabilirsiniz. Başarı hikayelerinizi de duymak isteriz!

Görüşmek üzere!
