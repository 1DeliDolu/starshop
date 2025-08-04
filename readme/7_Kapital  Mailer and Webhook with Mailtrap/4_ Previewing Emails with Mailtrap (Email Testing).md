# 📨 Previewing Emails with Mailtrap (Email Testing) / Mailtrap ile E-postaları Önizleme (E-posta Testi)

Profillerde e-postaları önizlemek temel e-postalar için yeterlidir, ancak yakında HTML stilleri ve uzay kedilerinin görsellerini ekleyeceğiz. E-postalarımızın nasıl göründüğünü düzgün bir şekilde görebilmek için daha sağlam bir araca ihtiyacımız var. `Mailtrap`'in e-posta test aracını kullanacağız. Bu, bağlanabileceğimiz gerçek bir `SMTP` sunucusu sağlar, fakat e-postalar gerçek gelen kutularına gitmez, bunun yerine inceleyebileceğimiz sahte bir gelen kutusuna gider! Gerçekten e-posta göndermişiz gibi olur, sonra o kişinin hesabını hackleyip bakarız... ama uğraşmadan ve yasal olmayan şeyler olmadan!

## 📥 Fake Inbox / Sahte Gelen Kutusu

[https://mailtrap.io](https://mailtrap.io) adresine git ve ücretsiz bir hesaba kaydol. Ücretsiz planın bazı sınırları var ama başlamak için mükemmel. Giriş yaptıktan sonra, uygulamanın ana sayfasında olacaksın. Şu anda ilgilendiğimiz kısım e-posta test etme, buna tıkla. Böyle bir ekran göreceksin. Eğer henüz gelen kutun yoksa, burada bir tane ekle.

Yeni gelen kutunu aç. Sonraki adımda, uygulamamızı `Mailtrap SMTP` sunucusu üzerinden e-posta gönderecek şekilde yapılandırmamız gerekiyor. Bu çok kolay! Aşağıda, "Code Examples" bölümünde, "PHP" ve ardından "Symfony"ya tıkla. `MAILER_DSN` bilgisini kopyala.

## 🛡️ MAILER\_DSN for Fake Inbox / Sahte Gelen Kutusu için MAILER\_DSN

Bu değer hassas olduğundan ve geliştiriciden geliştiriciye değişebileceğinden, bunu `.env` dosyasına ekleme çünkü bu git ile paylaşılır. Bunun yerine, projenin kök dizininde yeni bir `.env.local` dosyası oluştur. `MAILER_DSN` değerini buraya yapıştır ve `.env` içindeki değeri geçersiz kıl.

Artık Mailtrap testi için yapılandırılmış durumdayız! Çok kolaydı! Hemen test et!

Uygulamada tekrar, yeni bir seyahat rezervasyonu yap: İsim: Steve, E-posta: [steve@minecraft.com](mailto:steve@minecraft.com), ileri bir tarih ve... rezervasyon yap! Bu istek biraz daha uzun sürer çünkü dışarıdaki Mailtrap SMTP sunucusuna bağlanır.

## 📧 Email in Mailtrap / Mailtrap'ta E-posta

Mailtrap'ta, bam! E-posta gelen kutumuzda! Aç ve bak. "Text" önizlemesi ve "Raw" görünümü mevcut. Ayrıca "Spam Analysis" var – harika! "Tech Info" kısmı ise tüm teknik "e-posta başlıklarını" kolay okunur bir formatta gösterir.

"HTML" sekmeleri ise gri çünkü henüz e-postamızın bir HTML versiyonu yok... henüz... Bunu bir sonraki adımda değiştireceğiz!
