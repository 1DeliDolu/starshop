# 🖥️ Bonus: Messenger Monitor Bundle / Bonus: Messenger Monitor Paketi

Hâlâ buradasın! Harika! Son bir bonus bölüm daha yapalım!

Arka planda bir sürü mesaj ve zamanlayıcı çalışırken neler olup bittiğini takip etmek zor olabilir. İşçilerim çalışıyor mu? Zamanlayıcım çalışıyor mu? Nerede çalışıyor? Başarısızlıklar ne durumda? Elimizde loglar var ama... loglar. Bunun yerine, çalışan robot ordumuzun neler yaptığını görmemizi sağlayan bir arayüz sunan harika bir pakete bakalım!

## 📦 Installation / Kurulum

Terminalde şunu çalıştır:

```shell
composer require zenstruck/messenger-monitor-bundle
```

👉 Bu komut, `zenstruck/messenger-monitor-bundle` paketini projeye ekler.

Bir tarif yüklemek isteyecek, evet deyin. IDE'ye geri dönün ve neler eklendiğine bakın.

İlk olarak, bir `src/Schedule.php` eklendi. Bu dosya bu paketle ilgili değil. Önceki bölümde eklediğimiz Symfony Scheduler'ın resmi tarifi artık varsayılan bir zamanlayıcı ekliyor. Zaten bir zamanlayıcınız olduğu için bu dosyayı silebilirsiniz.

## 🕹️ MessengerMonitorController

Yeni bir denetleyici eklendi: `src/Controller/Admin/MessengerMonitorController.php`. Bu, paketin arayüzünü etkinleştirmek için bir taslak. Paketten `BaseMessengerMonitorController` sınıfını genişletir ve `/admin/messenger` rotasını ekler. Ayrıca `#[IsGranted('ROLE_ADMIN')]` özniteliğini ekler. Gerçek uygulamalar için bu çok önemlidir; arayüz hassas bilgiler gösterdiği için yalnızca site yöneticileri erişebilmelidir. Bu uygulamada güvenlik yapılandırılmadığından, bu satırı kaldırabilirsiniz:


```php
// src/Controller/Admin/MessengerMonitorController.php
// ... lines 1 - 7
#[Route('/admin/messenger')]
class MessengerMonitorController extends BaseMessengerMonitorController
{
}
```

👉 Bu denetleyici, admin için Messenger Monitor arayüzünü etkinleştirir.

## 📨 ProcessedMessage

`src/Entity/ProcessedMessage.php` yeni bir varlık olarak eklendi. Bu da bir taslak; `BaseProcessedMessage` sınıfını genişletir ve bir ID sütunu ekler. Bu, messenger mesajlarınızın geçmişini izlemek için kullanılır. Her işlenen mesaj için bu varlıktan bir tane kaydedilir. Endişelenmeyin, bu işçi sürecinde yapılır, uygulamanızın ön yüzünü yavaşlatmaz.

Yeni bir varlık olduğu için bir migration eklemeliyiz, ama bu projede migration yapılandırılmamış. Terminalde şunu çalıştırın:

```shell
symfony console doctrine:schema:update --force
```

👉 Bu komut, veritabanı şemasını günceller.

## ➕ Install Optional Dependencies / İsteğe Bağlı Bağımlılıkları Kur

Arayüze bakmadan önce, paketin iki isteğe bağlı bağımlılığı var, bunları kurmak istiyorum. Önce:

```shell
composer require knplabs/knp-time-bundle
```

👉 Bu paket, zaman damgalarını insan tarafından okunabilir hale getirir — örneğin "4 dakika önce".

Sonra:

```shell
composer require lorisleiva/cron-translator
```

👉 Bu paket, zamanlanmış görevlerimiz için cron ifadelerini insan tarafından okunabilir hale getirir. Yani "11 2 \* \* \*" yerine "her gün saat 2:11'de" gibi gösterir.

Her şey hazır! Sunucuyu başlatın:

```shell
symfony serve -d
```

👉 Bu komut, Symfony web sunucusunu arka planda başlatır.

## 📊 Dashboard

Tarayıcıda `/admin/messenger` adresine gidin. Burası Messenger Monitor kontrol paneli!

İlk widget, çalışan işçileri ve durumlarını gösterir. Async taşıyıcımız için bir işçi çalışıyor. Bunu Symfony CLI sunucusu ile yapılandırdık.

Aşağıda, mevcut taşıyıcılarımızı, kuyrukta kaç mesaj olduğunu ve kaç işçinin onları çalıştırdığını görebiliriz. `scheduler_default` taşıyıcısının çalışmadığını görebilirsiniz, bu normal; çünkü yerelde çalışması için yapılandırmadık.

Daha aşağıda, son 24 saatin istatistiklerinin bir anlık görüntüsünü bulabilirsiniz.

Sağda ise, son 15 işlenmiş mesaj görüntülenir (şu anda boş olabilir).

Tüm bu widget'lar her 5 saniyede bir otomatik olarak yenilenir.

## ⏰ Schedule / Zamanlayıcı

Şimdi biraz geçmiş oluşturalım! Üstteki barda "Schedule" tıklayın (ikonun kırmızı olması zamanlayıcının çalışmadığını gösterir). Burası "daha gelişmiş bir debug\:schedule komutu" gibi. Tek zamanlanmış görevimizi görebilirsiniz: `RunCommandMessage` ile `app:send-booking-reminders`. Cron ifadesiyle her gün saat 2:11'de çalışacak şekilde ayarlanmış. Henüz hiç çalışmamış, ama "Trigger" tıklayarak manuel olarak çalıştırabilirsiniz... ve async taşıyıcıyı seçin.

## 🔎 "Details" / "Detaylar"

Tekrar kontrol paneline dönün. Görev başarılı şekilde çalıştı, 58ms sürdü ve 31MB bellek kullandı. "Details" tıklayın, daha fazla bilgi görebilirsiniz! "Time in Queue", "Time to Handle", zaman damgaları...

Etiketler, mesajları filtrelemek için çok faydalı. Kendi etiketlerinizi ekleyebilirsiniz ama bazıları paket tarafından otomatik eklenir: `manual` (manüel çalıştırıldı), `schedule` (zamanlanmış görev), `schedule:default` (varsayılan takvim parçası), `schedule:default:<hash>` (bu zamanlanmış görevin benzersiz kimliği).

Sağda, mesaj "handler"ının sonucu görünür — burada `RunCommandMessageHandler`. Farklı handler'lar farklı sonuçlar döndürebilir. Bu handler için sonuç, komutun çıkış kodu ve çıktısıdır.

Örneğin:
Sent 0 booking reminders

Bu görevi bir de hatırlatma gönderilmesi gereken bir rezervasyon varken çalıştıralım. Terminalde fixture'ları tekrar yükleyin:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, test verilerini tekrar yükler ve mesaj geçmişini temizler.

Tarayıcıya dönün. Dashboard şu an boş olabilir; fixture'ları tekrar yüklemek mesaj geçmişini de temizler. "Schedule" tıklayın, "async" taşıyıcısında "Trigger" tıklayın.

Dashboard'da şimdi 2 mesaj görmelisiniz. Yine `RunCommandMessage`, "Details" tıklayın:

Örneğin:
Sent 1 booking reminders

Şimdi ikinci mesaj: `SendEmailMessage`. Bu, komut tarafından tetiklendi. "Details" tıklayın; burada e-posta ile ilgili sonuçları görebilirsiniz. Etiket olarak `booking_reminder` göreceksiniz. Paket, bir e-posta gönderildiğini ve "Mailer" etiketiyle birlikte otomatik olarak ekledi.

## 🚚 Transports

Üst menüden "Transports" tıklayarak, her bir taşıyıcıda bekleyen mesajlar hakkında daha fazla bilgi görebilirsiniz (uygunsa). `failed` taşıyıcısı başarısız mesajları gösterir ve doğrudan arayüzden yeniden deneme veya silme imkanı sunar!

## 🕰️ History / Geçmiş

"History" bölümünde, mesajları filtreleyebilirsiniz: zaman aralığına, belirli bir taşıyıcıya, duruma (başarılı/başarısız), zamanlanmış görevlere, mesaj tipine göre.

## 📈 Statistics / İstatistikler

"Statistics" bölümü, mesaj sınıfına göre özet istatistikleri gösterir ve belirli bir zaman aralığına göre sınırlandırılabilir.

## 🧹 Purge Message History / Mesaj Geçmişini Temizleme

Uygulamanız çok fazla mesaj işliyorsa, geçmiş tablonuz büyüyebilir. Paket, eski mesajları temizlemek için komutlar sunar.

Dokümantasyonda "messenger\:monitor\:purge" komutunu bulun ve kopyalayın. Bunu zamanlamak için ne yapacağız? Elbette Symfony Scheduler ile! `src/Scheduler/MainSchedule.php` dosyasını açın, yeni bir görev ekleyin; `->add(RecurringMessage::cron())`. `#midnight` kullanın, böylece her gün gece yarısı ile 03:00 arasında çalışsın. `RunCommandMessage` ile komutu ekleyin ve `--exclude-schedules` seçeneğini unutmayın:


```php
// src/Scheduler/MainSchedule.php
// ... lines 1 - 12
final class MainSchedule implements ScheduleProviderInterface
{
// ... lines 15 - 19
    public function getSchedule(): Schedule
    {
        return (new Schedule())
// ... lines 23 - 24
            ->add(RecurringMessage::cron(
                    '#midnight',
                    new RunCommandMessage('messenger:monitor:purge --exclude-schedules'),
                )
            )
// ... lines 30 - 34
        ;
    }
}
```

👉 Bu görev, takvimle tetiklenmeyen 30 günden eski mesajları temizler.

## 🧹 Purge Schedule History / Zamanlayıcı Geçmişini Temizleme

Bunları da temizlemeliyiz. Dokümantasyondan ikinci komutu kopyalayın: `messenger:monitor:schedule:purge`. Zamanlamada yine `->add(RecurringMessage::cron('#midnight', new RunCommandMessage()))` ile ekleyin:


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
            ->add(RecurringMessage::cron(
                    '#midnight',
                    new RunCommandMessage('messenger:monitor:schedule:purge'),
                )
            )
        ;
    }
}
```

👉 Bu görev, yukarıdaki komutla atlanan zamanlanmış mesajların geçmişini temizler ama her birinden son 10 çalıştırmayı saklar.

Bu görevlerin zamanlamaya eklendiğinden emin olun. Tarayıcıda "Schedule" tıklayın, iki yeni görevinizi göreceksiniz.

Önceden elle çalıştırdığınız görev için son çalıştırma özetini, detaylarını ve hatta geçmişini görebilirsiniz.

Hepsi bu kadar! İşte zenstruck/messenger-monitor-bundle'ın hızlı bir özeti. Tüm özellikler için dokümantasyona göz atabilirsin.

Bir sonraki sefere kadar, iyi izlemeler!
