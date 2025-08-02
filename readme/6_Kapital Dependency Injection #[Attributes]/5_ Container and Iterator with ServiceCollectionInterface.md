# 🪣 Container and Iterator with ServiceCollectionInterface / ServiceCollectionInterface ile Container ve Iterator

Son bölümde, butonların programatik olarak listelenmesini sağladık. Fakat bunu yapınca, butonlara tıklama fonksiyonu bozuldu! Çocuklar huzursuzlanıyor: bunu hemen düzeltmemiz lazım.

## 🔧 ButtonRemote İçin Çözüm Yolları

`ButtonRemote` içinde bunun için birkaç yol var. En kolay yol, iki argüman enjekte etmek: biri buton servislerinin iterator’ü, diğeri ise bir locator (her bir servisi almak için get() metodu olan mini-container). Bu işe yarar ve gayet geçerli bir çözüm. Ama daha iyisini yapabiliriz!

## 🪄 ServiceCollectionInterface

Hem iterator hem locator olan bir nesne enjekte edebiliriz: `ServiceCollectionInterface`. Buna bakalım. Bu arayüz, bir `ServiceProviderInterface`'dir (locator) ve bir `IteratorAggregate`'tir (iterator). Ayrıca `Countable`'dır.

ButtonRemote’da, `AutowireIterator`'ı tekrar `AutowireLocator` olarak değiştirin ki Symfony bize `ServiceCollectionInterface` enjekte edebilsin:


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 8
final class ButtonRemote
{
    public function __construct(
        #[AutowireLocator(ButtonInterface::class, indexAttribute: 'key')]
        private ServiceCollectionInterface $buttons,
    ) {
    }
// ... lines 16 - 30
}
```

👉 Bu satır, butonları hem iterator hem de locator olarak kullanmamızı sağlar.

Kullanılmayan importları da temizleyebilirsiniz.

Uygulamaya dönüp sayfayı yenileyin... Butonlar hala listeleniyor, bu iyi bir işaret. Şimdi bir butona tıklayın... tekrar çalışıyor gibi görünüyor! Profiler’da POST isteğini kontrol edin, doğru buton mantığının çağrıldığını görebilirsiniz. Harika!

## 💤 Laziness

Service locator’ın güzel yanlarından biri "tembel" (lazy) olmasıdır. Servisler ancak ve ancak get() çağrılırsa oluşturulur. Üstelik, aynı servisi kaç defa çağırırsanız çağırın, sadece tek bir örnek yaratılır.

Fakat bir problemimiz var. `buttons()` metodunda tüm butonlar üzerinde döngü kuruyoruz. Bu, sadece isimlerini almak için tüm buton servislerinin oluşturulmasına neden oluyor. Sadece isimlere ihtiyacımız varken bu gereksiz bir israf!

## 🪣 ServiceCollectionInterface::getProvidedServices()

Neyse ki, `ServiceCollectionInterface` yardımımıza koşuyor! Symfony service locator’larının özel bir metodu var: `getProvidedServices()`. Tüm döngü kodunu kaldırıp `dd($this->buttons->getProvidedServices())` yazalım ve ne döndürdüğüne bakalım:


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 24
    public function buttons(): iterable
    {
        dd($this->buttons->getProvidedServices());
    }
// ... lines 29 - 30
```

👉 Bu satır, locator’ın sunduğu servislerin isimlerini gösterir.

Uygulamayı yenileyin. Bu çıktı, daha önce elle yazdığımız `#[AutowireLocator]` eşlemesine neredeyse aynıdır.

İstediğimiz ise bu dizinin anahtarları. Şimdi şu şekilde döndürün:


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 8
final class ButtonRemote
{
// ... lines 11 - 24
    public function buttons(): iterable
    {
        return array_keys($this->buttons->getProvidedServices());
    }
}
```

👉 Bu kod, sadece buton isimlerini döndürür ve gereksiz nesne oluşturmayı önler.

Uygulamaya geri dönüp yenileyin. Her şey hala çalışıyor ve arka planda tüm buton servisleri oluşturulmuyor.

Performans zaferi!

## 🔇 Mute Butonu Eklemek

Bunu kutlamak için, kumandamıza yeni bir buton ekleyelim!

Yeni bir PHP sınıfı `MuteButton` oluşturun ve `ButtonInterface`'i uygulasın. `press()` metodunu yazın, içine `dump('Mute button pressed')` ekleyin. Şimdi, `#[AsTaggedItem]` ekleyin, \$index olarak `mute` yazın. Priority’i varsayılan bırakın (0). Bu, butonu diğerlerinin altına ekler:


```php
// src/Remote/Button/MuteButton.php
// ... lines 1 - 6
#[AsTaggedItem('mute')]
final class MuteButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Mute button pressed');
    }
}
```

👉 Bu sınıf, sessize alma (mute) butonu için bir komut tanımlar.

Bir de SVG ikonu gerek: `assets/icons` içine mute.svg dosyasını kopyalayın.

Her şey hazır! Uygulamayı yenileyin... ve işte burada! Butona tıklayın ve profiler’ı kontrol edin. Çalışıyor! Artık çocuklar Barney izlerken TV’yi sessize alabiliriz. Mükemmel!

Bu refaktör burada tamamlandı! Artık buton eklemek hem kolay hem de performanslı.

## ⏭️ Sıradaki Adım: Kumandamıza logging (kayıt) ekleyelim ve sıradaki attribute’umuz olan `#[AsAlias]` ile tanışalım.
