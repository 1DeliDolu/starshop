# 🏷️ Simplify with AutoconfigureTag and AsTaggedItem / AutoconfigureTag ve AsTaggedItem ile Basitleştirme

Önceki bölümde, denetleyicimizdeki büyük switch ifadesini küçük bir try-catch bloğuna dönüştürdük. Kumandamız eskisi gibi çalışıyor, ancak artık Komut tasarım desenini kullanıyoruz. Ayrıca bu container'ın nasıl bağlanacağını Symfony'ye anlatmak için `AutowireLocator` attribute'unu kullandık. Bu harika, ama size daha da basit bir yol olduğunu söylesem?

## 🏷️ #\[AutoconfigureTag]

Tüm butonlarımız `ButtonInterface` arayüzünü uyguladığı için, ikinci bağımlılık enjeksiyonu attribute'umuzu ekleyeceğiz: `#[AutoconfigureTag]`:


```php
// src/Remote/Button/ButtonInterface.php
// ... lines 1 - 6
#[AutoconfigureTag]
interface ButtonInterface
// ... lines 9 - 12
```

👉 Bu attribute, Symfony'ye şu mesajı verir:
Herhangi bir servis bu arayüzü uygularsa, ona bir tag ekle!

## 🏷️ Service Tags / Servis Etiketleri

Bir tag, bir servise bağlı olan bir string'dir ve tek başına bir şey yapmaz. Ancak artık servislerimizin bu etiketi var. Şimdi, ButtonRemote içindeki manuel servis eşlemesini, `AutowireLocator` attribute'unda tag ismi ile değiştirebiliriz:


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 10
    public function __construct(
        #[AutowireLocator(ButtonInterface::class)]
        private ContainerInterface $buttons,
    ) {
// ... lines 15 - 22
```

👉 Bu satır, tüm `ButtonInterface` implementasyonlarını tag adı ile container'a dahil eder.

`#[AutoconfigureTag]` attribute'unu kullandığınızda tag ismini özelleştirebilirsiniz. Ama bunu yapmazsanız, arayüz adı varsayılan olarak kullanılır. Biz de varsayılanı kullanıyoruz çünkü isim burada çok önemli değil.

Şimdi tekrar tarayıcıya dönelim, bir butona basalım ve... hata.

**Button "power" not found.**

Sayfanın aşağısında istisna detaylarını görebilirsiniz. İşte burada! Bu "not found" istisnası, `press()` metodunda fırlatıldı çünkü "power" ismini ButtonRemote'un container'ında bulamadı. Neyse ki, önceki istisna detaylarında container'ın bulduğu servis ID'lerini (butonların tam sınıf adları) görebiliyoruz. Yani butonlar bağlanmış, fakat ihtiyacımız olan ID ile değil.

## 🏷️ #\[AsTaggedItem]

Container'daki servis ID'lerinin, butonların slug isimleri olmasını istiyoruz. Bunu yapmak için sıradaki bağımlılık enjeksiyonu attribute'umuzu ekleyeceğiz: `#[AsTaggedItem]`. Bunu `ButtonInterface`'i uygulayan sınıflara eklemek, servis ID'sini özelleştirmemizi sağlar.

Öncelikle ChannelDownButton ile başlayalım. Sınıfa `#[AsTaggedItem]` ekleyin... ve ilk argüman olarak index için `channel-down` yazıyoruz.


```php
// src/Remote/Button/ChannelDownButton.php
// ... lines 1 - 6
#[AsTaggedItem('channel-down')]
final class ChannelDownButton implements ButtonInterface
// ... lines 9 - 15
```

👉 Bu attribute, servisin container'daki ismini `channel-down` olarak ayarlar.

Aynı işlemi ChannelUpButton, PowerButton, VolumeDownButton ve VolumeUpButton için de yapıyoruz.


```php
// src/Remote/Button/ChannelUpButton.php
// ... lines 1 - 6
#[AsTaggedItem('channel-up')]
final class ChannelUpButton implements ButtonInterface
// ... lines 9 - 15
```

👉 Bu attribute, servisin container'daki ismini `channel-up` olarak ayarlar.


```php
// src/Remote/Button/PowerButton.php
// ... lines 1 - 6
#[AsTaggedItem('power')]
final class PowerButton implements ButtonInterface
// ... lines 9 - 15
```

👉 Bu attribute, servisin container'daki ismini `power` olarak ayarlar.


```php
// src/Remote/Button/VolumeDownButton.php
// ... lines 1 - 6
#[AsTaggedItem('volume-down')]
final class VolumeDownButton implements ButtonInterface
// ... lines 9 - 15
```

👉 Bu attribute, servisin container'daki ismini `volume-down` olarak ayarlar.


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 6
#[AsTaggedItem('volume-up')]
final class VolumeUpButton implements ButtonInterface
// ... lines 9 - 15
```

👉 Bu attribute, servisin container'daki ismini `volume-up` olarak ayarlar.

Şimdi tekrar tarayıcıya dönüp sayfayı yenileyelim... bir butona basalım ve... çalışıyor! Profiler'da butonun doğru mesajı döktüğünü görebiliriz.

Artık yeni bir buton eklemek istediğimizde, sadece buton sınıfını oluşturup `ButtonInterface`'i uygulatmamız ve `#[AsTaggedItem]` attribute'unu benzersiz bir buton ismiyle eklememiz yeterli.

Eğer bu butonun kumanda arayüzünde görünmesini istiyorsak, hala şablona eklememiz gerekiyor. Ama daha iyisini yapabilir miyiz? Yani, her yeni buton eklediğimizde bu dosyayı düzenlememize gerek kalmasaydı? Bunu yapmak için başka bir bağımlılık enjeksiyonu attribute'u kullanmamız gerekecek. Sıradaki konu bu olacak.
