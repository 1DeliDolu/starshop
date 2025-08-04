# 📦 Installing the Mailer / Mailer'ın Kurulumu

Arkadaşlar merhaba! "Symfony Mailer with Mailtrap"e hoş geldiniz! Ben Kevin, bu kursta size rehberlik edeceğim ve Symfony'nin `Mailer` bileşeniyle nasıl güzel e-postalar gönderebileceğimizi, HTML ve CSS eklemeyi ve üretim ortamı için yapılandırmayı anlatacağım. Bu arada, e-postalarınızı gerçek ortamda göndermek için kullanabileceğiniz birçok servis var. Bu kursta `Mailtrap` adlı servise odaklanacağız: (1) çünkü harika ve (2) çünkü e-postalarınızı önizlemeniz için mükemmel bir yol sunuyor. Ama endişelenmeyin, burada öğreneceğiniz kavramlar evrenseldir ve herhangi bir e-posta servisinde kullanılabilir. Ve bonus! Ayrıca, bazı nispeten yeni Symfony bileşenlerinden olan `Webhook` ve `RemoteEvent`'i kullanarak e-posta olaylarını (örn. iletilmeyenler, açılma, bağlantı tıklama) nasıl takip edebileceğinizi de göstereceğiz.

## ✉️ Transactional vs Bulk Emails / Transactional ve Toplu E-postalar

Spam göndermeden, yani önemli bilgileri e-posta ile ulaştırmadan önce, bir konuyu netleştirmemiz gerek: Symfony `Mailer` yalnızca `transactional` e-postalar için kullanılır. Bunlar, uygulamanızda belirli bir olay gerçekleştiğinde, belirli bir kullanıcıya gönderilen e-postalardır. Örneğin: bir kullanıcı kayıt olduktan sonra gönderilen hoş geldin e-postası, sipariş verdikten sonra gönderilen sipariş onay e-postası veya "gönderinize oy verildi" gibi bildirimler `transactional` e-postalara örnektir. Symfony `Mailer`, toplu veya pazarlama amaçlı e-postalar için kullanılmaz. Bu nedenle, herhangi bir abonelikten çıkma (unsubscribe) işlevini düşünmemize gerek yok. Toplu e-posta veya bülten göndermek için özel servisler vardır, `Mailtrap` da sitesinden bu işlemi yapabilir.

## 🚀 Our Project / Projemiz

Her zamanki gibi, en yüksek faydayı almak için benimle birlikte kod yazmanız harika olur! Kursun kodunu bu sayfadan indirin. Dosyayı açtığınızda, başlangıç kodlarının bulunduğu bir `start/` dizini göreceksiniz. Uygulamayı çalıştırmak için `README.md` dosyasındaki adımları izleyin. Ben bu adımları zaten uyguladım ve web sunucusunu başlatmak için `symfony serve -d` komutunu çalıştırdım.

"Universal Travel"e hoş geldiniz: kullanıcıların galaksinin farklı bölgelerine seyahat rezervasyonu yapabildiği bir seyahat acentesi. Şu anda mevcut olan geziler burada. Kullanıcılar bu seyahatleri rezerve edebiliyor, ancak şu anda rezervasyon yaptıklarında herhangi bir onay e-postası gönderilmiyor. Bunu düzelteceğiz! Naboo'ya binlerce kredi harcayıp seyahat rezervasyonu yaptığımda, rezervasyonumun başarılı olduğunu bilmek isterim!

## 🔧 Installing the Mailer Component / Mailer Bileşenini Kurmak

Adım 1: Symfony `Mailer`'ı kuralım! Terminali açıp şunu çalıştırın:



```bash
composer require mailer
```

👉 Bu komut, Symfony projesine Mailer bileşenini ekler.

`mailer` için Symfony Flex tarifi, bize bazı Docker yapılandırmalarını kurmamızı öneriyor. Bu, e-postaları önizlemek için yerel bir `SMTP` sunucusuna yardımcı olur. Biz bunun yerine `Mailtrap` kullanacağız, bu yüzden "hayır" deyin. Kurulum tamamlandı! Şimdi şunu çalıştırın:

src/

```bash
git status
```

👉 Bu komut, projenizde yapılan değişiklikleri gösterir.

Görünüşe göre, tarif `.env` dosyasına bazı ortam değişkenleri ekledi ve `config/packages/mailer.yaml` dosyasına mailer yapılandırması ekledi.

## 🔑 MAILER\_DSN

IDE'nizde `.env` dosyasını açın. `Mailer` tarifi, bu dosyaya `MAILER_DSN` adlı bir ortam değişkeni ekledi. Bu, mailer transportunuzu yapılandıran özel bir URL benzeri stringdir: e-postalarınızın nasıl gönderileceğini (ör. `SMTP`, `Mailtrap` vb.) belirler. Tarifin varsayılan değeri `null://null` olarak gelir ve yerel geliştirme/test için idealdir. Bu transport, e-posta gönderildiğinde hiçbir şey yapmaz! E-postanın gönderildiğini varsayar ama gerçekte göndermeden yokmuş gibi davranır. E-postaları farklı bir şekilde önizleyeceğiz.

Hazırız! Artık ilk e-postamızı göndermeye başlayabiliriz! Sıradaki adımda bunu yapacağız!
