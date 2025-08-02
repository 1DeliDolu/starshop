# 🛠️ Maker Bundle: Let's Generate Some Code!

## Maker Bundle: Let's Generate Some Code! / Maker Bundle: Haydi Kod Üretelim!

Symfony eğitim serisinin ilk bölümünü neredeyse tamamladınız, tebrikler! Web üzerinde dilediğinizi inşa etme yolunda büyük bir adım attınız. Bunu kutlamak için, Symfony'nin harika kod üretim aracı olan MakerBundle ile oynamak istiyorum.

## Composer require vs require-dev

Hadi yükleyelim:

```bash
composer require symfony/maker-bundle --dev
```

Daha önce bu `--dev` bayrağını görmemiştik ama çok da önemli değil. `composer.json` dosyasını açarsanız, bu bayrak sayesinde `symfony/maker-bundle` paketi `require` anahtarının altına değil, `require-dev` altına eklendiğini göreceksiniz.

```json
{
    // ... satır 2 - 84
    "require-dev": {
        // ... satır 86
        "symfony/maker-bundle": "^1.52"
        // ... satır 88 - 89
    }
}
```

Varsayılan olarak `composer install` komutunu çalıştırdığınızda, hem `require` hem de `require-dev` altındaki tüm paketleri indirir. Ancak `require-dev`, yalnızca yerel geliştirme sırasında ihtiyaç duyulan ve üretimde kullanılmayan paketler içindir. Dağıtım sırasında Composer’a şu şekilde diyebilirsiniz:

> Hey! Sadece `require` altındaki paketleri yükle, `require-dev` olanları yükleme.

Bu, üretim ortamında küçük bir performans artışı sağlayabilir ama genelde çok önemli değildir.

---

## 🧰 Maker Komutları

### The Maker Commands / Maker Komutları

Şimdi yeni bir bundle yükledik. Bundle'ların bize ne sağladığını hatırlıyor musunuz? Evet: servisler! Bu sefer MakerBundle bize yeni konsol komutları sağlayan servisler sundu. Şimdi büyük davul! Şunu çalıştırın:

```bash
php bin/console
```

Ya da ben bundan sonra `symfony console` kullanacağım, aynı şey. Bu yeni bundle sayesinde `make` ile başlayan tonlarca komutumuz var! Güvenlik sistemi oluşturmak, controller yazmak, veritabanı ile konuşan doctrine entity’leri oluşturmak, formlar, listener’lar, kayıt formları... birçok şey var!

---

## 🔧 Konsol Komutu Oluşturma

### Generating a Console Command / Konsol Komutu Oluşturma

Haydi bu komutlardan birini kullanarak kendi özel konsol komutumuzu oluşturalım:

```bash
symfony console make:command
```

Bu komut, bizden etkileşimli olarak komut adı isteyecek. Şöyle diyelim: `app:ship-report`. Tamam!

Bu işlem tam olarak bir dosya oluşturur: `src/Command/ShipReportCommand.php`. Hadi içine bakalım!

```php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:ship-report',
    description: 'Add a short description for your command',
)]
class ShipReportCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
```

Süper! Bu, normal bir sınıf - bu arada bir servistir - ama üzerinde bir öznitelik var: `#[AsCommand]`. Symfony’ye şu mesajı verir:

> Hey! Bu servis sadece bir servis değil: onu konsol komutları listesine dahil etmeni istiyorum.

Bu öznitelik komutun adını ve açıklamasını içerir. Sınıfın içinde `configure()` metodunda argümanlar ve seçenekler tanımlanabilir. Ama asıl olay şu: biri bu komutu çağırdığında Symfony `execute()` metodunu çalıştırır.

`$io` nesnesi oldukça kullanışlı. Çıktılar üretmek için kullanılır - örneğin `$this->note()` veya `$this->success()` gibi stillerle. Ve burada görmesek de kullanıcıya etkileşimli sorular sormak da mümkündür.

Ve en güzel kısmı? Bu sınıfı oluşturduğumuz anda kullanıma hazır! Hemen deneyin:

```bash
symfony console app:ship-report
```

Çok havalı! Alttaki mesaj, komutun en altındaki success() mesajından geliyor. `configure()` sayesinde bir adet `arg1` adlı argümanımız var. Argümanlar, komuttan sonra verilen stringlerdir:

```bash
symfony console app:ship-report ryan
```

Şunu söyler:

```
You passed an argument: ryan
```

... bu da komut sınıfının ilgili yerinden geliyor.

---

## 📊 İlerleme Çubuğu Oluşturma

### Building a Progress Bar / İlerleme Çubuğu Oluşturma

Komutlar ile yapabileceğiniz birçok eğlenceli şey var... ve bunlardan biriyle oynamak istiyorum. `$io` nesnesinin süper güçlerinden biri de animasyonlu ilerleme çubukları oluşturmaktır.

Diyelim ki bir gemi raporu oluşturuyoruz... ve bu bazı ağır sorgular gerektiriyor. Bu yüzden ekranda bir ilerleme çubuğu göstermek istiyoruz. Bunun için `$io->progressStart()` diyerek, üzerinden geçeceğimiz veri satırı sayısını veririz. Diyelim ki 100 satır üzerinden geçiyoruz.

Gerçek veri yerine, sahte bir `for` döngüsü oluşturalım. Ortasına `$i` değişkenini de ekliyoruz! İçeride, çubuğu ilerletmek için `$io->progressAdvance()` diyelim. Burada ağır iş yükümüz yer alacaktı. Bunu sahte bir `usleep(10000)` ile simüle edelim.

Döngü sonunda `$io->progressFinish()` ile bitiriyoruz.

```php
$io->progressStart(100);
for ($i = 0; $i < 100; ++$i) {
    $io->progressAdvance();
    usleep(10000);
}
$io->progressFinish();
```

Hepsi bu kadar! Hemen çalıştırın:

```bash
symfony console app:ship-report ryan
```

Ah, bu gerçekten harika!

---

## 🎉 Tebrikler!

Ve... işte bu kadar! Kendinize bir çak bi’şey verin... ya da daha iyisi, iş arkadaşınızı şaşırtarak zıplayan bir çak yapın! Ardından hak ettiğiniz bir bira, çay, kısa yürüyüş veya köpeğinizle frizbi maçı ile kutlayın. Çünkü... başardınız! Symfony ile tehlikeli olmaya ilk büyük adımı attınız. Şimdi geri gelin ve bu şeylerle oynayın: bir blog oluşturun, birkaç statik sayfa yaratın, her şey olabilir. Bu size çok şey katacaktır.

Ve eğer bir sorunuz olursa, her videonun altındaki yorumları dikkatle takip ediyoruz ve her soruya cevap veriyoruz. Ayrıca devam edin! Bir sonraki eğitimde Symfony'nin yapılandırma ve servislerine daha derinlemesine dalacağız: Symfony'de yapacağınız her şeyi yöneten sistemlere!

Pekâlâ dostlar, bir sonraki derste görüşmek üzere!

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./19_Your Single Page App.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
</div>
