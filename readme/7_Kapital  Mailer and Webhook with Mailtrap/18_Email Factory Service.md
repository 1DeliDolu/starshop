# 🏭 BookingEmailFactory / BookingEmailFactory

Uygulamamız iki e-posta gönderiyor: biri `SendBookingRemindersCommand` içinde, diğeri ise `TripController::show()` içinde. Burada çok fazla tekrar var. Gözlerimi acıtıyor! Ama sorun yok! Bunu bir e-posta fabrikası servisiyle yeniden düzenleyebiliriz. Üstelik, her iki e-posta yolunu kapsayan testlerimiz olduğu için gönül rahatlığıyla refaktör edebiliriz. Testleri çok seviyorum!

`BookingEmailFactory` adında yeni bir sınıf oluşturun ve `App\Email` isim alanına ekleyin. Yapıcı metoduna, `TripController::show()`'dan `$termsPath` argümanını kopyalayıp buraya yapıştırın ve özel bir özellik olarak tanımlayın:



```php
// src/Email/BookingEmailFactory.php
// ... lines 1 - 11
class BookingEmailFactory
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/assets/terms-of-service.pdf')]
        private string $termsPath,
    ) {
    }
// ... lines 19 - 54
}
```

👉 Burada, PDF dosya yolu dependency injection ile alınır.

Şimdi iki fabrika metodu ekleyin: `public function createBookingConfirmation()`, bu metod `Booking $booking` alır ve `TemplatedEmail` döner. Ayrıca, `public function createBookingReminder(Booking $booking)` de ekleyin:



```php
// src/Email/BookingEmailFactory.php
// ... lines 1 - 11
class BookingEmailFactory
{
// ... lines 14 - 19
    public function createBookingConfirmation(Booking $booking): TemplatedEmail
    {
// ... lines 22 - 25
    }
// ... line 27
    public function createBookingReminder(Booking $booking): TemplatedEmail
    {
// ... lines 30 - 33
    }
// ... lines 35 - 54
}
```

Tekrarlanan mantığı barındıracak özel bir metod oluşturun: `private function createEmail(Booking $booking, string $tag): TemplatedEmail`:



```php
// src/Email/BookingEmailFactory.php
// ... lines 1 - 11
class BookingEmailFactory
{
// ... lines 14 - 35
    private function createEmail(Booking $booking, string $tag): TemplatedEmail
    {
        $customer = $booking->getCustomer();
        $trip = $booking->getTrip();
        $email = (new TemplatedEmail())
            ->to(new Address($customer->getEmail()))
            ->context([
                'customer' => $customer,
                'trip' => $trip,
                'booking' => $booking,
            ])
        ;
        $email->getHeaders()->add(new TagHeader($tag));
        $email->getHeaders()->add(new MetadataHeader('booking_uid', $booking->getUid()));
        $email->getHeaders()->add(new MetadataHeader('customer_uid', $customer->getUid()));
        return $email;
    }
}
```

👉 Ortak mantık `createEmail()` metoduna taşındı.

Şimdi, `createBookingConfirmation()` içinde bunu kullanın. `$booking` ve `'booking'` etiketiyle çağırın. Sonra, başlığı ve şablonu ayarlayın. Son olarak PDF ekini ekleyin:



```php
// src/Email/BookingEmailFactory.php
// ... lines 1 - 11
class BookingEmailFactory
{
// ... lines 14 - 19
    public function createBookingConfirmation(Booking $booking): TemplatedEmail
    {
        return $this->createEmail($booking, 'booking')
            ->subject('Booking Confirmation for '.$booking->getTrip()->getName())
            ->htmlTemplate('email/booking_confirmation.html.twig')
            ->attachFromPath($this->termsPath, 'Terms of Service.pdf')
        ;
    }
// ... lines 28 - 55
}
```

👉 Bu metod, onay e-postasının tüm gereksinimlerini yerine getirir.

`createBookingReminder()` için de aynı mantıkla, etiketi `booking_reminder`, başlığı `Booking Reminder`, şablonu ise `email/booking_reminder.html.twig` olarak ayarlayın:



```php
// src/Email/BookingEmailFactory.php
// ... lines 1 - 28
    public function createBookingReminder(Booking $booking): TemplatedEmail
    {
        return $this->createEmail($booking, 'booking_reminder')
            ->subject('Booking Reminder for '.$booking->getTrip()->getName())
            ->htmlTemplate('email/booking_reminder.html.twig')
        ;
    }
// ... lines 35 - 54
```

👉 Hatırlatma e-postası için özelleştirilmiş metod.

## 🧹 The Refactor / Refaktör

Şimdi eğlenceli kısmı! Fabrikayı kullanıp tekrar eden kodları temizleyelim.

`TripController::show()` içinde, artık `$termsPath` yerine `BookingEmailFactory $emailFactory` enjeksiyonunu kullanın:


```php
// src/Controller/TripController.php
// ... lines 1 - 18
final class TripController extends AbstractController
{
// ... lines 21 - 29
    public function show(
// ... lines 31 - 35
        BookingEmailFactory $emailFactory,
    ): Response {
// ... lines 38 - 58
    }
}
```

Tüm e-posta oluşturma kodunu silin ve `$mailer->send($emailFactory->createBookingConfirmation($booking));` yazın:



```php
// src/Controller/TripController.php
// ... lines 1 - 18
final class TripController extends AbstractController
{
// ... lines 21 - 29
    public function show(
// ... lines 31 - 36
    ): Response {
// ... lines 38 - 39
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 41 - 49
            $mailer->send($emailFactory->createBookingConfirmation($booking));
// ... lines 51 - 52
        }
// ... lines 54 - 58
    }
}
```

Aynı şekilde, `SendBookingRemindersCommand` içinde de, e-posta oluşturma kodunu kaldırın. Yapıcıya `private BookingEmailFactory $emailFactory` enjeksiyonunu ekleyin:



```php
// src/Command/SendBookingRemindersCommand.php
// ... lines 1 - 18
class SendBookingRemindersCommand extends Command
{
    public function __construct(
// ... lines 22 - 24
        private BookingEmailFactory $emailFactory,
    ) {
// ... line 27
    }
// ... lines 29 - 48
}
```

İlgili yerde, e-posta gönderirken şu kodu kullanın:
`$this->mailer->send($this->emailFactory->createBookingReminder($booking));`



```php
// src/Command/SendBookingRemindersCommand.php
// ... lines 1 - 18
class SendBookingRemindersCommand extends Command
{
// ... lines 21 - 29
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
// ... lines 32 - 37
        foreach ($io->progressIterate($bookings) as $booking) {
            $this->mailer->send($this->emailFactory->createBookingReminder($booking));
// ... line 40
        }
// ... lines 42 - 47
    }
}
```

## ✅ Test It / Test Et

Harika, şimdi testleri çalıştırıp bir şeyleri bozup bozmadığımızı kontrol edelim:

```shell
bin/phpunit
```

👉 Testler çalışır durumda mı kontrol edin.

Bir hata çıktı:
Mesajda şu dosya yok: `[Terms of Service.pdf]`.

## 🔧 Fix It / Düzelt

Kolay düzeltme! Refaktör sırasında, onay e-postasına hizmet şartları PDF dosyasını eklemeyi unuttum. Ve müşterilerimiz buna bağımlı!
`BookingEmailFactory::createBookingConfirmation()` metodunda şunu ekleyin:
`->attachFromPath($this->termsPath, 'Terms of Service.pdf')`



```php
// src/Email/BookingEmailFactory.php
// ... lines 1 - 11
class BookingEmailFactory
{
// ... lines 14 - 19
    public function createBookingConfirmation(Booking $booking): TemplatedEmail
    {
        return $this->createEmail($booking, 'booking')
            ->subject('Booking Confirmation for '.$booking->getTrip()->getName())
            ->htmlTemplate('email/booking_confirmation.html.twig')
            ->attachFromPath($this->termsPath, 'Terms of Service.pdf')
        ;
    }
// ... lines 28 - 55
}
```

👉 Artık PDF eki tekrar eklendi.

Testleri tekrar çalıştırın:

```shell
bin/phpunit
```

👉 Tüm testler geçiyor! Başarılı refaktör: Tamam!

---

Sıradaki adımda, Mailtrap'tan gelen e-posta webhook olaylarını tüketmek için iki yeni Symfony bileşenini kullanacağız.
