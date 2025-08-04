# 🚀 Async & Retryable Sending with Messenger / Messenger ile Asenkron ve Yeniden Denenebilir Gönderim

Bu e-postayı gönderdiğimizde, hemen yani eşzamanlı olarak gönderiliyor. Bu da, e-posta göndermek için `mailer transport` ile bağlantı kurarken kullanıcının bir gecikme yaşaması anlamına gelir. Ve eğer e-posta gönderiminde bir ağ sorunu olursa, kullanıcı bir `500` hatası görür: bu da sizi bir rokete bağlayacak bir şirket için pek güven vermiyor.

Bunun yerine, e-postalarımızı asenkron olarak gönderelim. Bu, istek sırasında e-postanın işlenmek üzere bir kuyruğa gönderileceği anlamına gelir. `Symfony Messenger` bunun için mükemmeldir! Ve şu avantajları sağlar: kullanıcı için daha hızlı yanıtlar, e-posta başarısız olursa otomatik tekrar denemeler ve çok fazla başarısızlık olursa e-postaları manuel inceleme için işaretleme imkânı.

## 🛠️ Installing Messenger & Doctrine Transport / Messenger ve Doctrine Transport'un Kurulumu

`Messenger`'ı yükleyelim! Terminalinizde şu komutu çalıştırın:

```bash
composer require messenger
```

👉 Bu komut, `Messenger` paketini projeye ekler.

`Mailer` gibi, `Messenger` da bir `transport` (taşıyıcı) kavramına sahiptir: mesajların kuyruklanmak üzere gönderildiği yerdir. Kurulumu en kolay olduğu için `Doctrine transport`u kullanacağız.

```bash
composer require symfony/doctrine-messenger
```

👉 Bu komut, `Doctrine transport`u projeye ekler.

IDE'ye döndüğümüzde, tarifin `.env` dosyamıza şu `MESSENGER_TRANSPORT_DSN` anahtarını eklediğini göreceğiz ve varsayılan olarak `Doctrine` kullanılıyor – harika! Bu `transport`, veritabanımıza bir tablo ekler, bu nedenle teknik olarak bunun için bir migration oluşturmalıyız. Ancak... biraz hile yapıp tabloyu otomatik olarak oluşturmasını sağlayacağız. Bunun için, `auto_setup` değerini `1` olarak ayarlayın:


```dotenv
// .env
// ... lines 1 - 40
###> symfony/messenger ###
// ... lines 42 - 44
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=1
###< symfony/messenger ###
```

👉 Bu ayar, eksikse tabloyu otomatik oluşturur.

## ⚙️ Configuring Messenger Transports / Messenger Taşıyıcılarının Yapılandırılması

Tarif ayrıca `config/packages/messenger.yaml` dosyasını oluşturdu. `failure_transport` satırının başındaki yorumu kaldırın:


```yaml
# config/packages/messenger.yaml
framework:
    messenger:
// ... line 3
        failure_transport: failed
// ... lines 5 - 24
```

👉 Bu, manuel hata inceleme sistemini etkinleştirir.

Ardından, `transports` altında `async` satırının başındaki yorumu kaldırın:


```yaml
# config/packages/messenger.yaml
framework:
    messenger:
// ... lines 3 - 5
        transports:
// ... line 7
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
// ... lines 9 - 24
```

👉 Bu, `MESSENGER_TRANSPORT_DSN` ile yapılandırılmış taşıyıcıyı etkinleştirir ve adını `async` olarak belirler.

Burada açıkça görünmese de, başarısız mesajlar 3 kez yeniden denenir, her denemede bekleme süresi artar. Eğer bir mesaj 3 denemeden sonra hala başarısız olursa, `failure_transport` olarak adlandırılan `failed` taşıyıcısına gönderilir. Bunun için de şu taşıyıcıyı aktif edin:


```yaml
# config/packages/messenger.yaml
framework:
    messenger:
// ... lines 3 - 5
        transports:
// ... lines 7 - 8
            failed: 'doctrine://default?queue_name=failed'
// ... lines 10 - 24
```

👉 Bu, başarısız mesajlar için ayrı bir taşıyıcı tanımlar.

## 🛤️ Configuring Messenger Routing / Messenger Yönlendirmesini Yapılandırma

`routing` bölümü, Symfony'ye hangi mesajların hangi taşıyıcıya gönderileceğini bildirir. `Mailer`, e-posta göndermek için özel bir mesaj sınıfı kullanır. Bu yüzden `Symfony\Component\Mailer\Messenger\SendEmailMessage` mesajını `async` taşıyıcısına gönderin:


```yaml
# 13_config/packages/messenger.yaml
framework:
    messenger:
// ... lines 3 - 11
        routing:
// ... lines 13 - 14
            'Symfony\Component\Mailer\Messenger\SendEmailMessage': async
// ... lines 16 - 24
```

👉 Bu satır, e-posta mesajlarının `async` taşıyıcıya yönlendirilmesini sağlar.

Hepsi bu kadar! `Symfony Messenger` ve `Mailer` birlikte mükemmel çalışır, bu yüzden kodumuzda başka bir değişiklik yapmamıza gerek yok.

## 🧪 Let's Test! / Test Edelim!

Uygulamaya dönün... bir seyahat rezervasyonu yapın. Tekrar `Mailtrap` test taşıyıcısını kullandığımız için herhangi bir e-posta adresini kullanabilirsiniz. Artık işlemin ne kadar hızlı gerçekleştiğine dikkat edin.

Boom!

## ⏳ Status: Queued / Durum: Kuyrukta

Son isteğin profiler'ını açın ve "Emails" bölümüne bakın. Her zamanki gibi görünüyor, ama durumun "`Queued`" olduğunu fark edin. Mesajımız `messenger transport`a gönderildi, `mailer transport`a değil. Yeni bir "Messages" bölümümüz var. Burada, `SendEmailMessage` ve içindeki `TemplatedEmail` nesnesini görebilirsiniz.

`Mailtrap`'a gidip yenileyin... henüz bir şey yok. Tabii! Kuyruğumuzu işlememiz gerekiyor.

## 🔄 Processing the Queue / Kuyruğu İşlemek

Terminalde şu komutu çalıştırın:

```bash
symfony console messenger:consume async -vv
```

👉 Bu komut, `async` taşıyıcıyı işler (`-vv` daha fazla çıktı verir).

Mesaj alındı ve başarıyla işlendi. Yani: e-posta gerçekten gönderilmiş olmalı.

`Mailtrap`'ı kontrol edin... işte burada! Her şey doğru görünüyor... fakat... bağlantılardan birine tıklayın.

Ne oluyor? URL yanlış bir domain içeriyor! Kötü... Hangi bölümde hata yaptığımızı bulup, bir sonraki adımda bunu düzelteceğiz!
