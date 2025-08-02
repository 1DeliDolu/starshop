# 🚀 Upgrading to Symfony 6.4 / Symfony 6.4'e Yükseltme

Herkese merhaba! `Symfony 7` çıktı! Harika! Tabii ki heyecanlıyım - `Symfony`, `Twig` ve ilgili her şeyi seviyorum. Ama `Symfony 7`'nin çıkması gerçekte ne anlama geliyor?

## 📆 Symfony'nin Keyifli ve Tahmin Edilebilir Sürüm Takvimi / Symfony'nin Keyifli ve Tahmin Edilebilir Sürüm Takvimi

Açıkçası... pek bir şey değişmiyor! `Symfony`'nin sürüm takvimi sayesinde, yeni bir ana sürümün çıkması büyük bir olay değil... gerçi pazarlama için öyleymiş gibi davranıyoruz.

Her 6 ayda bir - Mayıs ve Kasım aylarında - yeni bir küçük sürüm yayımlanır, örneğin `6.1` veya `6.2`. Bu sürümler yeni özellikler içerir. Yani `Symfony 6.3` veya `Symfony 6.4`'teki harika yeni özellikler için heyecanlanmak gayet mantıklı. Sonra, her ".4" sürümü, örneğin `6.4`, bir sonraki ana sürümün `.0` versiyonuyla aynı gün yayımlanır: `7.0`. Evet, `6.4` ve `7.0` tam olarak aynı gün yayımlandı ve aslında özünde aynılar! İkizler gibi!

Tek fark, `7.0`'da tüm `depreceated` kod yollarının kaldırılmış olmasıdır. İşte bu, `Symfony`'yi özel kılan şeyin özü. Sürüm takvimi ve `deprecation` politikası sayesinde, kullanıcılar olarak uygulamalarımızı ana sürümler arasında daima yükseltebiliriz... hem de büyük sorunlar veya uygulamamızı bozacak şeyler yaşamadan. Bu eğiticide de tam olarak bunu yapacağız... ardından en sevdiğim bazı yeni özellikleri inceleyeceğiz.

## 🛠️ Project Set Up / Proje Kurulumu

Her zamanki gibi, bu eğitimden en iyi şekilde yararlanmak için, benimle birlikte kod yazarak ilerleyin: bu sayfadaki kurs kodunu indirin. Dosyayı açtığınızda, burada gördüğünüz kodla aynı olan bir `start/` dizini bulacaksınız. `README.md` dosyası, uygulamanın nasıl çalıştırılacağıyla ilgili ilham verici bir hikaye anlatıyor. Gerekli adımların çoğunu zaten tamamladım, bunlara `yarn install` ve bu sekmede `yarn watch` çalıştırmak da dahil.

Son adım ise, `symfony` binary'sini kullanarak şunu çalıştırmak:



```bash
symfony serve -d
```

👉 Bu komut, geliştirme web sunucusunu başlatır.

Bağlantıya tıklayacağım. Mixed Vinyl'e merhaba deyin: Bu, birçok `Symfony 6` eğitimimizden gelen uygulama ve şu anda `6.1.2` sürümünde.

## 🆕 Using a Newer PHP Version / Daha Yeni Bir PHP Sürümü Kullanmak

`composer.json` dosyasını açın. En üstte, uygulamamızın `php 8.1` veya üzeri gerektirdiğini görebilirsiniz. Kendi uygulamalarımda, aşağıda `config.platform.php` bölümünde de prodüksiyonda kullandığım PHP sürümünü özellikle belirlemeyi seviyorum:



```json
// composer.json
{
    // ... lines 2 - 4
    "require": {
        "php": ">=8.1",
    // ... lines 7 - 32
    },
    "config": {
    // ... lines 35 - 44
        "platform": {
            "php": "8.1.0"
        }
    },
    // ... lines 49 - 96
}
```

👉 Bu ayar, Composer'ın yalnızca bu sürümle uyumlu bağımlılıkları seçmesini garanti eder.

Yerel olarak, `php -v` çalıştırırsam, zaten `PHP 8.3` kurulu. Ayrıca 8.1 için ayrı bir php binary'si de var. Ve `composer.json`'daki `8.1` sayesinde, `symfony` web sunucusunu başlattığımda eski sürümü kullandı.

Bunu sadece `PHP 8.3` olarak değiştirin. Sonra şunu çalıştırın:

src/Controller/MainController.php

```bash
composer up
```

👉 Bu komut, bağımlılıkları günceller.

`composer.json`'da tüm bağımlılıklarım - ister `Symfony` ister başka bir şey olsun - yalnızca son rakamın veya sondan bir önceki rakamın değişmesine izin verecek şekilde yazılmıştır. Paket bakıcıları işini doğru yapıyorsa, bu güncellemelerde geriye dönük uyumsuzluk olmaz. `6.1`'den `6.4`'e veya `2.0`'dan `2.4`'e yükseltme yaptığımızda uygulamamız çalışmaya devam etmelidir!

Yani, bu güncellemeleri almak için `composer up` çalıştırmak teoride tamamen güvenlidir.

## 🔄 Encore & Minor Changes / Encore ve Küçük Değişiklikler

`yarn` sekmemde, güncelleme bir hata oluşturdu: bir controller mevcut değil gibi bir şey. Bu, `Symfony UX & Encore`'a özeldir. PHP bağımlılıklarını güncellediğinizde, node bağımlılıklarını da yeniden yüklemeniz gerekebilir. `Ctrl+C` ile işlemi durdurun ve ardından şunu çalıştırın:



```bash
yarn install --force
```

👉 Bu komut, node bağımlılıklarını zorla yeniden yükler.

Ya da `npm` kullanıyorsanız `npm install --force` çalıştırın. Sonra tekrar



```bash
yarn watch
```

👉 Bu komut, derleyiciyi izlemeye başlatır.

Ana sekmede şu komutu çalıştırın:



```bash
git status
```

👉 Bu komut, değişiklikleri gösterir.

Her zamanki dosyaların yanında, `controllers.json` içinde yeni bir controller var... bu, `ux-turbo` güncellemesinden geldi. Kullanmayacağız, ama orada kalmasında sorun yok. `package.json`'a stimulus bundle için yeni bir giriş eklendi. Bu, yükseltme sırasında yüklenen nispeten yeni bir paket ve yakında daha fazla konuşacağız.

## ⬆️ Upgrading to 6.4 / 6.4'e Yükseltme

Şimdi `PHP 8.3` kullanıyoruz ve bağımlılıklarımızı biraz güncelledik. Ama hâlâ `Symfony 6.1` kullanıyoruz. `7`'ye yükseltmek için önce `6.4`'e yükseltmemiz gerekiyor. Bu, `7.0`'a hazırlık yapmak ve tüm `deprecation`ları bulup düzeltmek için bir fırsat sağlayacak.

Ve... yükseltmek çok kolay! `6.1.*` olan her yeri bulun, `6.4.*` ile değiştirin ve hepsini değiştirin.



```json

//composer.json 
{
    // ... lines 2 - 4
    "require": {
    // ... lines 6 - 17
        "symfony/asset": "6.4.*",
        "symfony/console": "6.4.*",
        "symfony/dotenv": "6.4.*",
    // ... line 21
        "symfony/framework-bundle": "6.4.*",
        "symfony/http-client": "6.4.*",
    // ... line 24
        "symfony/proxy-manager-bridge": "6.4.*",
        "symfony/runtime": "6.4.*",
        "symfony/twig-bundle": "6.4.*",
    // ... lines 28 - 29
        "symfony/yaml": "6.4.*",
    // ... lines 31 - 32
    },
    // ... lines 34 - 81
    "extra": {
        "symfony": {
    // ... line 84
            "require": "6.4.*",
    // ... line 86
        }
    },
    "require-dev": {
    // ... line 90
        "symfony/debug-bundle": "6.4.*",
    // ... line 92
        "symfony/stopwatch": "6.4.*",
        "symfony/web-profiler-bundle": "6.4.*",
    // ... line 95
    }
}
```

👉 Bu dosya, `symfony` paketlerinin 6.4 sürümüne çekilmesini sağlar.

Ama dikkatli olun. Çoğu zaman, `Symfony` sürüm kısıtlamaları bu şekilde olur. Ancak, bazen şöyle de olabilir: `^6.1`. Bunları da atlamayın: amaç, ana depodan gelen her `symfony` paketini yükseltmek. Bu biraz kafa karıştırıcı olabilir çünkü, yükseltmek istediğimiz paketlerin yanında, kendi zaman çizelgesi ve versiyonlaması olan başka `symfony` altındaki bağımsız paketler de vardır. Şimdilik onları görmezden gelin - ama sonunda tüm paketlerin güncellendiğinden emin olacağız.

Ayrıca, en altta, `extra.symfony.require` altında bunun da `6.4.*` olarak güncellendiğinden emin olun. Bu, Composer'ın yalnızca `6.4` `Symfony` sürümleriyle ilgilenmesini sağlayan bir Composer optimizasyonudur.

Terminalde, şimdi bunu yapalım!



```bash
composer up
```

👉 Bu komut, paketleri yükseltir.

Şu güzelliklere bakın: `6.1`'den `6.4`'e güncellemeler! Ve... siteyi denediğimizde, uygulama hala çalışıyor!

Ama şuna dikkat edin: `PHP 8.1.27`. `symfony` web sunucusunu başlattığımızda, `composer.json`'daki `PHP 8.1` sürümünü okudu, o sürümü buldu ve kullandı. Bunu `8.3` olarak değiştirdik, ama bunu kullanması için sunucuyu yeniden başlatmamız gerekiyor. Şunu çalıştırın:



```bash
symfony server:stop
```

👉 Bu komut, sunucuyu durdurur.

Sonra:


```bash
symfony serve -d
```

👉 Bu komut, sunucuyu yeniden başlatır.

Evet: sistemimdeki `PHP 8.3.1` sürümünü buldu. Ve sitede... tamam!

Şimdi uygulama `Symfony 6.4` üzerinde çalışıyor. Şimdi işimiz, tüm `deprecation`ları bulmak ve düzeltmek. Web debug toolbar'da, bu sayfada 22 tane `deprecated` kod yolu kullandığımızı gösteriyor! Bunları düzeltmeye başlamak için... hile yapacağız... bir kestirme kullanıp Flex tariflerimizi yükselteceğiz.
