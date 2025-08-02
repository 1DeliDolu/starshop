# 💤 Lazy Services / Lazy (Tembel) Servisler

Symfony'nin en sevdiğim özelliklerinden biri: lazy servisler. Çoğu zaman bir servisi enjekte edersiniz, fakat sadece belirli koşullarda kullanırsınız. İşte bir örnek:

```php
public function calculateSomething(ExpensiveService $service)
{
    if ($this->isCached()) {
        return $this->getCachedValue();
    }

    return $service->doSomethingExpensive();
}
```

👉 Bu fonksiyonda, çoğu zaman `ExpensiveService` kullanılmaz. Fakat enjekte edildiği için her zaman örneklenir.

"Lazy" (tembel) bir servis, gerçekten ihtiyaç duyulana kadar dinlenir.

## ➕ Create a New Button / Yeni Bir Buton Oluşturmak

`src/Remote/` dizininde yeni bir PHP sınıfı oluşturun: `ParentalControls`. Bu sınıf, çocuklar uygunsuz bir şey yaptığında uyarı gönderecek. Sınıfı `final` olarak işaretleyin ve yapıcıya (constructor) şu parametreyi ekleyin: `private MailerInterface $mailer` (böylece uyarıları e-posta ile gönderebiliriz):


```php
// src/Remote/ParentalControls.php
// ... lines 1 - 6
final class ParentalControls
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }
// ... lines 13 - 17
}
```

👉 Bu sınıf, uyarı göndermek için mailer servisini kullanır.

Yeni bir public metod ekleyin: `volumeTooHigh()`, dönüş tipi void olsun. İçeride, e-posta gönderimini simüle etmek için sadece `dump('send volume alert email')` yazın:


```php
// src/Remote/ParentalControls.php
// ... lines 1 - 6
final class ParentalControls
{
// ... lines 9 - 13
    public function volumeTooHigh(): void
    {
        dump('send volume alert email');
    }
}
```

👉 Bu metod, yüksek ses uyarısını simüle eder.

Şimdi `VolumeUpButton`'ı açın, bir constructor ekleyin ve `private ParentalControls $parentalControls` enjekte edin:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 8
final class VolumeUpButton implements ButtonInterface
{
    public function __construct(
        private ParentalControls $parentalControls,
    ) {
    }
// ... lines 15 - 23
}
```

👉 Bu constructor, `ParentalControls` servisini enjekte eder.

`press()` metodunda, sesi çok yüksek algıladığımızı varsayın. Bir `if (true)` ekleyin (yorum ile belirtin), ardından `$this->parentalControls->volumeTooHigh();` çağrısı yapın:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 8
final class VolumeUpButton implements ButtonInterface
{
// ... lines 11 - 15
    public function press(): void
    {
        if (true) { // determine if volume is too high
            $this->parentalControls->volumeTooHigh();
        }
// ... lines 21 - 22
    }
}
```

👉 Bu kodda, ses yüksekse uyarı göndermek simüle edilir.

Uygulamaya dönüp sayfayı yenileyin, "volume up" butonuna basın ve profilere bakın. `ParentalControls` servisinin kullanıldığını göreceksiniz.

Şimdi, `VolumeUpButton`'da true'yu false olarak değiştirin (yüksek ses algılanmadığını varsayın). if bloğunun altına `dump($this->parentalControls);` yazın:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 8
final class VolumeUpButton implements ButtonInterface
{
// ... lines 11 - 15
    public function press(): void
    {
        if (false) { // determine if volume is too high
            $this->parentalControls->volumeTooHigh();
        }
        dump($this->parentalControls);
// ... lines 23 - 24
    }
}
```

👉 Bu durumda, `ParentalControls` servisi kullanılmıyor gibi görünse de yine de örneklenir.

Uygulamaya dönüp sayfayı yenileyin, "volume up" butonuna basın ve profilere bakın. `ParentalControls` ve ona bağlı olan mailer servisi, mailer transport vs. hepsi yine de örneklenmiş olur!

## 🏷️ #\[Lazy] Class Attribute / #\[Lazy] Sınıf Özniteliği

Çözüm ne? `ParentalControls`'u lazy servis yapın. Sınıfın başına `#[Lazy]` ekleyin:


```php
// src/Remote/ParentalControls.php
// ... lines 1 - 7
#[Lazy]
final class ParentalControls
// ... lines 10 - 21
```

👉 Bu şekilde, bu servis ihtiyaç duyulana kadar başlatılmaz.

Uygulamayı yenileyin ve... bir hata!

```
Cannot generate lazy proxy for service ParentalControls.
```

Önceki istisnaya bakın:

```
Class ParentalControls is final.
```

Lazy servislerde küçük bir dezavantaj: Sınıf `final` olamaz. Nedenini göreceğiz.

`ParentalControls`'da `final` anahtarını kaldırın:


```php
// src/Remote/ParentalControls.php
// ... lines 1 - 8
#[Lazy]
class ParentalControls
// ... lines 10 - 21
```

👉 Artık sınıf final olmadığı için lazy proxy oluşturulabilir.

Yeniden uygulamayı yenileyin ve tekrar deneyin.

## 👻 Ghost Proxies

Vay! Ne oldu? `ParentalControlsGhost` gibi rastgele bir string mi göründü? Bu, "ghost proxy" olarak adlandırılır ve Symfony tarafından oluşturulur. Sizin sınıfınızı (bu yüzden final olamaz) genişletir ve gerçekten kullanılana kadar tam olarak başlatılmaz — bir hayalet gibi!

Peki ya `ParentalControls`'a sahip değilsek? Mesela bir üçüncü parti paketten geliyorsa? Sınıfı lazy yapmak için vendor kodunu düzenleyemeyiz, fakat `#[Lazy]` özniteliğini bir argümana ekleyerek, sadece o kullanımda lazy hale getirebiliriz.

## 🏷️ #\[Lazy] Argument Attribute / #\[Lazy] Parametre Özniteliği

`ParentalControls` içinden `#[Lazy]` özniteliğini kaldırın:


```php
// src/Remote/ParentalControls.php
// ... lines 1 - 8
class ParentalControls
// ... lines 10 - 21
```

ve `VolumeUpButton`'da, `$parentalControls` parametresinin üzerine `#[Lazy]` ekleyin:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 9
final class VolumeUpButton implements ButtonInterface
{
    public function __construct(
        #[Lazy]
        private ParentalControls $parentalControls,
    ) {
    }
// ... lines 17 - 27
}
```

👉 Bu şekilde, sadece bu kullanımda `ParentalControls` lazy olur.

Uygulamayı yenileyin, "volume up" butonuna basın ve profilere bakın. Hâlâ lazy!

Sınıfa `#[Lazy]` eklediğinizde, bu servisin tüm örnekleri lazy olur. Argümana eklediğinizde ise sadece o kullanım için lazy olur.

Peki ya üçüncü parti paketten geliyorsa ve final ise? Şansımız bitti mi?

Hayır! Symfony'nin başka birkaç numarası ve özniteliği daha var. Sıradaki adımda onları keşfedeceğiz!
