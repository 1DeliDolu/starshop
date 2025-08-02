# 🎯 Enforce Named Autowiring with Target / Target ile İsimli Otomatik Bağlamayı Zorunlu Kılmak

Son bölümde günlüklemeyi ekledik! "Logs" profil paneline bakın. Şu küçük "app" etiketi dikkatinizi çekti mi? Symfony'nin logger'ı olan `Monolog`, log kanalları (log channels) kavramına sahiptir; bunlar, log mesajlarınız için kategoriler gibidir. Varsayılan kanal `app`'tir, fakat başka kanallar da oluşturabilirsiniz.

## ➕ Adding a New Log Channel / Yeni Bir Log Kanalı Eklemek

Yeni bir `button` adında kanal ekleyip ekleyemeyeceğimize bakalım. `config/packages/monolog.yaml` dosyasını açın. `channels` altında yeni bir `button` kanalı ekleyin:


```yaml
// config/packages/monolog.yaml
monolog:
    channels:
        - button
// ... lines 5 - 64
```

👉 Bu kodda, yeni bir `button` kanalı eklenmiştir.

Mükemmel! Dosyayı kapatın.

`LoggerRemote`'un bu yeni kanalı kullanmasını istiyoruz... fakat bunu nasıl yapacağız?

Otomatik bağlama (autowiring) ile `LoggerInterface` servisini kullanıyoruz. Bu bize ana logger'ı, yani "app" kanalına log atanı verir. Yeni kanala log atmak için farklı bir logger servisi bağlamamız gerekir.

Terminalde şunu çalıştırın:

```bash
bin/console debug:autowiring logger
```

👉 Bu komut, `logger` ismi geçen servisleri listeler.

Buradaki ilk servis `LoggerInterface`: Standart otomatik bağlama ile elde ettiğiniz servis budur. Fakat birden fazla logger servisi var. Diğerleri, parametrelerinde bir isim içerir ve yeni eklenen `buttonLogger` servisi de bunlardan biri! Aradığımız servis bu. Bu servisi almak için isimli otomatik bağlama (named autowiring) kullanmalıyız.

## 🏷️ Named Autowiring / İsimli Otomatik Bağlama

`LoggerRemote` içinde, parametre adını `$buttonLogger` olarak değiştirin:


```php
// src/Remote/LoggerRemote.php
// ... lines 1 - 8
final class LoggerRemote implements RemoteInterface
{
    public function __construct(
        private LoggerInterface $buttonLogger,
    ) {
    }
// ... lines 16 - 36
}
```

👉 Bu kodda, `button` kanalına log atmak için ilgili logger otomatik bağlanır.

Şimdi tekrar uygulamaya dönün, sayfayı yenileyin ve "Channel Up" tuşuna basın. Son isteğin profilinde "Logs" paneline bakın. Artık `button` kanalına log atıyoruz!

Bu olması gerektiği gibi çalışıyor; ancak isimli otomatik bağlamada oluşabilecek bir problemden bahsetmek istiyorum. Farz edin, bir yıl sonra bu değişkenin adını basitçe `$logger` olarak değiştirdik:


```php
// src/Remote/LoggerRemote.php
// ... lines 1 - 9
final class LoggerRemote implements RemoteInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }
// ... lines 18 - 38
}
```

👉 Parametre adı `$logger` olarak değiştirildiğinde, standart otomatik bağlamaya dönülür.

Uygulamayı yenileyin ve bir butona basın. Görünüşte hâlâ çalışıyor; ama "Logs" panelini kontrol edin. Mesaj hâlâ orada, fakat gizlice tekrar "app" kanalına log atıyor!

Parametre adını değiştirdiğimizde isimli otomatik bağlamayı bozduk ve tekrar standart bağlamaya döndük. Bu günlükleme için çok büyük bir sorun olmayabilir, ancak veritabanı veya önbellek servisleri bu şekilde bağlanıyorsa ciddi sorunlara yol açabilir.

## 🛡️ Target Attribute / Target Özniteliği

İsimli otomatik bağlamayı zorunlu kılacak bir yol gerek. İşte karşınızda `#[Target]` özniteliği!

`LoggerRemote` içinde, `LoggerInterface $logger` parametresinin üzerine `#[Target]` ekleyin. İçine, debug\:autowiring komutunda gördüğümüz hedef servis adını (`buttonLogger`, \$ işareti olmadan) yazın:


```php
// src/Remote/LoggerRemote.php
// ... lines 1 - 9
final class LoggerRemote implements RemoteInterface
{
    public function __construct(
        #[Target('buttonLogger')]
        private LoggerInterface $logger,
    ) {
    }
// ... lines 18 - 38
}
```

👉 Burada, parametre adı ne olursa olsun, doğru servis bağlanır.

Artık parametre adı istediğiniz gibi olabilir!

Tekrar uygulamaya dönün, sayfayı yenileyin, "Volume Up" tuşuna basın ve "Logs" panelini kontrol edin. Yine `button` kanalına log atıyoruz!

## 🧰 Enforcing Named Autowiring / İsimli Otomatik Bağlamayı Zorunlu Kılmak

Zorunlu kılmayı göstermek için `config/packages/monolog.yaml` dosyasında kanal adını `buttons` (sonuna s ekleyin) olarak değiştirin:


```yaml
// config/packages/monolog.yaml
monolog:
    channels:
        - buttons
// ... lines 5 - 64
```

👉 Kanal adı `buttons` olarak değiştirildi.

Uygulamayı yenileyin ve... bir hata göreceksiniz!

```
Cannot autowire service "LoggerRemote": argument "$logger" of method "__construct()" has "#[Target('buttonLogger')]" but no such target exists.
```

👉 Bu hata, hedef servis bulunamadığında oluşur.

Evet! Genelde hatalara sevinmeyiz ama bu harika bir hata! Artık isimli otomatik bağlama servisi bulunamazsa doğrudan hata alıyoruz. İşte istediğimiz bu!

Bunu düzeltmek için `LoggerRemote`'da `#[Target]` özniteliğini `buttonsLogger` (sonuna s ekleyerek) olarak güncelleyin:


```php
// src/Remote/LoggerRemote.php
// ... lines 1 - 9
final class LoggerRemote implements RemoteInterface
{
    public function __construct(
        #[Target('buttonsLogger')]
        private LoggerInterface $logger,
    ) {
    }
// ... lines 18 - 38
}
```

👉 Artık yeni kanal adı ile otomatik bağlama tekrar düzgün çalışır.

Uygulamayı yenileyin ve tekrar iş başına dönün! "Mute" tuşuna basın ve "Logs" paneline bakın. Evet, artık loglar `buttons` kanalına atılıyor!

İsimli otomatik bağlamayı seviyorum, ama bunu `#[Target]` özniteliği ile zorunlu kılmayı daha çok seviyorum.

Sonraki adım: Yeni bir buton ekleyeceğiz; fakat bu sefer, sadece belirli ortamlarda görünecek şekilde koşullu olacak.
