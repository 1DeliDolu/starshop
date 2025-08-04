# 🧪 Test for CLI Command / CLI Komutu için Test

Kaptan, roketin peşinden koşan ve geç kalan insanlardan bıktı! Bu yüzden hatırlatma e-postası gönderen bir komut oluşturduk! Sorun çözüldü! Şimdi bunun çalışmaya devam ettiğinden emin olmak için bir test yazalım. "Yeni özellik, yeni test", benim sloganım bu!

Terminalde şunu çalıştırın:

```shell
symfony console make:test
```

👉 Bu komut yeni bir test sınıfı oluşturur.

Tür olarak `KernelTestCase` girin. Ad olarak `SendBookingRemindersCommandTest` yazın.

IDE'mizde, yeni sınıf `tests/` klasörüne eklendi. Dosyayı açın ve sınıfı yeni bir isim alanına taşıyın: `App\Tests\Functional\Command`, böylece her şey düzenli olur.

İlk olarak içeriği temizleyin ve bazı davranış trait'leri ekleyin: `use ResetDatabase, Factories, InteractsWithMailer`:



```php
// tests/Functional/Command/SendBookingRemindersCommandTest.php
// ... lines 1 - 9
class SendBookingRemindersCommandTest extends KernelTestCase
{
    use ResetDatabase, Factories, InteractsWithMailer;
// ... lines 13 - 22
}
```

👉 Bu trait'ler test ortamını yönetir ve kolaylıklar sunar.

İki test için taslak oluşturun: `public function testNoRemindersSent()` ve `$this->markTestIncomplete()`, ayrıca `public function testRemindersSent()`. Bunu da eksik olarak işaretleyin:



```php
// tests/Functional/Command/SendBookingRemindersCommandTest.php
// ... lines 1 - 9
class SendBookingRemindersCommandTest extends KernelTestCase
{
// ... lines 12 - 13
    public function testNoRemindersSent()
    {
        $this->markTestIncomplete();
    }
    public function testRemindersSent()
    {
        $this->markTestIncomplete();
    }
}
```

👉 Bu, test fonksiyonlarını şimdilik eksik olarak bırakır.

Terminalde testleri çalıştırın:

```shell
bin/phpunit
```

👉 Bu komut testleri çalıştırır.

İki eski test geçiyor, iki tane de yeni eksik test var. Bu modeli seviyorum: yeni özellik için test taslakları yaz, sonra eksikleri teker teker tamamlayarak bitir.

Symfony, komutları test etmek için hazır bazı araçlar sağlar. Ancak ben, bunları daha kolay hale getiren bir paket kullanmayı seviyorum. Şunu kurun:

```shell
composer require --dev zenstruck/console-test
```

👉 Bu paket, komut testlerini kolaylaştırır.

Bu paketin yardımcılarını etkinleştirmek için teste yeni bir davranış trait'i ekleyin: `InteractsWithConsole`:



```php
// tests/Functional/Command/SendBookingRemindersCommandTest.php
// ... lines 1 - 10
class SendBookingRemindersCommandTest extends KernelTestCase
{
    use ResetDatabase, Factories, InteractsWithMailer, InteractsWithConsole;
// ... lines 14 - 26
}
```

👉 Konsol komutlarıyla etkileşime geçmenizi sağlar.

Artık eksikleri kaldırmaya hazırız!

## testNoRemindersSent()

İlk test kolay: hatırlatılacak rezervasyon olmadığında komutun e-posta göndermediğinden emin olmak istiyoruz. Şunu yazın: `$this->executeConsoleCommand()` ve sadece komut adı: `app:send-booking-reminders`. Komutun başarıyla çalıştığını ve çıktıda `'Sent 0 booking reminders'` geçtiğini kontrol edin:



```php
//tests/Functional/Command/SendBookingRemindersCommandTest.php
// ... lines 1 - 10
class SendBookingRemindersCommandTest extends KernelTestCase
{
// ... lines 13 - 14
    public function testNoRemindersSent()
    {
        $this->executeConsoleCommand('app:send-booking-reminders')
            ->assertSuccessful()
            ->assertOutputContains('Sent 0 booking reminders')
        ;
    }
// ... lines 22 - 26
}
```

👉 Hatırlatma gerekmeyen durumda komutun doğru davrandığı test edilir.

## testRemindersSent()

**Arrange (Hazırlık)**

Sıradaki test biraz daha ayrıntılı: Hatırlatma için uygun bir rezervasyon oluşturmalıyız. Şu şekilde rezervasyon sabiti oluşturun:



```php
// tests/Functional/Command/SendBookingRemindersCommandTest.php
// ... lines 1 - 14
class SendBookingRemindersCommandTest extends KernelTestCase
{
// ... lines 17 - 26
    public function testRemindersSent()
    {
        $booking = BookingFactory::createOne([
            'trip' => TripFactory::new([
                'name' => 'Visit Mars',
                'slug' => 'iss',
            ]),
            'customer' => CustomerFactory::new(['email' => 'steve@minecraft.com']),
            'date' => new \DateTimeImmutable('+4 days'),
        ]);
// ... lines 37 - 56
    }
}
```

👉 Veritabanında hatırlatma gerektiren bir rezervasyon oluşturur.

**Pre-Assertion (Ön Doğrulama)**

Bu rezervasyonun henüz hatırlatma almadığından emin olun:



```php
// tests/Functional/Command/SendBookingRemindersCommandTest.php
// ... lines 1 - 14
class SendBookingRemindersCommandTest extends KernelTestCase
{
// ... lines 17 - 26
    public function testRemindersSent()
    {
// ... lines 29 - 37
        $this->assertNull($booking->getReminderSentAt());
// ... lines 39 - 56
    }
}
```

👉 Hatırlatma tarihi henüz atanmadıysa test geçer.

**Act (Aksiyon)**

Şimdi aksiyon zamanı:


```php
// tests/Functional/Command/SendBookingRemindersCommandTest.php
// ... lines 1 - 14
class SendBookingRemindersCommandTest extends KernelTestCase
{
// ... lines 17 - 39
        $this->executeConsoleCommand('app:send-booking-reminders')
            ->assertSuccessful()
            ->assertOutputContains('Sent 1 booking reminders')
        ;
// ... lines 44 - 56
    }
}
```

👉 Komut çalıştırılır ve doğru çıktı doğrulanır.

**Assert (Doğrulama)**

Şimdi, e-posta gönderildi mi ona bakalım:



```php
// tests/Functional/Command/SendBookingRemindersCommandTest.php
// ... lines 1 - 14
class SendBookingRemindersCommandTest extends KernelTestCase
{
// ... lines 17 - 44
        $this->mailer()
            ->assertSentEmailCount(1)
            ->assertEmailSentTo('steve@minecraft.com', function(TestEmail $email) {
                $email
                    ->assertSubject('Booking Reminder for Visit Mars')
                    ->assertContains('Visit Mars')
                    ->assertContains('/booking/'.BookingFactory::first()->getUid())
                ;
            })
        ;
// ... lines 55 - 56
    }
}
```

👉 Gönderilen e-posta sayısı, alıcı, konu ve içerik doğrulanır.

Son olarak, komutun veritabanındaki rezervasyonu güncellediğini doğrulayın:



```php
// tests/Functional/Command/SendBookingRemindersCommandTest.php
// ... lines 1 - 14
class SendBookingRemindersCommandTest extends KernelTestCase
{
// ... lines 17 - 55
        $this->assertNotNull($booking->getReminderSentAt());
    }
}
```

👉 Hatırlatma tarihi artık atanmış olmalı.

## Son Adım

Doğruluk anı! Testleri çalıştırın:

```shell
bin/phpunit
```

👉 Tüm testler başarılı olursa, özellik güvenle çalışıyor demektir.

---

Dıştan-içe testler (outside-in) yazmak gerçekten eğlenceli ve kolay, çünkü iç mantığı test etmeye fazla takılmadan uygulamanın dışarıdan nasıl davrandığına odaklanılır. Bu yaklaşımda kontroller, kullanıcının göreceği şeylere ve yüksek seviyeli doğrulamalara odaklanır.

Artık hem e-posta gönderme yolları için testlerimiz var, güvenle refaktör edip tekrarı kaldırabiliriz!
