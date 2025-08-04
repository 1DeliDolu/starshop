# 📩 The Webhook Component for Email Events / E-posta Olayları için Webhook Bileşeni

Mailtrap'ta, üretimde e-postaları gönderdiğimizde, her bir e-postayı kontrol edebileceğimizi unutmayın: gönderildi mi, teslim edildi mi, açıldı mı, geri döndü mü (bu önemli!) ve daha fazlası. Mailtrap, bu olaylar hakkında bize bilgi gönderebilmesi için bir `webhook` URL'si ayarlamamıza olanak tanır.

## 🧩 Webhook & RemoteEvent Components / Webhook ve RemoteEvent Bileşenleri

Ek olarak, iki yeni Symfony bileşenini keşfetmiş oluyoruz! Terminalinizi açın ve şu komutu çalıştırın:

```shell
composer require webhook remote-event
```

👉 Bu komut, `webhook` ve `remote-event` bileşenlerini projeye ekler.

`webhook` bileşeni, tüm `webhook`'ların gönderileceği tek bir uç nokta sağlar. Bize gönderilen verileri - buna yük (`payload`) denir - ayrıştırır, bir uzaktan olay (`remote event`) nesnesine dönüştürür ve bir tüketiciye (`consumer`) gönderir. Uzaktan olayları, Symfony olaylarına benzetebilirsiniz. Uygulamanızın bir olay tetiklemesi yerine, üçüncü taraf bir servis bunu yapar - bu yüzden uzaktan olay denir. Olay dinleyicileri (`listener`) yerine, uzaktan olayların tüketicileri (`consumer`) vardır.

Şunu çalıştırın:

```shell
git status
```

👉 Bu komut, Symfony tarifinin (`recipe`) neler eklediğini gösterir: `config/routes/webhook.yaml`.

Bu, `webhook` denetleyicisini (`controller`) ekler. Rotayı şu komutla kontrol edin:

```shell
symfony console debug:route webhook
```

👉 Bu komut, `webhook` rotasını ve yolunu listeler.

İlk rotayı kontrol edin. Yol `/webhook/{type}` şeklindedir. Şimdi bir tür (`type`) yapılandırmamız gerekir.

Mailtrap veya bir ödeme işlemcisi ya da bir süpernova uyarı sistemi gibi üçüncü taraf `webhook`'lar bize çok farklı yükler gönderebilir, bu nedenle genellikle kendi ayrıştırıcılarımızı ve uzaktan olaylarımızı oluşturmamız gerekir. E-posta olayları oldukça standart olduğundan, Symfony bunlar için hazır uzaktan olaylar sağlar: `MailerDeliveryEvent` ve `MailerEngagementEvent`. Bazı posta köprüleri (`mailer bridge`), kullandığımız Mailtrap köprüsü de dahil olmak üzere, her servisin `webhook` yükünü ayrıştırmak ve bu nesneleri oluşturmak için ayrıştırıcılar sağlar. Sadece yapılandırmamız yeterli.

## ⚙️ Mailtrap Parser Configuration / Mailtrap Ayrıştırıcı Yapılandırması

`config/packages/` dizininde bir `webhook.yaml` dosyası oluşturun. Şunları ekleyin: `framework`, `webhook`, `routing`, `mailtrap` (URL'de kullanılan tür budur) ve ardından `service`. Mailtrap ayrıştırıcı servis kimliğini (`service id`) bulmak için Symfony Webhook dokümantasyonuna bakın. Mailtrap için servis kimliğini bulun, kopyalayın... ve buraya yapıştırın:


```yaml
# config/packages/webhook.yaml
framework:
    webhook:
        routing:
            mailtrap:
                service: mailer.webhook.request_parser.mailtrap
```

👉 Bu yapılandırma, `mailtrap` için webhook ayrıştırıcıyı belirler.

## 👤 EmailEventConsumer

Şimdi bir tüketiciye ihtiyacımız var. `App\Webhook` ad alanında (`namespace`) `EmailEventConsumer` adında yeni bir sınıf oluşturun. Bu sınıf, `RemoteEvent`'ten `ConsumerInterface`'i uygulamalıdır. Gerekli `consume()` metodunu ekleyin. Symfony'ye hangi `webhook` türünü tüketmek istediğimizi belirtmek için, `#[AsRemoteEventConsumer]` özniteliğini (`attribute`) `mailtrap` ile ekleyin:


```php
// src/Webhook/EmailEventConsumer.php
// ... lines 1 - 10
#[AsRemoteEventConsumer('mailtrap')]
class EmailEventConsumer implements ConsumerInterface
{
// ... lines 14 - 16
    public function consume(RemoteEvent $event): void
    {
// ... line 19
    }
}
```

👉 Bu sınıf, `mailtrap` türü webhook olaylarını tüketmek için ayarlanmıştır.

`consume()` metodunun üstüne, IDE'mize yardımcı olması için bir doküman bloğu (`docblock`) ekleyin: `@param MailerDeliveryEvent|MailerEngagementEvent $event`:


```php
// src/Webhook/EmailEventConsumer.php
// ... lines 1 - 11
class EmailEventConsumer implements ConsumerInterface
{
    /**
     * @param MailerDeliveryEvent|MailerEngagementEvent $event
     */
    public function consume(RemoteEvent $event): void
    {
// ... line 19
    }
}
```

👉 Bu açıklama, `consume()` metodunun hangi tür olayları alabileceğini belirtir.

Bunlar, Symfony'nin sunduğu genel posta uzaktan olaylarıdır. İçeride, `$event->` yazınca mevcut metodları görebilirsiniz.

Gerçek bir uygulamada, burada bu olaylarla bir şeyler yaparsınız; örneğin veritabanına kaydedersiniz veya bir e-posta geri dönerse bir yöneticiyi bilgilendirirsiniz. Aslında bir e-posta birkaç kez geri dönerse, tekrar denememek için bir şeyi güncellemek isteyebilirsiniz çünkü bu, e-posta güvenilirliğinize zarar verebilir. Ancak bizim için sadece `dump($event);` yazmamız yeterli:


```php
// src/Webhook/EmailEventConsumer.php
// ... lines 1 - 11
class EmailEventConsumer implements ConsumerInterface
{
// ... lines 14 - 16
    public function consume(RemoteEvent $event): void
    {
        dump($event);
    }
}
```

👉 Bu kod, gelen uzaktan olayı ekrana döker.

## 🔄 Asynchronous Consumers / Asenkron Tüketiciler

Son bir şey: `webhook` denetleyicisi, uzaktan olayı tüketiciye Symfony Messenger aracılığıyla, `ConsumeRemoteEventMessage` adında bir mesaj sınıfı içinde gönderir.

Bunu asenkron olarak işlemek ve webhook yanıtlarınızı hızlı tutmak için, `config/packages/messenger.yaml` dosyasında, `routing` altında `Symfony\Component\RemoteEvent\Messenger\ConsumeRemoteEventMessage` ekleyin ve `async` taşıyıcısına yönlendirin:


```yaml
// config/packages/messenger.yaml
framework:
    messenger:
# ... lines 3 - 11
        routing:
# ... lines 13 - 15
            'Symfony\Component\RemoteEvent\Messenger\ConsumeRemoteEventMessage': async
# ... lines 17 - 25
```

👉 Bu yapılandırma, uzaktan olayların asenkron şekilde işlenmesini sağlar.

Tamam! Artık bu webhook'u demo etmeye hazırız. Sıradaki adım bu!
