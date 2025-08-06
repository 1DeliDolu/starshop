# 🌐 Ngrok Tunnels / Ngrok Tünelleri

Önceki bölümde, Symfony Webhook bileşeni ile çalıştık. Webhook’larımızı yönetmek için benzersiz bir uç nokta (endpoint) oluşturduk, fakat... bir sorun var: LemonSqueezy doğrudan yerel makinemize istek gönderemez. Sorun gibi görünüyor, değil mi? Endişelenmeyin! Bunun için harika bir çözümümüz var: "Ngrok" adlı akıllı bir araçla bir tünel açacağız. Bu, tüm istekleri yerel makinemize iletecek herkese açık bir URL oluşturur. Kullanımı çok kolaydır ve ücretsiz planının bazı sınırlamaları olsa da başlamak için fazlasıyla yeterli. Hadi başlayalım!

## Ngrok Kurulumu

İlk adım: Resmi web sitesinden işletim sisteminize uygun Ngrok’u kurun. Ben Mac kullanıyorum, bu yüzden Homebrew paket yöneticisi ile kuruyorum. Aslında daha önce kurmuştum, bu yüzden bu adımı atlıyorum. Hazırsanız, şu komutu çalıştırın:

---

```shell
ngrok http 8000
```

👉 Bu komut, 8000 portunu herkese açık bir tünel ile erişilebilir hale getirir.

---

Yerel web sitemiz `127.0.0.1:8000` adresini kullandığı için burada 8000 portunu kullanmamız çok önemli. Farklı bir port kullanıyorsanız, o portu yazmalısınız.

## Ngrok Ayarları

Bu komutu çalıştırdıktan sonra, aşağıda "Forwarding" satırında rastgele bir URL göreceksiniz. Bu, gelen istekleri `http://localhost:8000` adresine yönlendirir. Ben Mac'te Cmd tuşuna basılı tutup yeni URL’ye tıklıyorum ve… işte! Sitemiz karşımızda! Artık herkese açık bir URL ile erişilebilir. Ne kadar harika!

Webhook bileşeni, yeni bir `/webhook` rotası ekledi. Biz lemon-squeezy webhook'u oluşturduğumuz için rotada webhook "{type}" olarak yer aldı. Son URL şöyle görünecek:

```bash
https://0787-5-154-3-6.ngrok-free.app/webhook/lemon-squeezy
```

Bu URL bir hata döndürüyor, ama şimdilik bunu görmezden gelip kopyalayın ve az önce gördüğümüz LemonSqueezy "Callback URL" alanına yapıştırın.

## İmza Sırrı (Signing Secret) Oluşturma

Şimdi, "signing secret" (imza sırrı) dediğimiz bir şeye ihtiyacımız var; bu, rastgele bir dizeden ibaret. Bunu doğrudan `webhook.yaml` dosyasına yazabilirdik, ama daha düzenli olması için bir ortam değişkeninde (environment variable) saklayalım. Ben rastgele harf ve rakamlardan oluşan bir karışım kullanıyorum. İsterseniz şifre oluşturucu bir araçla daha rastgele bir dize elde edebilirsiniz. Sırrınızı oluşturduktan sonra kopyalayın. Ben bunu depoya eklemede sorun görmüyorum, bu yüzden `.env` dosyamı açıp şu satırı ekliyorum:

---


```
// .env
// ... lines 1 - 18
LEMON_SQUEEZY_SIGNING_SECRET=lEm0n-5qUeEzY
// ... lines 20 - 43
```

👉 Bu ortam değişkeni, LemonSqueezy webhook’ları için kullanılan imza sırrıdır.

---

Sonra, `webhook.yaml` dosyasında secret değerini şu şekilde ayarlayın ve tek tırnak içinde yazın:

---


```yaml
# config/packages/webhook.yaml
framework:
  webhook:
    routing:
      lemon-squeezy:
        secret: '%env(LEMON_SQUEEZY_SIGNING_SECRET)%'
```

👉 Bu ayar ile webhook bileşeni ortam değişkenini kullanarak imza doğrulaması yapar.

---

## Olay Seçme ve Webhook'u Kaydetme

LemonSqueezy paneline geri dönüp, "order\_created" etkinliğini seçin. Şu anda ilgilendiğimiz tek etkinlik bu. "Save Webhook" butonuna tıklayın ve… mükemmel! Webhook yapılandırmamız hazır.

## Ngrok Web Arayüzü Kullanımı

Ngrok komutunu çalıştırdığınız terminalde bir de "Web Interface" için bir yerel URL göreceksiniz. Cmd tuşuna basılı tutup bu URL’ye tıklayarak yeni bir sekmede açın. Ngrok Web Arayüzüne hoş geldiniz! Burada, herkese açık URL’ye gelen tüm istekleri görebilirsiniz. Bu, webhook’larınızı izlemek ve hata ayıklamak için son derece yararlıdır.

## Webhook Tetikleme

Her şey hazırsa, bir webhook tetikleyelim. Sitemize yerel URL ile dönün, bir limonata seçin (ben karpuzluyu seçiyorum) ve sepete ekleyin. "Checkout" butonuna tıklayın… ödeme, fatura adresi ve en altta "Pay" butonuna tıklayın. Başarılı! LemonSqueezy bize bir webhook göndermiş olmalı, şimdi kontrol edelim.

## Hata Ayıklama ve Sorunları Yönetme

Ngrok web arayüzüne döndüğünüzde birkaç başarısız istek göreceksiniz. Biraz beklerseniz… bir tane daha görünecek. Bunlar LemonSqueezy’nin gönderip yanıt alamadığı aynı istekler. Her seferinde 406 Not Acceptable durum kodu döndürdüğümüz için LemonSqueezy başarılı şekilde işlendiğini düşünmüyor ve tekrar deniyor. Neden mi?

Sunucumuz başarılı bir durum kodu döndürmediğinde, LemonSqueezy 5 saniye sonra tekrar dener, sonra 25 saniye sonra tekrar dener ve son olarak 125 saniye sonra bir kez daha dener. Hala başarısızsa, LemonSqueezy pes eder çünkü sonsuza dek tekrar deneyemez. Yani gerekirse LemonSqueezy panelinden manuel olarak yeniden göndermeniz gerekebilir.

## Sonuç

Artık LemonSqueezy, yerel sitemizle gerçek zamanlı olarak iletişim kurabiliyor, böylece gerçek webhook işlemlerini uygulamaya hazırız. Şimdi bunu yapalım!
