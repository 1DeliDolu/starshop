
# ✉️ Global From (and Fun) with Email Events / Global From (ve Eğlence) E-posta Olaylarıyla

Çoğu, hatta belki de tüm uygulamanızın gönderdiği e-postaların aynı e-posta adresinden gönderileceğine eminim. Örneğin `hal9000@universal-travel.com` gibi yaratıcı bir adres ya da klasik ve daha durağan olan `info@universal-travel.com`.

Her e-postada aynı `from` adresi olacağı için, her e-postada bunu ayarlamaya gerek yok. Bunun yerine, bunu global olarak ayarlayalım. İlginç bir şekilde, bunun için küçük bir yapılandırma seçeneği yok. Ama bu bizim için harika: Çünkü bu sayede olayları (`event`) öğrenme şansı buluyoruz! Çok güçlü ve çok teknik.

## 📨 The MessageEvent / MessageEvent

Bir e-posta gönderilmeden önce, Mailer bir `MessageEvent` tetikler.

Bunu dinlemek için terminalinizi açın ve şu komutu çalıştırın:

```shell
symfony console make:listener
```

👉 Bu komut, bir event listener sınıfı oluşturur.

Buna `GlobalFromEmailListener` adını verin. Bu komut, dinleyebileceğimiz olayların bir listesini sunar. Biz ilk olanı, yani `MessageEvent` istiyoruz. `Symfony` yazmaya başlayınca otomatik tamamlanacak. Enter’a basın.

Listener oluşturuldu!

Daha havalı olmak için, global `from` adresimizi bir parametre olarak ayarlayalım. `config/services.yaml` dosyasında, `parameters` altında yeni bir tane ekleyin: `global_from_email`.

## 📧 Special Email Address String / Özel E-posta Adresi Dizesi

Bu bir metin olacak, ama şuna dikkat edin: Önce gönderen adı (ör: Universal Travel) yazılır, sonra açılı parantez içinde e-posta: `<info@universal-travel.com>`:


```yaml
// config/services.yaml
// ... lines 1 - 5
parameters:
    global_from_email: 'Universal Travel <info@universal-travel.com>'
// ... lines 8 - 26
```

👉 Bu satır, gönderici adı ve adresini birleştirir.

Symfony Mailer, e-posta adresi olarak bu şekilde görünen bir dizeyi algıladığında, hem adı hem e-posta adresi ayarlanmış bir `Address` nesnesi oluşturur.

## 🎧 MessageEvent Listener / MessageEvent Dinleyicisi

Yeni oluşturulan `src/EventListener/GlobalFromEmailListener.php` sınıfını açın. Bir kurucu metodu ekleyin, içinde `private string $fromEmail` argümanı olsun ve buna `%global_from_email%` parametresini `[Autowire]` ile atayın:


```php
// src/EventListener/GlobalFromEmailListener.php
// ... lines 1 - 8
final class GlobalFromEmailListener
{
    public function __construct(
        #[Autowire('%global_from_email%')]
        private string $fromEmail,
    ) {
    }
// ... lines 16 - 21
}
```

👉 Bu kod, dinleyiciye global `from` adresini parametre olarak iletir.

Aşağıda, `[AsEventListener]` attribute’u bu metodu bir event listener olarak işaretler. Aslında, bu olay argümanını kaldırabiliriz - metot argümanının türü sayesinde otomatik olarak belirlenir: `MessageEvent`:


```php
// src/EventListener/GlobalFromEmailListener.php
// ... lines 1 - 9
final class GlobalFromEmailListener
{
// ... lines 12 - 17
    #[AsEventListener]
    public function onMessageEvent(MessageEvent $event): void
    {
// ... lines 21 - 31
    }
}
```

👉 Bu metodun event listener olarak çalışmasını sağlar.

İçeride, önce event’ten mesajı alın: `$message = $event->getMessage();`


```php
// src/EventListener/GlobalFromEmailListener.php
// ... lines 1 - 9
final class GlobalFromEmailListener
{
// ... lines 12 - 18
    public function onMessageEvent(MessageEvent $event): void
    {
        $message = $event->getMessage();
// ... lines 22 - 31
    }
}
```

👉 Bu satır, event’ten e-posta mesajını alır.

`getMessage()` metodunun ne döndürdüğüne bakabilirsiniz. `RawMessage`... bu sınıfı inceleyin ve hangi sınıfların bunu genişlettiğine bakın. `TemplatedEmail`! Mükemmel!

Dinleyicide, `if (!$message instanceof TemplatedEmail)` yazın ve içeriye `return;` ekleyin:


```php
// src/EventListener/GlobalFromEmailListener.php
// ... lines 1 - 9
final class GlobalFromEmailListener
{
// ... lines 12 - 18
    public function onMessageEvent(MessageEvent $event): void
    {
// ... lines 21 - 22
        if (!$message instanceof TemplatedEmail) {
            return;
        }
// ... lines 26 - 31
    }
}
```

👉 Bu kontrol, sadece `TemplatedEmail` ile çalışılmasını garanti altına alır.

Bu muhtemelen hiç olmayacak, ama kontrol etmek iyi bir alışkanlık. Ayrıca, IDE’nin `$message` değişkeninin artık `TemplatedEmail` olduğundan emin olmasını sağlar.

Bir e-posta yine de kendi `from` adresini ayarlayabilir. Böyle bir durumda, bunu değiştirmek istemeyiz. Bu yüzden bir koruma ekleyin: `if ($message->getFrom()) return;`:


```php
// src/EventListener/GlobalFromEmailListener.php
// ... lines 1 - 9
final class GlobalFromEmailListener
{
// ... lines 12 - 18
    public function onMessageEvent(MessageEvent $event): void
    {
// ... lines 21 - 26
        if ($message->getFrom()) {
            return;
        }
// ... lines 30 - 31
    }
}
```

👉 Eğer e-posta zaten bir `from` adresi belirlediyse, müdahale edilmez.

Şimdi global `from` adresini ayarlayabiliriz: `$message->from($this->fromEmail);`


```php
// src/EventListener/GlobalFromEmailListener.php
// ... lines 1 - 9
final class GlobalFromEmailListener
{
// ... lines 12 - 18
    public function onMessageEvent(MessageEvent $event): void
    {
// ... lines 21 - 30
        $message->from($this->fromEmail);
    }
}
```

👉 Bu satır, global gönderici adresini ayarlar.

Mükemmel!

`TripController::show()` içinde, artık e-posta için `->from()` kısmını kaldırabilirsiniz.

Şimdi bunu test etme zamanı! Uygulamanızda bir rezervasyon yapın ve Mailtrap’te e-postayı kontrol edin. Beklenen sonuç: gönderici adresi doğru şekilde ayarlanmış! Dinleyicimiz çalışıyor!

## 📬 Reply-To / Yanıtla-Adresi

Bir detayı daha tamamlayalım.

Bir iletişim formunu düşünün: kullanıcı adını, e-postasını ve mesajını dolduruyor. Bu bilgiler destek ekibinize e-posta olarak gönderiliyor. Destek ekibinizin e-posta istemcisinde, yanıtla’ya bastıklarında bu yanıtın, kullanıcının e-posta adresine gitmesi hoş olurdu - sadece “global from” adresinize değil.

Belki `from` adresini kullanıcının e-posta adresi yapmayı düşünebilirsiniz. Ama bu işe yaramaz, çünkü o kullanıcı adına e-posta göndermeye yetkiniz yoktur. E-posta güvenliğiyle ilgili detaylara birazdan değineceğiz.

Neyse ki, bu senaryo için özel bir e-posta başlığı var: `Reply-To`. E-postanızı oluştururken `->replyTo()` ile kullanıcının e-posta adresini girin.

Her şey hazır, artık gerçek ortamda e-posta göndermeye hazırsınız!
