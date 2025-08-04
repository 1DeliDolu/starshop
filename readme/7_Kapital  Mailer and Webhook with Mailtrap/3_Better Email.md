## 📧 Better Email / Daha İyi E-posta

Sanırım sen, ben, e-posta almış olan herkes ilk e-postamızın kötü olduğunu kabul edebiliriz. Hiçbir değer sunmuyor. Hadi bunu geliştirelim!

## 🏷️ Address Object / Address Nesnesi

Öncelikle, e-postaya bir isim ekleyebiliriz. Bu, çoğu e-posta istemcisinde sadece e-posta adresi yerine görünecek: daha şık görünüyor. `from` kısmını `new Address()` ile sarmala, bu `Symfony\Component\Mime` içinden geliyor. İlk argüman e-posta, ikincisi ise isim – mesela `Universal Travel` olabilir:

````php
// src/Controller/TripController.php
```php
// ... lines 1 - 19
final class TripController extends AbstractController
{
// ... lines 22 - 30
    public function show(
// ... lines 32 - 36
    ): Response {
// ... lines 38 - 39
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 41 - 49
            $email = (new Email())
                ->from(new Address('info@universal-travel.com', 'Universal Travel'))
// ... lines 52 - 54
            ;
// ... lines 56 - 59
        }
// ... lines 61 - 65
    }
}
````

👉 Bu kod, gönderici adresini ve ismini ayarlar.

Ayrıca, `to` kısmını da `new Address()` ile sarmalayabiliriz ve isim olarak `$customer->getName()` gönderebiliriz:

````php
//src/Controller/TripController.php
```php
// ... lines 1 - 19
final class TripController extends AbstractController
{
// ... lines 22 - 30
    public function show(
// ... lines 32 - 36
    ): Response {
// ... lines 38 - 39
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 41 - 49
            $email = (new Email())
// ... line 51
                ->to(new Address($customer->getEmail()))
// ... lines 53 - 54
            ;
// ... lines 56 - 59
        }
// ... lines 61 - 65
    }
}
````

👉 Bu kod, alıcı e-posta adresini ayarlar.

## 📝 Subject İçin Seyahat İsmi / Subject Alanında Gezi Adı

`subject` kısmına seyahat adını ekle: `'Booking Confirmation for ' . $trip->getName()`:

````php
// src/Controller/TripController.php
```php
// ... lines 1 - 19
final class TripController extends AbstractController
{
// ... lines 22 - 30
    public function show(
// ... lines 32 - 36
    ): Response {
// ... lines 38 - 39
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 41 - 49
            $email = (new Email())
// ... lines 51 - 52
                ->subject('Booking Confirmation for '.$trip->getName())
// ... line 54
            ;
// ... lines 56 - 59
        }
// ... lines 61 - 65
    }
}
````

👉 Bu kod, e-posta başlığını ayarlar ve seyahat adını ekler.

## 📄 Text Body İçin Twig / Text Body İçin Twig Kullanımı

Tüm metni burada satır içi olarak ekleyebilirdik. Bu çok karmaşık olurdu, bu yüzden `Twig` kullanalım! Bir şablona ihtiyacımız var. `templates/` içinde yeni bir `email/` dizini oluştur ve içine `booking_confirmation.txt.twig` adında yeni bir dosya oluştur. `Twig` her türlü metin formatı için kullanılabilir, sadece html için değil. Dosya adında formatı (`.html` veya `.txt`) belirtmek iyi bir uygulamadır. Ama `Twig` bununla ilgilenmez – bu sadece insanlar için bir kolaylık.

Bu dosyaya birazdan döneceğiz.

## 📨 Twig Email Template / Twig E-posta Şablonu

`TripController::show()`'da, `new Email()` yerine `new TemplatedEmail()` (bu `Symfony\Bridge\Twig` içinden gelir) kullan:

````php
//src/Controller/TripController.php
```php
// ... lines 1 - 19
final class TripController extends AbstractController
{
// ... lines 22 - 30
    public function show(
// ... lines 32 - 36
    ): Response {
// ... lines 38 - 39
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 41 - 49
            $email = (new TemplatedEmail())
// ... lines 51 - 64
        }
// ... lines 66 - 70
    }
}
````

👉 Bu kodda, e-posta şablonlu olarak gönderilir.

`->text()` fonksiyonunu, `->textTemplate('email/booking_confirmation.txt.twig')` ile değiştir:

````php
//src/Controller/TripController.php
```php
// ... lines 1 - 19
final class TripController extends AbstractController
{
// ... lines 22 - 30
    public function show(
// ... lines 32 - 36
    ): Response {
// ... lines 38 - 39
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 41 - 49
            $email = (new TemplatedEmail())
// ... lines 51 - 53
                ->textTemplate('email/booking_confirmation.txt.twig')
// ... lines 55 - 59
            ;
// ... lines 61 - 64
        }
// ... lines 66 - 70
    }
}
````

👉 Bu kod, e-posta içeriği için Twig şablonu kullanır.

Şablona değişkenleri göndermek için `->context()` ile `'customer' => $customer`, `'trip' => $trip`, `'booking' => $booking` ekle:

````php
//src/Controller/TripController.php
```php
// ... lines 1 - 19
final class TripController extends AbstractController
{
// ... lines 22 - 30
    public function show(
// ... lines 32 - 36
    ): Response {
// ... lines 38 - 39
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 41 - 49
            $email = (new TemplatedEmail())
// ... lines 51 - 54
                ->context([
                    'customer' => $customer,
                    'trip' => $trip,
                    'booking' => $booking,
                ])
            ;
// ... lines 61 - 64
        }
// ... lines 66 - 70
    }
}
````

👉 Bu kod, şablona değişken gönderimini sağlar.

Dikkat et, burada `Twig` şablonu teknik olarak doğrudan render edilmiyor: Mailer, e-postayı göndermeden önce bunu yapacak.

## 🧩 Twig Şablonu ile E-posta İçeriği / Twig Template ile Email Gövdesi

Bu sıradan, normal `Twig` kodu. Kullanıcının adının ilk kısmını (küçük bir hileyle), seyahat adını, kalkış tarihini ve rezervasyonu yönetmek için bir bağlantı gösterelim. E-postalarda mutlaka tam URL'ler kullanılmalı – mesela `https://univeral-travel.com/booking` gibi – bu yüzden `url()` Twig fonksiyonunu `path()` yerine kullanacağız: `{{ url('booking_show', {'uid': booking.uid}) }}`. Nazikçe bitir: Regards, the Universal Travel team.

````twig
//  templates/email/booking_confirmation.txt.twig
```twig
Hey {{ customer.name|split(' ')|first }},
Get ready for your trip to {{ trip.name }}!
Departure: {{ booking.date|date('Y-m-d') }}
Manage your booking: {{ url('booking_show', {uid: booking.uid}) }}
Regards,
The Universal Travel Team
````

👉 Bu şablon, e-posta gövdesinin nasıl görüneceğini tanımlar.

E-posta gövdesi tamam! Test et. Tarayıcıda bir seyahat seç, isim: Steve, e-posta: [steve@minecraft.com](mailto:steve@minecraft.com), ileri bir tarih seç ve seyahati ayırt. Son isteğin profiler'ını aç, Emails sekmesine tıkla ve e-postayı gör.

Çok daha iyi! Artık From ve To adreslerinde isimler var. Ve metin içeriğimiz çok daha değerli! Rezervasyon URL’sini kopyalayıp tarayıcıya yapıştır, doğru yere gidiyor mu bak. Görünüyor, güzel!

Sırada, daha güçlü bir e-posta önizlemesi için Mailtrap’ın test aracını kullanmak var.
