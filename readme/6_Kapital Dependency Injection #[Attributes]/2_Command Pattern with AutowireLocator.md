# 🕹️ Command Pattern with AutowireLocator / AutowireLocator ile Komut Tasarımı Deseni

Haydi başlayalım! Uygulamamıza baktığımızda, bu bizim kumanda arayüzümüz. Temelde sadece bir formdan oluşuyor ve her buton formu gönderiyor. Her butonun `name` özniteliği benzersiz ve bu sayede denetleyicimiz hangi butonun mantığının çalıştırılacağını belirliyor. Örneğin "Power" butonuna tıkladığımızda, bize ne olduğunu söyleyen bir `flash message` (denetleyici tarafından eklenen) görüyoruz. "Channel Up", "Channel Down" vb. tuşlara bastığımızda da aynı şekilde karşılık gelen mesajlar gösteriliyor.

Yani bu yapı çok basit. Form aynı sayfaya post ediliyor, buton mantığı işleniyor ve ardından bizi flash mesaj ile tekrar yönlendiriyor.

## 📝 Reviewing the Controller Logic / Denetleyici Mantığını Gözden Geçirmek

Kodumuza geçtiğimizde, `src/Controller` altında `RemoteController`'ı açıyoruz ve... işte buradayız! İsteğin `POST` olup olmadığını kontrol ediyoruz ve her buton bir isim ile gönderildiğinden, bu `switch()` ifadesi ismi istekte yakalıyor. Her buton bir case içinde ve her `dump()` o butonun kendi mantığını temsil ediyor. Eğer bir buton bulunamazsa, bu bir 404 fırlatıyor. Sonra `flash message` ekliyor, buton adını daha güzel göstermek için biraz string işlemesi yapıyor ve aynı rotaya tekrar yönlendiriyor.

En sonda ise, istek `POST` değilse sadece `index.html.twig` dosyasını render ediyoruz. Bu bizim kumanda şablonumuz. Böyle büyük bir switch-case ifadesine sahip olduğunuzda, genellikle yeniden düzenleme için iyi bir fırsattır, özellikle de daha fazla buton ve mantık ekledikçe. Bunu yapmanın harika bir yolu `Command pattern`'dır. Bu deseni daha detaylı incelemek isterseniz, "Design Patterns" kursumuza göz atabilirsiniz!

## 🏗️ Creating Commands / Komutları Oluşturmak

Tamam, ilk olarak butonları ve onların tüm mantığını içerecek komutlar oluşturacağız. `src/` içinde kodumuzu daha iyi organize etmek için yeni bir dizin oluşturalım. Adını `Remote` koyacağız ve içine de bir `Button` klasörü ekleyeceğiz. Harika! Şimdi her bir buton için yeni bir PHP sınıfı oluşturmamız gerekiyor. Başlamak için, her butonun uygulayacağı bir arayüz yazacağız ki komut işleyicimiz bunları öngörülebilir şekilde işleyebilsin. Buna `ButtonInterface` adını vereceğiz. İçine, argümansız ve geriye hiçbir şey döndürmeyen `public function press()` metodunu yazacağız.


```php
// src/Remote/Button/ButtonInterface.php
// ... lines 1 - 2
namespace App\Remote\Button;
interface ButtonInterface
{
    public function press(): void;
}
```

👉 Bu arayüz, tüm butonların uygulaması gereken temel metodu tanımlar.

`tutorial/` dizinine baktığımızda... işte burada! Tüm buton implementasyonları hazır ve bekliyor. Tek yapmamız gereken tüm PHP dosyalarını kopyalayıp `Button` klasörüne eklemek. Çok kolay! `ChannelDownButton.php` dosyasına bakarsak, burada `press()` metodunun uygulandığını ve denetleyicide gördüğümüz aynı `dump()` ve buton mesajının yer aldığını görebiliriz.

## 🔧 Building the Command Handler / Komut İşleyicisini Oluşturmak

Artık komutlarımız var! Şimdi, buton ismini alıp ilgili komutu çalıştıracak bir komut işleyicisine ihtiyacımız var. Bunun için, komut işleyicisi olarak davranacak bir nesne oluşturacağız. `Remote` dizininde yeni bir sınıf oluşturun, adına `ButtonRemote` diyelim. Sınıfları varsayılan olarak `final` olarak işaretlemeyi tercih ediyorum, ancak bu zorunlu değil, isterseniz kaldırabilirsiniz.

Yeni sınıfımızda `press()` adında public bir metot oluşturun. Bu, string bir argüman `$name` (buton ismini temsil ediyor) alacak. Bu metodun dönüş tipi yok, yani `void`. Artık bu nesne için bir kurucu (`constructor`) oluşturabiliriz, ve içine `private ContainerInterface $container` ekliyoruz. Doğru olanı seçmek için PSR\Container'dan aldığınızdan emin olun. Bu container, buton nesnelerimizi anahtar-değer olarak saklayacak; anahtar buton ismi, değer ise buton nesnesi olacak. `press()` metodunda, `$this->container->get($name)` ile ilgili `ButtonInterface` örneğini alıp `press()` metodunu çağıracağız.

### #\[AutowireLocator]

Şu anda, `press()` metodunu çağırmak hata verecektir çünkü Symfony bu container'ı nasıl oluşturacağını bilmiyor. Bunu kolaylaştırmak için, `#[AutowireLocator()]` bağımlılık enjeksiyonu `attribute`'unu kullanacağız. Eski Symfony sürümlerinde bunun adı `TaggedLocator` idi, fakat Symfony 7.1'de diğer attribute'larla uyumlu olması için adı değiştirildi. Attribute'un ilk argümanı, anahtar olarak buton isimleri ve değer olarak ilgili sınıf adları olan bir dizi olacak. Symfony, bunları container oluşturulurken gerçek buton örneklerine dönüştürecek.

Şimdi, tüm butonlarımızı bu container'a ekleyelim. Diğer butonlar da aynı şekilde olacak: `'channel-down' => ChannelDownButton::class`, `'volume-up' => VolumeUpButton::class`, ve `'volume-down' => VolumeDownButton::class`. İşte bu kadar! Komut işleyicimiz hazır!


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 12
final class ButtonRemote
{
    public function __construct(
        #[AutowireLocator([
            'power' => PowerButton::class,
            'channel-up' => ChannelUpButton::class,
            'channel-down' => ChannelDownButton::class,
            'volume-up' => VolumeUpButton::class,
            'volume-down' => VolumeDownButton::class,
        ])]
        private ContainerInterface $buttons,
    ) {
    }
    public function press(string $name): void
    {
        $this->buttons->get($name)->press();
    }
}
```

👉 Bu sınıf, buton ismine göre ilgili komut nesnesini çağırıp çalıştırır.

## 🛠️ Refactoring the Controller / Denetleyiciyi Yeniden Düzenlemek

Şimdi, denetleyicimizdeki büyük switch-case ifadesini bununla değiştirmemiz gerekiyor.

`RemoteController.php` dosyasına dönüp, `Request`'ten sonra `ButtonRemote`'u da enjekte edelim. Denetleyici otomatik olarak `autowired` olduğu için Symfony bunu otomatik olarak enjekte edecek. Aşağıda, buton ismini alan satırı kopyalayıp yukarıya ekleyin. Altına, `$remote->press($button)` satırını yazın. Artık bu büyük switch ifadesini kaldırabiliriz, fakat buton bulunamazsa durumu da ele almalıyız. Buradaki satırı kopyalayıp, switch ifadesini tamamen silin ve bu `press()` metodunu bir `try-catch` bloğu içinde sarın. `$remote->press($button)` kodunu try içine taşıyın ve aşağıya bu kodu ekleyin. Böylece, `ContainerInterface::get()` buton adı için bir komut bulamazsa bu istisna fırlatılacak. Son olarak, önceki istisnayı 404'e ekleyerek hata ayıklamayı kolaylaştırabiliriz.


```php
// src/Controller/RemoteController.php
// ... lines 1 - 15
    public function index(Request $request, ButtonRemote $remote): Response
    {
        if ('POST' === $request->getMethod()) {
            try {
                $remote->press($button = $request->request->getString('button'));
            } catch (NotFoundExceptionInterface $e) {
                throw $this->createNotFoundException(sprintf('Button "%s" not found.', $button), previous: $e);
            }
            $this->addFlash('success', sprintf('%s pressed', u($button)->replace('-', ' ')->title(allWords: true)));
            return $this->redirectToRoute('home');
        }
        return $this->render('index.html.twig');
    }
}
```

👉 Bu kod, hangi butona basıldığını güvenli şekilde işler ve ilgili komut nesnesini çağırır.

Denetleyicimiz artık çok daha küçük, şimdi uygulamamızda test edelim. "Power" butonuna basarsak... "Power pressed"! "Channel Up" butonuna basarsak... "Channel up pressed"! Her şey düzgün çalışıyor gibi görünüyor. Profiler'a bakarsak, `dump()` mesajının hala orada olduğunu ve artık doğru buton implementasyonundan geldiğini görebiliriz. Harika!

## 🔄 Okay, this looks great, but there’s another improvement we can make. / Tamam, bu harika görünüyor ama başka bir iyileştirme daha yapabiliriz.

Şu anda, her yeni buton eklediğimizde, `ButtonRemote` içindeki `AutowireLocator` attribute'unu güncellememiz gerekiyor. Bu kabul edilebilir ama biraz zahmetli.

## ⏭️ Next: Let's explore a refactor to remove this requirement. / Sıradaki Adım: Bu gerekliliği kaldıracak bir yeniden düzenlemeyi inceleyelim.
