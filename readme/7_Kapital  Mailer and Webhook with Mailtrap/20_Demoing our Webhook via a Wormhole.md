# 🧪 Demoing our Webhook via a Wormhole / Webhook'umuzu Bir Solucan Deliğiyle Test Etmek

Mailtrap webhook'unu test etme zamanı!

Öncelikle, geliştirme ortamımızı tekrar üretim modunda e-posta gönderecek şekilde değiştirmemiz gerekiyor. `.env.local` dosyasında, üretim Mailtrap `MAILER_DSN` değerine geçin ve `config/services.yaml` dosyasında, `global_from_email`'in alan adının Mailtrap ile yapılandırdığınız alan adı olduğundan emin olun.

## 🔗 Create a Webhook on Mailtrap / Mailtrap'ta Bir Webhook Oluşturun

Mailtrap'ta, "Settings" > "Webhooks" bölümüne gidin ve "Create New Webhook" butonuna tıklayın. İlk ihtiyacımız olan şey bir `Webhook URL`. Hmm, bu `/webhook/mailtrap` olmalı ama mutlak bir URL olmalı. Üretimde bu sorun olmaz: kendi alan adınızı kullanırsınız. Geliştirmede ise işler biraz daha karışık. Symfony CLI sunucusunun verdiği URL'yi doğrudan kullanamayız...

## 🌍 ngrok

Bir şekilde yerel Symfony sunucumuzu herkese açık hale getirmemiz gerekiyor. Tam olarak bunu yapan güzel bir araç var: `ngrok`. Ücretsiz bir hesap oluşturun, giriş yapın ve `ngrok` CLI istemcisini yapılandırma talimatlarını izleyin.

Terminalde, Symfony web sunucusunu yeniden başlatın:

```shell
symfony server:stop
```

👉 Bu komut, Symfony web sunucusunu durdurur.

Çalışmıyorsa, şu komutla başlatın:

```shell
symfony serve -d
```

👉 Bu komut, Symfony sunucusunu arka planda başlatır.

## 🌐 Expose the Local Server / Yerel Sunucuyu Yayınla

Yayınlamak istediğimiz URL'yi kopyalayın ve şu komutu çalıştırın:

```shell
ngrok http <paste-url>
```

👉 Bu komut, yerel Symfony sunucunuzu herkese açık bir tünel üzerinden erişilebilir yapar.

URL'yi yapıştırın ve enter'a basın. Solucan deliği açıldı!

Bu karmaşık "Forwarding" URL'si artık herkese açık URL'nizdir. Kopyalayın ve tarayıcınızda açın. Bu uyarı, bir tünel üzerinden çalıştığınızı bildirir. "Visit Site" tıklayarak uygulamanızı görebilirsiniz.

## 📬 Mailtrap Webhook URL

Tekrar Mailtrap'a dönün, bu URL'yi yapıştırın ve sonuna `/webhook/mailtrap` ekleyin. "Select Stream" olarak "Transactional" seçin. "Select Domain" olarak yapılandırdığınız Mailtrap alan adını seçin. Tüm olayları seçin ve "Save" ile kaydedin.

Yeni webhook'a tekrar girin ve "Run Test" tıklayın.

Webhook URL test completed successfully

Bu iyiye işaret!

## 📢 Dump Server

`EmailEventConsumer`'da olayı sadece dump'ladığımızı unutmayın. Webhook'a gelen istekler arka planda işlendiği için dump'ı göremeyiz... ya da görebilir miyiz? Yeni bir terminalde şu komutu çalıştırın:

```shell
symfony console server:dump
```

👉 Bu komut, uygulamaya gelen dump'ları anlık olarak terminalde görmenizi sağlar.

Tarayıcıda bir rezervasyon yapın, gerçek bir e-posta adresi kullanmayı unutmayın (ama benimkini değil!)

## ✉️ MailerDeliveryEvent

Gerçek an! Dump sunucusu terminaline geri dönün, biraz bekleyin... Tamam! Bir dump geldi! Biraz yukarı kaydırın... Bu, teslim edilen bir `MailerDeliveryEvent`. Mailtrap'in verdiği dahili kimliği, ham yükü, tarihi, alıcı e-posta adresini, hatta özel meta verileri ve etiketi görebilirsiniz.

## 👀 MailerEngagementEvent

Şimdi bir etkileşim olayı deneyelim! E-posta istemcinizde e-postayı açın.

Tekrar dump server terminaline bakın, biraz bekleyin... ve işte! Bir başka olay! Bu sefer, açılma (`open`) için bir `MailerEngagementEvent`. Bu gerçekten harika!

Tebrikler! Böylece, Symfony Mailer'ın neredeyse tüm özelliklerini kullanıcılarımıza SPAM göndermeden keşfetmiş olduk. Kazançlı çıktı!

Bir sonraki sefere kadar mutlu kodlamalar!
