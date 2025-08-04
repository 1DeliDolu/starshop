## 🖼️ HTML-powered Emails / HTML Destekli E-postalar

E-postaların her zaman bir düz metin (plain-text) versiyonu olmalıdır, fakat ayrıca bir `HTML` versiyonu da olabilir. Asıl eğlence de burada başlar! Şimdi bu e-postayı daha sunulabilir hale getirmek için `HTML` ekleyelim!

## 📝 HTML Email Template / HTML E-posta Şablonu

`templates/email/` dizininde, `booking_confirmation.txt.twig` dosyasını kopyala ve adını `booking_confirmation.html.twig` olarak değiştir. `HTML` versiyonu, tam bir HTML sayfası gibi davranır. Her şeyi bir `<html>` etiketi içine al, boş bir `<head>` ekle ve içeriği bir `<body>` etiketi ile sar. Ayrıca bu satırları `<p>` etiketleri ile sararak aralarına boşluk ekleyeceğim... ve "Regards," ifadesinden sonra bir satır boşluğu eklemek için bir `<br>` etiketi kullanacağım.

Bu URL artık uygun bir `<a>` etiketi içinde yer alabilir. Alan açmak için "Manage your booking" cümlesini kesin. Bir `<a>` etiketi ekleyip `href` özniteliğine URL'yi yerleştir ve metni içine yapıştır.

````twig
// templates/email/booking_confirmation.html.twig
```twig
<html>
<head></head>
<body>
<p>Hey {{ customer.name|split(' ')|first }},</p>
<p>Get ready for your trip to {{ trip.name }}!</p>
<p>Departure: {{ booking.date|date('Y-m-d') }}</p>
<p>
    <a href="{{ url('booking_show', {uid: booking.uid}) }}">
        Manage your booking
    </a>
</p>
<p>
    Regards,<br>
    The Universal Travel Team
</p>
</body>
</html>
````

👉 Bu şablon, HTML formatında e-posta içeriğini oluşturur.

Son olarak, Mailer'a bu HTML şablonunu kullanmasını belirtmemiz gerekiyor. `TripController::show()` metodunda, `->textTemplate()` satırının üstüne `->htmlTemplate()` ekle ve `'email/booking_confirmation.html.twig'` olarak belirt:

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
// ... lines 38 - 49
            $email = (new TemplatedEmail())
// ... lines 51 - 53
                ->htmlTemplate('email/booking_confirmation.html.twig')
                ->textTemplate('email/booking_confirmation.txt.twig')
// ... lines 56 - 60
            ;
// ... lines 62 - 65
        }
// ... lines 67 - 71
    }
}
````

👉 Bu kod, hem HTML hem de düz metin e-posta içeriği tanımlar.

Bir seyahat rezervasyonu yaparak test et: Steve, [steve@minecraft.com](mailto:steve@minecraft.com), ileri bir tarih ve rezervasyon yap... ardından Mailtrap’ı kontrol et. E-posta aynı görünüyor ama artık bir HTML sekmesi var!

Ayrıca "HTML Check" oldukça faydalı. E-postadaki HTML’in e-posta istemcilerinin yüzde kaçında desteklendiğini gösteren bir gösterge verir. Bilmiyorsan, e-posta istemcileri gerçekten uğraştırıcıdır: tıpkı 90’larda farklı tarayıcılarla uğraşmak gibi. Bu araç bu konuda yardımcı olur.

HTML sekmesine geri dönüp bağlantıya tıkla, çalışıyor mu kontrol et. Evet, çalışıyor!

## 🤔 Text ve HTML Versiyonlarını Otomatik Üretmek / Text Versiyonunu Otomatik Üretmek

Artık e-postamızda hem düz metin hem de HTML versiyonu var ama... ikisini de sürdürmek zahmetli. Sadece text e-posta istemcisi kullanan var mı? Muhtemelen yok ya da kullanıcıların çok azı.

Bir şey deneyelim: `TripController::show()` metodunda, `->textTemplate()` satırını kaldır. Artık e-postanın sadece HTML versiyonu var.

Tekrar bir seyahat rezervasyonu yap ve Mailtrap’ta e-postayı kontrol et. Hala bir text versiyonumuz var mı? Evet, var! Neredeyse text şablonumuza benziyor, fakat fazladan boşluklar var. Sadece HTML versiyonu olan bir e-posta gönderirsen, `Symfony Mailer` otomatik olarak bir text versiyonu oluşturur, fakat sadece etiketleri kaldırır. Bu güzel bir yedek, ama mükemmel değil. Eksik olanı görüyor musun? Bağlantı! Bu... oldukça önemli... Bağlantı gitti çünkü `href` özniteliğindeydi ve etiketler kaldırılınca kayboldu.

Peki, her zaman manuel olarak text versiyonu tutmamız mı gerekir? Gerekli değil. İşte küçük bir püf noktası.

## 🔄 HTML to Markdown / HTML’den Markdown’a

Terminalde şunu çalıştır:

```bash
# Proje kök dizininde çalıştır
composer require league/html-to-markdown
```

👉 Bu komut, HTML içeriğini markdown’a dönüştüren bir paket kurar.

Bu paket, HTML’i markdown’a çevirir. Ne? Genelde markdown’dan HTML’e dönüştürürüz, değil mi? Evet, ama HTML e-postalar için bu mükemmel! Ve tahmin et: başka hiçbir şey yapmamıza gerek yok! `Symfony Mailer`, bu paket kuruluysa sadece etiketleri kaldırmak yerine otomatik olarak bu paketi kullanır!

Bir kez daha seyahat rezervasyonu yap ve Mailtrap’ta e-postayı kontrol et. HTML aynı görünüyor, fakat text versiyonuna bak. Anchor etiketi markdown bağlantısına dönüştü! Hâlâ mükemmel değil, ama en azından var! Eğer tam kontrol istersen, ayrı bir text şablonuna ihtiyacın var, ama bence bu yeterli. IDE’de `booking_confirmation.txt.twig` dosyasını sil.

Sıradaki adımda, bu HTML’i CSS ile güzelleştireceğiz!
