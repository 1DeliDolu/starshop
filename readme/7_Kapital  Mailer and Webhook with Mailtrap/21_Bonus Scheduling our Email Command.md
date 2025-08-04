# ⏰ Bonus: Scheduling our Email Command / Bonus: E-posta Komutumuzu Zamanlamak

Buradasın, harika! Senin için bir bonus bölümüm var.

Stajyerimiz Hugo, her gece saat 12'de rezervasyon hatırlatma komutunu çalıştırmak için sunucuya giriş yapması gerektiğinden şikayet ediyor. Neden sorun etti anlamıyorum – stajyerlerin işi bu değil mi?!

## 🛠️ Installing Symfony Scheduler / Symfony Scheduler Kurulumu

Ama... daha sağlam olması için, Hugo hasta olursa ya da unutursa bunu otomatikleştirmeliyiz. Bir CRON işi ayarlayabilirdik... ama bu Symfony Scheduler bileşenini kullanmak kadar havalı veya esnek olmaz. Bu iş için mükemmel. Terminalde şu komutu çalıştır:

```shell
composer require scheduler
```

👉 Bu komut, `scheduler` bileşenini projeye ekler.

Symfony Scheduler'ı, Messenger için bir eklenti olarak düşünebilirsin. Kendi özel taşıyıcısı vardır; bir kuyruk yerine, bir işi çalıştırma zamanı olup olmadığını belirler. Her iş veya görev, bir messenger mesajıdır, yani bir mesaj işleyicisine ihtiyacı vardır. Takvimi, herhangi bir messenger taşıyıcısı gibi `messenger:consume` komutuyla tüketirsin.

## 🗓️ make\:schedule

Bir zamanlama oluşturmak için şunu çalıştır:

```shell
symfony console make:schedule
```

👉 Bu komut, bir zamanlama sınıfı oluşturur.

Not:
`symfony/scheduler` artık resmi bir tarife (recipe) sahip, bu da senin için otomatik olarak `src/Schedule.php` dosyasını oluşturur, bu adım artık gerekli değil.

Taşıyıcı adı? Varsayılanı kullan. Takvim adı? Varsayılan: `MainSchedule`. Heyecan verici!

Birden fazla takvime sahip olmak mümkün, fakat çoğu uygulama için tek bir takvim yeterlidir.

## ⚙️ Configuring the Schedule / Zamanlamayı Yapılandırmak

Şuna göz at: `src/Scheduler/MainSchedule.php`. Bu, `ScheduleProviderInterface` arayüzünü uygulayan bir servistir ve `#[AsSchedule]` özniteliği ile işaretlenmiştir. `getSchedule()` metodu, zamanlamayı yapılandırıp görevleri eklediğimiz yerdir.

`->stateful()` ile `$this->cache`'i geçiriyoruz, bu önemli. Takvimi çalıştıran süreç kapanırsa – örneğin messenger işçileri sunucu yeniden başlatılırken durursa – tekrar açıldığında kaçırılan tüm işleri bilir ve onları çalıştırır. Bir görev, süreç kapalıyken 10 defa çalışmalıysa, hepsini çalıştırır. Bu istenmeyebilir, bu yüzden yalnızca sonuncusunu çalıştırmak için `->processOnlyLastMissedRun(true)` ekleyin:


```php
// src/Scheduler/MainSchedule.php
// ... lines 1 - 12
final class MainSchedule implements ScheduleProviderInterface
{
// ... lines 15 - 19
    public function getSchedule(): Schedule
    {
        return (new Schedule())
// ... lines 23 - 29
            ->processOnlyLastMissedRun(true)
        ;
    }
}
```

👉 Bu ayar, sadece kaçırılan son görevi çalıştırır.

Daha karmaşık uygulamalarda, aynı zamanlamayı birden fazla işçiyle tüketiyor olabilirsin. Yalnızca bir işçinin görevi çalıştırması için `->lock()` ile bir kilit yapılandırabilirsin.

## ➕ Adding a Task / Görev Ekleme

İlk görevimizi ekleyelim! `->add()` içine `RecurringMessage::` yaz. Bir görevi tetiklemenin birkaç yolu var. Ben `cron()` kullanmayı seviyorum. Bu görevin her gece 12'de çalışmasını istiyorum, yani `0 0 * * *` kullan. İkinci parametre, gönderilecek messenger mesajı. `SendBookingRemindersCommand`'ı doğrudan ekleyemeyiz. Bunun yerine, `new RunCommandMessage()` ile komut adını geçiriyoruz: `app:send-booking-reminders` (buraya argüman ve seçenekler de ekleyebilirsin):


```php
// src/Scheduler/MainSchedule.php
// ... lines 1 - 12
final class MainSchedule implements ScheduleProviderInterface
{
// ... lines 15 - 19
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::cron(
                    '0 0 * * *',
                    new RunCommandMessage('app:send-booking-reminders')
                )
            )
// ... lines 29 - 30
        ;
    }
}
```

👉 Bu kod, komutu her gece 00:00'da çalıştıracak bir zamanlayıcı görevi ekler.

## 🕵️ Debugging the Schedule / Zamanlamayı Hata Ayıklama

Terminalde zamanlanmış görevleri listelemek için şunu çalıştır:

```shell
symfony console debug:schedule
```

👉 Bu komut, tanımlı zamanlanmış görevleri listeler.

Bir hata aldık:

You cannot use "CronExpressionTrigger" as the "cron expression" package is not installed

Kolay çözüm: Komutu kopyala ve çalıştır:

```shell
composer require dragonmantank/cron-expression
```

👉 Bu komut, cron ifadeleri için gerekli paketi kurar.

Şimdi tekrar debug komutunu çalıştır:

```shell
symfony console debug:schedule
```

👉 Artık cron ifadesini, mesajı (ve komutu) ve sonraki çalışma zamanını görebilirsin.

## 🏷️ #\[AsCronTask]

Komutları zamanlamak için alternatif bir yol daha var. `MainSchedule::getSchedule()` içindeki `->add()`'ı sil. Sonra `SendBookingRemindersCommand`'a başka bir öznitelik ekle: `#[AsCronTask()]`, içine `0 0 * * *` değerini yaz:


```php
// src/Command/SendBookingRemindersCommand.php
// ... lines 1 - 19
#[AsCronTask('0 0 * * *')]
class SendBookingRemindersCommand extends Command
// ... lines 22 - 52
```

👉 Bu öznitelik, komutun her gece 00:00'da otomatik olarak çalıştırılmasını sağlar.

Terminalde tekrar şu komutla zamanlamayı kontrol et:

```shell
symfony console debug:schedule
```

👉 Komutun zamanlandığını göreceksin.

## #️⃣ Hashed Cron Expressions / Hash'li Cron İfadeleri

Birçok görevin aynı anda, örneğin gece yarısı çalışmasını istemiyorsan, zamanlamalarını yaymak iyi olur. Tüm cron ifadelerini manuel olarak farklı yapmak sıkıcı. Bunun yerine hash'li cron ifadesi kullanabilirsin. İfadendeki 0'ların yerine `#` yaz. `#`, o kısmı için rastgele, geçerli bir değer seçmek anlamına gelir:


```php
// src/Command/SendBookingRemindersCommand.php
// ... lines 1 - 19
#[AsCronTask('# # * * *')]
class SendBookingRemindersCommand extends Command
// ... lines 22 - 52
```

👉 Bu ifade, komutun her gün farklı bir saatte çalışmasını sağlar.

Zamanlamayı tekrar debug et:

```shell
symfony console debug:schedule
```

Artık örneğin sabah 05:11'de çalışacağını görebilirsin. Gerçekten rastgele değil; değerler mesaj detaylarına göre deterministik olarak hesaplanır. Farklı bir komut, aynı hash ifadeyle farklı bir zaman alır.

Scheduler dokümantasyonunda tüm detaylar mevcut. Sık kullanılan hash'ler için kısayollar da var. Örneğin, `#midnight` ifadesi gece yarısı ile 03:00 arasında rastgele bir saat seçer. Bunu da kullanabilirsin:


```php
// src/Command/SendBookingRemindersCommand.php
// ... lines 1 - 19
#[AsCronTask('#midnight')]
class SendBookingRemindersCommand extends Command
// ... lines 22 - 52
```

👉 Bu ifade, görevin gece yarısı ile 03:00 arasında bir zamanda çalışmasını sağlar.

Tekrar debug et:

```shell
symfony console debug:schedule
```

Şimdi her gün saat 02:11'de çalışacak şekilde zamanlandı. Harika!

## ▶️ Running the Schedule / Zamanlamayı Çalıştırmak

Zamanlamamızı yapılandırdık, peki nasıl çalıştıracağız? Zamanlamalar, Messenger taşıyıcılarıdır. Taşıyıcı adı `scheduler_<schedule_name>`, bu durumda `scheduler_default`. Şu komutla çalıştır:

```shell
symfony console messenger:consume scheduler_default
```

👉 Bu komut, zamanlanmış görevleri arka planda çalıştırır.

Üretim sunucunda, bu komutu arka planda çalışan normal bir messenger işçisi gibi ayarlayabilirsin.

İşte bu kadar! Scheduler bileşeniyle ilgili hızlı bir özet. Daha fazlası için dokümantasyona göz atabilirsin!

İyi kodlamalar ve iyi zamanlamalar!
