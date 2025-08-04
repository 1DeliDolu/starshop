## 📧 Email Tracking with Tags and Metadata / Etiketler ve Meta Veriler ile E-posta Takibi

Artık gerçekten e-postaları gönderiyoruz. Bağlantılarımızın çalışıp çalışmadığını bir kez daha kontrol edelim... Her şey yolunda!

`Mailtrap` E-posta Günlükleri
`Mailtrap`, e-postaları iletmek ve hata ayıklamak dışında daha fazlasını da yapabilir: e-postaları ve e-posta olaylarını da takip edebiliriz. `Mailtrap`'e geçin ve "`Email API/SMTP`"ye tıklayın. Bu panel, gönderdiğimiz her bir e-postanın genel bir görünümünü gösterir. "`Email Logs`"a tıklayarak tam listeyi görebilirsiniz. İşte bizim e-postamız! Ayrıntıları görmek için üzerine tıklayın.

Hey! Bu tanıdık geliyor... `Mailtrap` test arayüzüne benziyor. Genel ayrıntıları, spam analizini ve daha fazlasını görebiliyoruz. Fakat şu çok güzel: "`Event History`"ye tıklayın. Bu bölüm, bu e-postanın akışında gerçekleşen tüm olayları gösterir. E-postanın ne zaman gönderildiğini, teslim edildiğini, hatta alıcı tarafından açıldığını bile görebiliyoruz! Her olayda, örneğin e-postayı açan IP adresi gibi ekstra bilgiler de bulunur. E-posta sorunlarını teşhis etmek için çok faydalı. `Mailtrap` ayrıca, etkinleştirilirse, e-postadaki bağlantıların hangisinin tıklandığını da gösteren bir bağlantı takip özelliğine sahiptir.

Yeniden "`Email Info`" sekmesinde, biraz aşağı kaydırın. "`Category`"nin "`missing`" (eksik) olduğunu fark edeceksiniz. Bu aslında bir sorun değil, fakat bir "`category`" (kategori), uygulamanızın gönderdiği farklı e-postaları düzenlemeye yardımcı olan bir metin ifadesidir. Bu, aramayı kolaylaştırır ve geçen ay kaç tane kullanıcı kayıt e-postası gönderdik gibi ilginç istatistikler elde etmemizi sağlar.

## 🏷️ Email Tag (Mailtrap Category) / E-posta Etiketi (Mailtrap Kategorisi)

`Symfony Mailer` buna "`tag`" (etiket) der ve e-postalara ekleyebilirsiniz. `Mailtrap bridge` bu etiketi alır ve kendi "`category`" (kategori) alanına dönüştürür. Hadi ekleyelim!

`TripController::show()` içinde, e-posta oluşturma işleminden sonra şu satırı ekleyin:
`$email->getHeaders()->add(new TagHeader());` – isim olarak `booking` kullanın:

src/Controller/TripController.php

```php
// ... lines 1 - 21
final class TripController extends AbstractController
{
// ... lines 24 - 32
    public function show(
// ... lines 34 - 41
    ): Response {
// ... lines 43 - 44
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 46 - 66
            $email->getHeaders()->add(new TagHeader('booking'));
// ... lines 68 - 71
        }
// ... lines 73 - 77
    }
}
```

👉 Bu kod, gönderilen e-postaya `booking` etiketi ekler.

## 🗃️ Email Metadata (Mailtrap Custom Variables) / E-posta Meta Verisi (Mailtrap Özel Değişkenler)

`Mailer` ayrıca e-postalara ekleyebileceğiniz özel bir meta veri başlığına sahiptir. Bu, ek veri eklemek için serbest biçimli bir anahtar-değer deposudur. `Mailtrap bridge` bunları kendi "`custom variables`" (özel değişkenler) alanına dönüştürür.

Hadi birkaç tane ekleyelim:

src/Controller/TripController.php

```php
// ... lines 1 - 22
final class TripController extends AbstractController
{
// ... lines 25 - 33
    public function show(
// ... lines 35 - 42
    ): Response {
// ... lines 44 - 45
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 47 - 68
            $email->getHeaders()->add(new MetadataHeader('booking_uid', $booking->getUid()));
// ... lines 70 - 74
        }
// ... lines 76 - 80
    }
}
```

👉 Bu kod, e-postaya `booking_uid` meta verisi olarak rezervasyonun UID bilgisini ekler.

Ve:

src/Controller/TripController.php

```php
// ... lines 1 - 22
final class TripController extends AbstractController
{
// ... lines 25 - 33
    public function show(
// ... lines 35 - 42
    ): Response {
// ... lines 44 - 45
        if ($form->isSubmitted() && $form->isValid()) {
// ... lines 47 - 69
            $email->getHeaders()->add(new MetadataHeader('customer_uid', $customer->getUid()));
// ... lines 71 - 74
        }
// ... lines 76 - 80
    }
}
```

👉 Bu kod, e-postaya `customer_uid` meta verisi olarak müşterinin UID bilgisini ekler.

Artık her rezervasyon e-postasına bir müşteri ve rezervasyon referansı eklenmiş oldu. Harika!

Bunların `Mailtrap`'ta nasıl görüneceğine bakmak için, uygulamaya gidip bir seyahat rezervasyonu yapın (hala üretim ortamında gönderim yaptığımız için kişisel e-postanızı kullanın). Gelen kutunuzu kontrol edin... işte burada. `Mailtrap`'a geri dönün, e-posta günlüklerini yenileyin... işte burada! Üzerine tıklayın. Artık "`Email Info`" sekmesinde, `booking` kategorimizi görebiliyoruz! Biraz daha aşağıda, meta verilerimizi yani "`custom variables`"ı görebiliriz.

## 🔍 Filtering by Category / Kategoriye Göre Filtreleme

"`Category`"ye göre filtrelemek için, e-posta günlüklerine gidin. Bu arama kutusunda "`Categories`" seçeneğini seçin. Bu filtre, kullandığımız tüm kategorileri listeler. "`booking`"i seçin ve "`Search`"a tıklayın. Bu bile mühendislik katındaki Jeffries tüplerinden daha organize!

İşte bu kadar! Artık `Mailtrap` ile üretim ortamında e-posta gönderiyoruz! Sonraki bölümleri kolaylaştırmak için tekrar `Mailtrap` test moduna geçelim. `.env.local` dosyasında, `Mailtrap` test için kullanılan `MAILER_DSN` satırının başındaki yorumu kaldırın ve üretim için kullanılan `MAILER_DSN` satırını yorum satırı haline getirin.

Sırada, e-postalarımızı asenkron olarak göndermek için `Symfony Messenger` kullanmak var. Ooo!
