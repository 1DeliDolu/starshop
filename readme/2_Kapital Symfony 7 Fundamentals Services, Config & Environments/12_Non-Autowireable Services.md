# 🛠️ Non-Autowireable Services / Otomatik Bağlanamayan Servisler

Bir önceki bölümde, otomatik bağlanamayan bir argümanı otomatik bağlamıştık. Bu sefer, otomatik bağlanamayan bir servisi otomatik bağlamayı deneyeceğiz. Ancak, bunu yapmadan önce, otomatik bağlanamayan bir servis bulmamız gerekiyor. Bunu yapmak için terminalinizde şu komutu çalıştırın:

```
php bin/console debug:container
```

👉 Bu komut, mevcut tüm servisleri ve durumlarını listeler.

Eğer bu Twig servisi yalnızca bir ID olduğu için otomatik bağlanamaz diye düşünüyorsanız, tekrar düşünün. Yukarıya kaydırırsak, `Twig\Environment`’i görürüz. Bu, Twig servisimizin bir takma adıdır. Tersine, `twig.command.debug` otomatik bağlanamaz. Bu servis, önceki bölümlerde kullandığımız `debug:twig` komutunu çalıştıran servistir. Terminalimizde şu komutu çalıştırdığımızda,

```
php bin/console debug:twig
```

👉 Bu komut, uygulamamızda mevcut olan tüm Twig filtrelerinin ve fonksiyonlarının bir listesini verir.

Bu, biraz garip olsa da, bu servisi alıp doğrudan kullanabileceğimiz anlamına gelir. Bunu bilmek güzel!

Şimdi buraya geri dönelim, `homepage()` içinde `DebugCommand` (Twig’den olan) türünde bir tip tanımlayalım ve buna `$twigDebugCommand` diyelim.

```php
//src/Controller/MainController.php
// ... lines 1 - 5
use Symfony\Bridge\Twig\Command\DebugCommand;
// ... lines 7 - 15
class MainController extends AbstractController
{
// ... line 18
    public function homepage(
// ... lines 20 - 22
        DebugCommand $twigDebugCommand,
    ): Response {
// ... lines 25 - 38
    }
}
```

👉 Burada, Twig'in `DebugCommand` servisi `homepage()` fonksiyonuna argüman olarak eklenmiştir.

## 🏷️ The Autowire Attribute / Autowire Niteliği

Tarayıcımıza dönüp sayfayı yenilersek... bir hata alırız:

`App\Controller\MainController::homepage()` fonksiyonunun `$twigDebugCommand` argümanı otomatik olarak bağlanamıyor.

Eğer parametrelerde yaptığımız gibi argümanın üzerine bir öznitelik (attribute) eklememiz gerektiğini tahmin ettiyseniz, haklısınız, ancak servisler için söz dizimi biraz farklıdır. Burada, `DebugCommand`’ın üzerine yeni bir öznitelik ekleyin - `#[Autowire()]`. İçerisine, servis adını yazacağız. Terminalimizdeki listeden servis adını aynen kopyalayabiliriz.

```php
src/Controller/MainController.php
// ... lines 1 - 7
use Symfony\Component\DependencyInjection\Attribute\Autowire;
// ... lines 9 - 15
class MainController extends AbstractController
{
// ... line 18
    public function homepage(
// ... lines 20 - 22
        #[Autowire(service: 'twig.command.debug')]
        DebugCommand $twigDebugCommand,
    ): Response {
// ... lines 26 - 39
    }
}
```

👉 Bu örnekte, `DebugCommand` argümanı için `Autowire` özniteliğiyle servis adı belirtildi.

Tamam, tekrar ana sayfaya dönüp yenilersek... otomatik olarak başarıyla bağlandığını göreceğiz. Güzel!

## 🧩 Using the Autowired Service / Otomatik Bağlanan Servisi Kullanmak

Şimdi, bu komutu çalıştırabilir miyiz bakalım. `Response`’un altında, `$twigDebugCommand->run()` yazın. İlk argüman bir girdi olmalı, yani `new ArrayInput` diyebiliriz. İkinci argüman ise aşağıda kullanacağımız çıktı olacak, ancak bunu yapmadan önce bir çıktı değişkeni oluşturmamız gerekiyor. Yukarıya `$output = new BufferedOutput()` yazın. Şimdi, `$output`'u burada ikinci argüman olarak ekleyebiliriz. Tamam, editörümüz memnun, şimdi aşağıda `dd($output)` ekleyelim. Tarayıcıya dönüp yenilersek... tüh... hata. Görünüşe göre `ArrayInput()` sınıfına boş bir dizi iletmemiz gerekiyor. Bunu yapıp tekrar yenilersek... işte bu! Fonksiyonların ve filtrelerin bir listesini aldık. Çalıştı. Bu sadece bir örnekti, bu yüzden bu kodu kaldırabiliriz, ancak burada asıl önemli olan şey, bir şey varsayılan olarak otomatik bağlanamasa bile, `#[Autowire]` özniteliğiyle bunu otomatik bağlanabilir hâle getirebilirsiniz; ister bir servis ister bir parametre olsun.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./11_Non-Autowireable Arguments.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./13_Environment Variables.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>

Sonraki: Şimdi, ortam değişkenleri ve daha önce gördüğümüz `.env` dosyasının amacından bahsedeceğiz. Ayrıca, bunları uygulamamızda nasıl kullanabileceğimizi ve farklı ortamlarda nasıl farklı davranışlar elde edebileceğimizi göreceğiz.
