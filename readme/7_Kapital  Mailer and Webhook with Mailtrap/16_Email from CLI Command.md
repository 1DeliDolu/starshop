# 📧 Reminder Email Template / Hatırlatma E-Posta Şablonu

Hatırlatma e-posta özelliğimiz için ön hazırlıkları yaptık. Şimdi, gerçekten e-postaları oluşturup gönderelim!

`templates/email` dizininde, yeni e-posta şablonu `booking_confirmation.html.twig` dosyasına çok benzeyecek. O dosyayı kopyalayıp adını `booking_reminder.html.twig` olarak değiştirin. İçeride, çok zaman harcamak istemiyorum, bu yüzden sadece vurgu başlığını "Coming soon!" olarak değiştirin:


```twig
// templates/email/booking_reminder.html.twig
{% extends 'email/layout.html.twig' %}
{% block content %}
    <row>
        <columns>
            <spacer size="40"></spacer>
            <p class="accent-title">Coming soon!</p>
            <h1 class="trip-name">{{ trip.name }}</h1>
            <img
                    class="trip-image float-center"
                    src="{{ email.image('@images/%s.png'|format(trip.slug)) }}"
                    alt="{{ trip.name }}">
        </columns>
    </row>
    <row>
        <columns>
            <p class="accent-title">Departure: {{ booking.date|date('Y-m-d') }}</p>
        </columns>
    </row>
    <row>
        <columns>
            <button class="expanded rounded center" href="{{ url('booking_show', {uid: booking.uid}) }}">
                Manage Booking
            </button>
            <button class="expanded rounded center secondary" href="{{ url('bookings', {uid: customer.uid}) }}">
                My Account
            </button>
        </columns>
    </row>
{% endblock %}
```

👉 Bu dosya, `Coming soon!` başlığını içeren hatırlatma e-postası şablonudur.

## 🛠️ Send Reminder Command / Hatırlatma Gönderme Komutu

E-postaları göndermek için gereken mantık, her saat ya da her gün çalışacak şekilde zamanlanabilecek bir şey olmalı. Bu iş için bir CLI komutu mükemmel! Terminalinizde şu komutu çalıştırın:

```shell
symfony make:command
```

👉 Bu komut yeni bir CLI komutu oluşturur.

Ancak, doğru komut şudur:

```shell
symfony console make:command
```

👉 Bu komut Symfony'de komut üretmek için kullanılır.

Adını şu şekilde belirleyin: `app:send-booking-reminders`.

`src/Command/SendBookingRemindersCommand.php` dosyasını inceleyin. Açıklamasını şu şekilde değiştirin: "Send booking reminder emails".



```php
// src/Command/SendBookingRemindersCommand.php
// ... lines 1 - 17
#[AsCommand(
// ... line 19
    description: 'Send booking reminder emails',
)]
class SendBookingRemindersCommand extends Command
// ... lines 23 - 70
```

👉 Komutun açıklaması hatırlatma e-postalarını göndermek olarak güncellendi.

## 🔗 Constructor Autowiring / Constructor Otomatik Bağlama

Yapıcı metodunda, `BookingRepository`, `EntityManagerInterface` ve `MailerInterface` için otomatik bağımlılık ekleyin ve özellik olarak ayarlayın:



```php
//src/Command/SendBookingRemindersCommand.php
// ... lines 1 - 21
class SendBookingRemindersCommand extends Command
{
    public function __construct(
        private BookingRepository $bookingRepo,
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
    ) {
        parent::__construct();
    }
// ... lines 31 - 68
}
```

👉 Bağımlılıklar yapıcı metoda eklendi.

Bu komutun herhangi bir argümana veya seçeneğe ihtiyacı yok, bu yüzden `configure()` metodunu tamamen kaldırın.

## 🚀 Execute Method / Execute Metodu

`execute()` metodunun içeriğini temizleyin. İlk olarak güzel bir başlık ekleyin: `$io->title('Sending booking reminders')`. Ardından, hatırlatma gönderilmesi gereken rezervasyonları alın: `$bookings = $this->bookingRepo->findBookingsToRemind()`.

En iyi olmak için, rezervasyonlar üzerinde dönerken bir ilerleme çubuğu gösterelim. `$io` nesnesi bunun için bir hileye sahip. Şöyle yazın: `foreach ($io->progressIterate($bookings) as $booking)`. Bu, tüm sıkıcı ilerleme çubuğu mantığını halleder! İçeride, yeni bir e-posta oluşturmalıyız. `TripController`'daki e-postayı (başlıklar dahil) buraya kopyalayın.

Ancak bunu biraz ayarlamalıyız: eki kaldırın. Ve konu için: "Confirmation" yerine "Reminder" yazın. Yukarıda kolaylık olması için bazı değişkenler ekleyin: `$customer = $booking->getCustomer()` ve `$trip = $booking->getTrip()`. Burada aynı meta verileri koruyun, ancak etiketi `booking_reminder` olarak değiştirin. Bu, bu e-postaları Mailtrap'te daha kolay ayırt etmemize yardımcı olacak.

Ayrıca, şablonu `booking_reminder.html.twig` olarak değiştirin.

Hâlâ döngüdeyken, e-postayı şu şekilde gönderin: `$this->mailer->send($email)` ve rezervasyonu hatırlatma gönderildi olarak işaretleyin: `$booking->setReminderSentAt(new \DateTimeImmutable('now'))`.

Mükemmel! Döngü dışında, değişiklikleri veritabanına kaydetmek için `$this->em->flush()` çağırın. Son olarak, şu şekilde kutlayın: `$io->success(sprintf('Sent %d booking reminders', count($bookings)))`.



```php
// src/Command/SendBookingRemindersCommand.php
// ... lines 1 - 21
class SendBookingRemindersCommand extends Command
{
// ... lines 24 - 31
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Sending booking reminders');
        $bookings = $this->bookingRepo->findBookingsToRemind();
        foreach ($io->progressIterate($bookings) as $booking) {
            $trip = $booking->getTrip();
            $customer = $booking->getCustomer();
            $email = (new TemplatedEmail())
                ->to(new Address($customer->getEmail()))
                ->subject('Booking Reminder for '.$trip->getName())
                ->htmlTemplate('email/booking_reminder.html.twig')
                ->context([
                    'customer' => $customer,
                    'trip' => $trip,
                    'booking' => $booking,
                ])
            ;
            $email->getHeaders()->add(new TagHeader('booking_reminder'));
            $email->getHeaders()->add(new MetadataHeader('booking_uid', $booking->getUid()));
            $email->getHeaders()->add(new MetadataHeader('customer_uid', $customer->getUid()));
            $this->mailer->send($email);
            $booking->setReminderSentAt(new \DateTimeImmutable('now'));
        }
        $this->em->flush();
        $io->success(sprintf('Sent %d booking reminders', count($bookings)));
        return Command::SUCCESS;
    }
}
```

👉 Bu kod bloğu, hatırlatma e-postalarını topluca gönderen ve ilgili rezervasyonları güncelleyen komuttur.

## 🧪 Testing Time! / Test Zamanı!

Terminalinize geçin. Hatırlatma gönderilmesi gereken bir rezervasyon olduğundan emin olmak için verileri tekrar yükleyin:

```shell
symfony console doctrine:fixture:load
```

👉 Bu komut, örnek verileri tekrar yükler.

Şimdi yeni komutumuzu çalıştırın!

```shell
symfony console app:send-booking-reminders
```

👉 Bu komut, hatırlatma e-postalarını gönderir.

Harika, 1 hatırlatma gönderildi! Ve çıktı meslektaşlarımızı etkileyecek! Mailtrap'i kontrol etmeden önce, komutu tekrar çalıştırın:

```shell
symfony console app:send-booking-reminders
```

👉 Bu komut ikinci kez çalıştırıldığında artık hatırlatılacak rezervasyon kalmadığını gösterir.

"Sent 0 booking reminders". Mükemmel! Rezervasyonların hatırlatma gönderildi olarak işaretlenmesiyle ilgili mantığımız çalışıyor!

Şimdi Mailtrap'i kontrol edin... işte burada! Beklendiği gibi, onay e-postamıza çok benziyor, fakat burada "Coming soon!" yazıyor: yeni şablon kullanılıyor.

## 🏷️ X-Tag and X-Metadata / X-Tag ve X-Metadata

"Mailtrap Testing" kullanılırken, `Mailer` etiketleri ve meta verileri, üretimde olduğu gibi Mailtrap kategorilerine ve özel değişkenlerine dönüştürülmez. Ancak, hâlâ gönderildiklerinden emin olabilirsiniz! "Tech Info" sekmesine tıklayın ve biraz aşağı kaydırın. `Mailer`, etiketleri ve meta verileri nasıl dönüştüreceğini bilmediğinde, bunları şu genel özel başlıklar olarak ekler: `X-Tag` ve `X-Metadata`.

Gerçekten de, `X-Tag` değeri `booking_reminder`. Harika, beklediğimiz şey de bu!

Tamam, yeni özellik? Tamam! Yeni özellik için test mi? O, sıradaki adım!
