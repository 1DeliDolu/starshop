# 🧪 Emails Assertions in Functional Tests / Fonksiyonel Testlerde E-posta Doğrulamaları

Kod tabanını biraz incelediysen, birinin (herkes olabilir... ama muhtemelen bir Kanadalı) `tests/Functional/` dizinine bazı testler eklediğini fark etmiş olabilirsin. Bunlar geçiyor mu? Bilmiyorum! Hadi birlikte bakalım!

Terminaline geç ve şunu çalıştır:

```bash
bin/phpunit
```

👉 Bu komut, PHPUnit ile testleri çalıştırır.

Bir hata! Hata çünkü gerçekler zamanı, bu testleri ekleyen dost canlısı Kanadalı bendim ve kursun başında bu testlerin geçtiğinden emindim! Hata `BookingTest` içinde, özellikle de `testCreateBooking`'de:

Beklenen yönlendirme durum kodu yerine 500 alındı

`BookingTest` dosyasının 38. satırında. Burada e-posta gönderiyoruz... yani suçlu arıyorsak, önce Kanadalıyı, yani beni ve benim çılgın e-posta gönderme yollarımı sorgulamalıyız.

## 🛠️ Foundry ve Browser / Foundry ve Browser

`BookingTest.php` dosyasını aç. Daha önce Symfony ile fonksiyonel testler yazdıysan, bu biraz farklı görünebilir çünkü bazı yardımcı kütüphaneler kullanıyorum. `zenstruck/foundry` bize bu `ResetDatabase` trait'ini sağlar, bu da her testten önce veritabanını sıfırlar. Ayrıca `Factories` trait'i ile testlerimizde veritabanı fixture'ları oluşturabiliriz. `HasBrowser` ise başka bir paketten - `zenstruck/browser` - ve aslında Symfony'nin test istemcisinin kullanıcı dostu bir sargısıdır.

`testCreateBooking` asıl testtir. Önce, bilinen değerlerle veritabanına bir `Trip` ekliyoruz. Sonra, veritabanında hiç `booking` veya `customer` olmadığından emin olmak için ön-kontrol yapıyoruz. Ardından, `->browser()` ile bir `trip` sayfasına gidiyoruz, rezervasyon formunu doldurup gönderiyoruz. Daha sonra, belirli bir rezervasyon URL'sine yönlendirildiğimizi doğruluyor ve sayfanın bazı beklenen HTML içeriklerine sahip olup olmadığını kontrol ediyoruz. Son olarak, veritabanındaki verilerle ilgili Foundry ile bazı doğrulamalar yapıyoruz.

## 🚩 ->throwExceptions() / ->throwExceptions()

38. satırda hata oluştu... bu rezervasyon sayfasına yönlendirme sırasında 500 yanıt kodu alıyoruz. Testlerde 500 durum kodları sinir bozucu olabilir çünkü gerçek istisnayı bulmak zor olabilir. Neyse ki, `Browser` bize gerçek istisnayı fırlatma imkanı tanır. Bu zincirin başına `->throwExceptions()` ekleyin:

````php
//tests/Functional/BookingTest.php
```php
// ... lines 1 - 12
class BookingTest extends KernelTestCase
{
// ... lines 15 - 19
    public function testCreateBooking(): void
    {
// ... lines 22 - 30
        $this->browser()
            ->throwExceptions()
// ... lines 33 - 42
        ;
// ... lines 44 - 52
    }
}
````

👉 Bu satır, oluşan istisnaların test sırasında atılmasını sağlar.

Terminale dön ve tekrar testleri çalıştır:

```shell
bin/phpunit
```

👉 Bu komut, PHPUnit ile testleri çalıştırır.

Şimdi bir istisna görüyoruz: `Unable to find template "@images/mars.png"`. Hatırlarsan, bu e-posta içine seyahat görsellerini gömmek için kullandığımız şeye benziyor. `mars.png`, `public/imgs` klasöründe olmadığı için hata veriyor. Basit olması için, testimizi mevcut bir görsel kullanacak şekilde ayarlayalım. Fixture'da `mars` yerine `iss` kullan, aşağıda da, `->visit(): /trip/iss` olarak değiştir:

````php
//tests/Functional/BookingTest.php
```php
// ... lines 1 - 12
class BookingTest extends KernelTestCase
{
// ... lines 15 - 19
    public function testCreateBooking(): void
    {
        $trip = TripFactory::createOne([
// ... line 23
            'slug' => 'iss',
// ... line 25
        ]);
// ... lines 27 - 30
        $this->browser()
// ... line 32
            ->visit('/trip/iss')
// ... lines 34 - 42
        ;
// ... lines 44 - 52
    }
}
````

👉 Burada, görseli bulunan bir gezegen kullanılıyor.

Testleri tekrar çalıştır!

```shell
bin/phpunit
```

👉 Bu komut, PHPUnit ile testleri çalıştırır.

Geçti!

Görünüşe göre e-posta gönderiliyor... ama bunu doğrulayalım! Testin sonunda e-posta ile ilgili bazı doğrulamalar eklemek istiyorum. Symfony bunu varsayılan olarak sağlar, fakat ben e-posta fonksiyonel testlerine eğlence katan bir kütüphaneyi tercih ediyorum.

## 📦 zenstruck/mailer-test / zenstruck/mailer-test

Terminalde şunu çalıştır:

```shell
composer require --dev zenstruck/mailer-test
```

👉 Bu komut, ilgili test kütüphanesini kurar.

Kuruldu ve yapılandırıldı... testte etkinleştirmek için `InteractsWithMailer` trait'ini ekleyin:

````php
//tests/Functional/BookingTest.php
```php
// ... lines 1 - 13
class BookingTest extends KernelTestCase
{
    use ResetDatabase, Factories, HasBrowser, InteractsWithMailer;
// ... lines 17 - 54
}
````

👉 Bu trait, e-posta doğrulama özelliklerini ekler.

Basit başlayalım, testin sonunda `$this->mailer()->assertSentEmailCount(1);` ekleyin:

````php
//tests/Functional/BookingTest.php
```php
// ... lines 1 - 13
class BookingTest extends KernelTestCase
{
// ... lines 16 - 20
    public function testCreateBooking(): void
    {
// ... lines 23 - 54
        $this->mailer()
            ->assertSentEmailCount(1)
        ;
    }
}
````

👉 Bu satır, bir adet e-posta gönderilip gönderilmediğini kontrol eder.

## 📝 Test-specific Environment Variables / Teste Özel Ortam Değişkenleri

Kısa bir not: `env.local` - gerçek Mailtrap kimlik bilgilerimizi koyduğumuz yer - test ortamında okunmaz veya kullanılmaz: testlerimiz sadece `.env` ve `.env.test` dosyasını yükler. Ve `.env`'de, `MAILER_DSN` değeri `null://null` olarak ayarlanmıştır. Harika! Testlerimizin hızlı olmasını ve gerçekten e-posta göndermemesini istiyoruz.

Tekrar çalıştırın!

```shell
bin/phpunit
```

👉 Bu komut, PHPUnit ile testleri çalıştırır.

## 📬 assertEmailSentTo() / assertEmailSentTo()

Geçti - 1 adet e-posta gönderiliyor! Geri dön ve başka bir doğrulama ekle: `->assertEmailSentTo()`. Hangi e-posta adresini bekliyoruz? Formda doldurduğumuz adres: `bruce@wayne-enterprises.com`. Kopyalayıp yapıştır. İkinci argüman başlık: `Booking Confirmation for Visit Mars`:

````php
//tests/Functional/BookingTest.php
```php
// ... lines 1 - 13
class BookingTest extends KernelTestCase
{
// ... lines 16 - 20
    public function testCreateBooking(): void
    {
// ... lines 23 - 54
        $this->mailer()
// ... line 56
            ->assertEmailSentTo('bruce@wayne-enterprises.com', 'Booking Confirmation for Visit Mars')
        ;
    }
}
````

👉 Bu satır, belirli adrese ve konuya sahip e-postanın gönderilip gönderilmediğini kontrol eder.

Testleri çalıştır!

```shell
bin/phpunit
```

👉 Bu komut, PHPUnit ile testleri çalıştırır.

Hala geçiyor! Ve artık 19 yerine 20 doğrulama var.

## 🧑‍💻 TestEmail / TestEmail

Ama daha ileri gidebiliriz! Bu doğrulamada konu için bir dize yerine, `TestEmail $email` parametresiyle bir closure kullanın:

````php
//tests/Functional/BookingTest.php
```php
// ... lines 1 - 14
class BookingTest extends KernelTestCase
{
// ... lines 17 - 21
    public function testCreateBooking(): void
    {
// ... lines 24 - 55
        $this->mailer()
// ... line 57
            ->assertEmailSentTo('bruce@wayne-enterprises.com', function(TestEmail $email) {
// ... lines 59 - 64
            })
        ;
    }
}
````

👉 Bu şekilde, e-posta nesnesi üzerinde birden fazla doğrulama yapılabilir.

Artık içeride, bu e-posta üzerinde birçok doğrulama yapabiliriz. Yukarıda kontrol etmediğimiz için önce bunu ekleyin: `$email->assertSubject('Booking Confirmation for Visit Mars')`:

````php
// tests/Functional/BookingTest.php
```php
// ... lines 1 - 14
class BookingTest extends KernelTestCase
{
// ... lines 17 - 21
    public function testCreateBooking(): void
    {
// ... lines 24 - 55
        $this->mailer()
// ... line 57
            ->assertEmailSentTo('bruce@wayne-enterprises.com', function(TestEmail $email) {
                $email
                    ->assertSubject('Booking Confirmation for Visit Mars')
// ... lines 61 - 63
                ;
            })
        ;
    }
}
````

👉 Burada, e-postanın konusunu doğruluyoruz.

Ve daha fazla doğrulama zincirleyebiliriz!

`->assert` yazıp editörün önerilerini gör. Hepsine bak... `assertTextContains` ve `assertHtmlContains`'a dikkat et. Her biriyle ayrı ayrı kontrol yapabilirsin, fakat önemli detayların ikisinde de bulunması iyi bir uygulama olduğu için, ikisini birden kontrol eden `assertContains()` kullan. `Visit Mars` için kontrol et:

````php
// tests/Functional/BookingTest.php
```php
// ... lines 1 - 14
class BookingTest extends KernelTestCase
{
// ... lines 17 - 21
    public function testCreateBooking(): void
    {
// ... lines 24 - 55
        $this->mailer()
// ... line 57
            ->assertEmailSentTo('bruce@wayne-enterprises.com', function(TestEmail $email) {
                $email
// ... line 60
                    ->assertContains('Visit Mars')
// ... lines 62 - 63
                ;
            })
        ;
    }
}
````

👉 Bu satır, e-posta içeriğinde "Visit Mars" ifadesinin geçtiğini doğrular.

Bağlantıların kontrolü de önemli, bu yüzden rezervasyon URL'sinin orada olduğundan emin ol: `->assertContains('/booking/'.. Şimdi, `BookingFactory::first()->getUid()\` kullan:

````php
// tests/Functional/BookingTest.php
```php
// ... lines 1 - 14
class BookingTest extends KernelTestCase
{
// ... lines 17 - 21
    public function testCreateBooking(): void
    {
// ... lines 24 - 55
        $this->mailer()
// ... line 57
            ->assertEmailSentTo('bruce@wayne-enterprises.com', function(TestEmail $email) {
                $email
// ... lines 60 - 61
                    ->assertContains('/booking/'.BookingFactory::first()->getUid())
// ... line 63
                ;
            })
        ;
    }
}
````

👉 Bu satır, e-posta içeriğinde rezervasyon URL'sinin olup olmadığını kontrol eder.

## 📎 Ek Kontrolü / Ek Kontrolü

Hatta ek dosya bile kontrol edebiliriz: `->assertHasFile('Terms of Service.pdf')`:

````php
//tests/Functional/BookingTest.php
```php
// ... lines 1 - 14
class BookingTest extends KernelTestCase
{
// ... lines 17 - 21
    public function testCreateBooking(): void
    {
// ... lines 24 - 55
        $this->mailer()
// ... line 57
            ->assertEmailSentTo('bruce@wayne-enterprises.com', function(TestEmail $email) {
                $email
// ... lines 60 - 62
                    ->assertHasFile('Terms of Service.pdf')
                ;
            })
        ;
    }
}
````

👉 Bu satır, e-posta eklerinde "Terms of Service.pdf" dosyasının olup olmadığını kontrol eder.

İçerik türünü ve dosya içeriğini ek argümanlarla kontrol edebilirsin, ama şu anda sadece ekin varlığını kontrol etmek yeterli.

Testleri tekrar çalıştır!

```shell
bin/phpunit
```

👉 Bu komut, PHPUnit ile testleri çalıştırır.

Harika, artık 25 doğrulama var!

## 🛑 ->dd() / ->dd()

Son bir şey: Eğer bu e-posta doğrulamalarından birinin neden geçmediğini anlamakta zorlanırsan, zincire bir `->dd()` ekle:

````php
//tests/Functional/BookingTest.php
```php
// ... lines 1 - 14
class BookingTest extends KernelTestCase
{
// ... lines 17 - 21
    public function testCreateBooking(): void
    {
// ... lines 24 - 55
        $this->mailer()
// ... line 57
            ->assertEmailSentTo('bruce@wayne-enterprises.com', function(TestEmail $email) {
                $email
// ... lines 60 - 63
                    ->dd()
                ;
            })
        ;
    }
}
````

👉 Bu satır, e-posta nesnesini ekrana basarak hata ayıklamayı kolaylaştırır.

ve testlerini çalıştır. `dd()`'ye ulaştığında, hata ayıklaman için e-posta içeriğini döker. İşin bittiğinde unutma, kaldır!

Sonraki adımda, uygulamamıza ikinci bir e-posta eklemek istiyorum. Yinelenmeyi önlemek ve tutarlılığı sağlamak için, ikisinin de ortak kullanacağı bir Twig e-posta şablonu oluşturacağız.
