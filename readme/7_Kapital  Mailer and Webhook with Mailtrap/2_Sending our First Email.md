# 📧 Sending our First Email / İlk E-postamızı Göndermek

Bir yolculuğa çıkalım! "Krypton'u Ziyaret Et", umarım henüz yok olmamıştır! Kontrol etmeye bile gerek duymadan, rezervasyonumuzu yapalım! Ad: "Kevin", e-posta: "[kevin@example.com](mailto:kevin@example.com)" ve gelecekte herhangi bir tarih kullanacağım. "Book Trip" butonuna tıklayın.

Burası "booking details" yani rezervasyon detayları sayfası. Dikkat edin, URL bu rezervasyona özgü benzersiz bir `token` içeriyor. Kullanıcı daha sonra bu sayfaya dönmek isterse, şu anda bu sayfayı yer imlerine eklemeli veya benim gibi Slack'ten kendine göndermeli. Kötü bir çözüm! Bunun yerine, bu sayfaya bağlantı içeren bir onay e-postası gönderelim.

Bunun rezervasyon ilk kaydedildikten sonra olmasını istiyorum. `TripController` dosyasını açın ve `show()` metodunu bulun. Burada rezervasyon yapılıyor: Eğer form geçerliyse, bir müşteri oluşturuluyor veya var olan bulunuyor ve bu müşteri için bir rezervasyon kaydı yapılıyor. Sonra rezervasyon detayları sayfasına yönlendirme yapılıyor. Şu ana kadar her şey gayet sıkıcı, tam da kodlarımın ve hafta sonlarımın olmasını istediğim gibi.

## 📨 Inject MailerInterface / MailerInterface Enjekte Et

Rezervasyon oluşturulduktan sonra bir e-posta göndermek istiyorum. Her bir metot parametresini kendi satırına alarak kodda biraz boşluk açın. Ardından, ana e-posta gönderme servisini almak için `MailerInterface $mailer` ekleyin:


```php
// src/Controller/TripController.php
// ... lines 1 - 17
final class TripController extends AbstractController
{
// ... lines 20 - 27
    #[Route('/trip/{slug:trip}', name: 'trip_show')]
    public function show(
// ... lines 30 - 33
        MailerInterface $mailer,
    ): Response {
// ... lines 36 - 54
    }
}
```

👉 Bu kodda, `MailerInterface` ile e-posta gönderme yeteneği metoda eklenir.

## 📝 Create the Email / E-postayı Oluşturmak

`flush()` işleminden (yani rezervasyonun veritabanına kaydedilmesinden) sonra, yeni bir e-posta nesnesi oluşturun: `$email = new Email()` (bu, `Symfony\Component\Mime`'dan gelen sınıf). Metod zincirlemesi yapmak için parantez içinde yazın. Her e-postada neler olması gerekir? Bir gönderici adresi: `->from()`, örneğin `info@universal-travel.com`. Bir alıcı adresi: `->to($customer->getEmail())`. Konu: `->subject('Booking Confirmation')`. Ve son olarak, bir içerik: `->text('Your booking has been confirmed')` - şimdilik yeterli:


```php
// src/Controller/TripController.php
// ... lines 1 - 18
final class TripController extends AbstractController
{
// ... lines 21 - 29
    public function show(
// ... lines 31 - 35
    ): Response {
// ... lines 37 - 38
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 40 - 48
            $email = (new Email())
                ->from('info@universal-travel.com')
                ->to($customer->getEmail())
                ->subject('Booking Confirmation')
                ->text('Your booking has been confirmed!')
            ;
// ... lines 55 - 56
        }
// ... lines 58 - 62
    }
}
```

👉 Bu kod, e-posta nesnesini oluşturur ve gerekli alanları doldurur.

## 📤 Send the Email / E-postayı Gönder

Son olarak `$mailer->send($email)` satırını ekleyin:


```php
// src/Controller/TripController.php
// ... lines 1 - 18
final class TripController extends AbstractController
{
// ... lines 21 - 29
    public function show(
// ... lines 31 - 35
    ): Response {
// ... lines 37 - 38
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 40 - 55
            $mailer->send($email);
// ... lines 57 - 58
        }
// ... lines 60 - 64
    }
}
```

👉 Bu kod, e-posta nesnesini gönderir.

## 🧪 Let's test this out! / Şimdi Test Edelim!

Uygulamamıza dönüp ana sayfaya gidin ve bir seyahat seçin. İsim olarak "Steve", e-posta olarak "[steve@minecraft.com](mailto:steve@minecraft.com)", gelecekte herhangi bir tarih girin ve rezervasyonu yapın.

Sayfa görünüşte aynen eskisi gibi. E-posta gönderildi mi? Web debug araç çubuğunda herhangi bir gösterge yok gibi görünüyor...

Aslında e-posta önceki istekte - form gönderildiğinde - gönderildi. O controller, bizi bu sayfaya yönlendirdi. Ancak web debug araç çubuğu, önceki isteğin profilini hızlıca açmamızı sağlayan bir kısayol sunar: 200 üzerine gelin ve profil bağlantısına tıklayın.

## 🗂️ Email in the Profiler / Profiler'da E-posta

Yan menüye bakın - artık yeni bir "Emails" sekmesi var! Ve burada 1 e-posta gönderildiği görünüyor. Başardık! Tıklayın ve işte e-postamız! Gönderen, alıcı, konu ve içerik beklediğimiz gibi.

Unutmayın, şu anda `null` mailer transport kullanıyoruz, bu yüzden bu e-posta gerçekte gönderilmedi, ama yine de profilörde önizleyebilmek harika!

Yine de... bu e-postanın oldukça işe yaramaz olduğunu ikimiz de biliyoruz. Hiçbir faydalı bilgi yok! Rezervasyon detaylarına bağlantı yok, varış yeri yok, tarih yok, hiçbir şey yok! Bu kadar gereksiz bir e-postayı `null` transport'un uzaya fırlatması iyi olmuş.

Bunu bir sonraki adımda düzelteceğiz!
