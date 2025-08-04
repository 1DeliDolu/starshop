# 🚀 Production Sending with Mailtrap / Mailtrap ile Üretimde E-posta Gönderimi

Artık üretim ortamında gerçek e-postalar göndermenin zamanı geldi!

## 📦 Mailer Transports / Mailer Taşıyıcıları

`Mailer`, e-posta göndermek için çeşitli yollarla gelir, bunlara "taşıyıcı" (`transports`) denir. Bu `smtp` olanı, Mailtrap testlerimizde kullandığımızdır. Kendi `SMTP` sunucunuzu kurarak e-posta gönderebilirsiniz... fakat... bu karmaşıktır ve e-postalarınızın spam olarak işaretlenmemesini sağlamak için birçok şey yapmanız gerekir. Sıkıcı.

## 🤝 3rd-Party Transports / 3. Parti Taşıyıcılar

Şiddetle, şiddetle bir üçüncü parti e-posta servisi kullanmanızı öneriyorum. Bunlar tüm bu karmaşıklıklarla sizin yerinize ilgilenir ve `Mailer` bunların birçoğu için köprüler (`bridges`) sağlar, böylece kurulum çok kolay olur.

## 📨 Mailtrap Bridge / Mailtrap Köprüsü

Test için Mailtrap kullanıyoruz ama Mailtrap'in üretim ortamı için de e-posta gönderme yetenekleri var! Harika! Hatta bunun için resmi bir köprüsü de var!

Terminalde aşağıdaki komutu çalıştırarak yükleyin:


```php
// src/Controller/MainController.php
composer require symfony/mailtrap-mailer
```

👉 Bu komut, `symfony/mailtrap-mailer` paketini projenize ekler.

Kurulumdan sonra, IDE'nizi kontrol edin. `.env` dosyasında, tarif (`recipe`) bazı `MAILER_DSN` şablonları ekledi. Gerçek `DSN` değerlerini Mailtrap'ten alacağız, ancak önce biraz kurulum yapmamız gerekiyor.

## 🌐 Sending Domain / Gönderim Alanı

Mailtrap'te, bir "gönderim alanı" (`sending domain`) ayarlamamız gerekiyor. Bu, sahip olduğunuz bir alan adını, Mailtrap'in bu alan adına ait e-postaları düzgün şekilde gönderebilmesi için yapılandırır.

Avukatlarımız hala `universal-travel.com` alan adının satın alınmasını müzakere ediyor, bu yüzden şimdilik bana ait bir alan adı olan `zenstruck.com`'u kullanıyorum. Siz de kendi alan adınızı buraya ekleyin.

Eklendikten sonra, "Alan Doğrulama" (`Domain Verification`) sayfasında olacaksınız. Bu çok önemli ama Mailtrap bunu kolaylaştırıyor. Yeşil onay işaretini görene kadar talimatları izleyin. Temelde, alan adınıza bir dizi özel `DNS` kaydı eklemeniz gerekecek. `DKIM`, alan adınızdan gönderilen e-postaları doğrular ve `SPF`, Mailtrap'in alanınız adına e-posta göndermesine izin verir. Mailtrap, bunların tam olarak nasıl çalıştığı hakkında daha derinlemesine bilgi almak isterseniz harika belgeler sunar. Ama temel olarak, dünyaya Mailtrap'in bizim adımıza e-posta göndermesine izin verdiğimizi söylüyoruz.

## 🛠️ Production MAILER\_DSN / Üretim MAILER\_DSN

Yeşil onay işaretini aldıktan sonra, "Integrations" (Entegrasyonlar) bölümüne, ardından "Transaction Stream" altında "Integrate" (Entegre Et) seçeneğine tıklayın.

Artık `SMTP` veya `API` kullanma arasında karar verebiliriz. Ben `API`yi kullanacağım, ama ikisi de çalışır. Ve bakın! Bu tanıdık görünüyor: Mailtrap testindeki gibi, `PHP`, ardından `Symfony`'yu seçin. İşte ihtiyacımız olan `MAILER_DSN` bu! Kopyalayın ve editörünüze geçin.

Bu hassas bir ortam değişkeni (`environment variable`), bu yüzden `.env.local` dosyasına ekleyin ki git ile paylaşıma gitmesin. Mailtrap test `DSN`'ini yorum satırı yapın ve yenisini altına yapıştırın. Bu yorumu kaldırıyorum, çünkü temizliği seviyoruz.


```env
# .env.local
MAILER_DSN=mailtrap+api://<username>:<password>@default
```

👉 Bu satır, üretim ortamında Mailtrap API ile e-posta göndermek için gerçek `MAILER_DSN` değerini ayarlar.

Neredeyse hazırız! Unutmayın, yalnızca yapılandırdığımız alan adından e-posta gönderebiliriz. Benim örneğimde, `zenstruck.com`. `config/services.yaml` dosyasını açın ve `global_from_email` ayarını kendi alan adınıza göre güncelleyin.


```yaml
# config/services.yaml
parameters:
    global_from_email: 'kevin@zenstruck.com'
```

👉 Bu satır, tüm gönderilecek e-postalar için varsayılan gönderen e-posta adresini ayarlar.

Bakalım çalışacak mı! Uygulamanızda bir seyahat rezervasyonu yapın. Bu sefer gerçek bir e-posta adresi kullanın. İsmi Kevin olarak ayarlayacağım ve kişisel e-posta adresimi kullanacağım: `kevin@symfonycasts.com`. Sizi ve uzay yolculuğunu ne kadar sevsem de, buraya kendi e-posta adresinizi yazın ki bana spam göndermeyin. Bir tarih seçin ve rezervasyon yapın!

Rezervasyon onay sayfasındayız, bu iyiye işaret! Şimdi, kişisel e-postanızı kontrol edin. Ben kendi e-postama gideceğim ve bekleyeceğim... yenile... işte burada! Eğer tıklarsam, tam olarak beklediğimiz gibi! Görsel, ek, her şey yerinde!

Sonraki adımda, gönderilen e-postaları Mailtrap ile nasıl takip edeceğimizi ve bu takibi geliştirmek için nasıl etiket ve meta veri ekleyebileceğimizi göreceğiz!
