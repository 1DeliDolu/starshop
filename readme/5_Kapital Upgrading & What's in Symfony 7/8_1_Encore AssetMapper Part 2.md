# 🎨 Encore -> AssetMapper Bölüm 2 / Encore -> AssetMapper Part 2

Üçüncü parti CSS dosyalarını `AssetMapper` ile çalıştırmak en zorlayıcı konulardan biridir. Bu dosyaları şu şekilde içe aktarmak işe yaramaz.

## 📦 Bootstrap CSS Kurulumu / Bootstrap CSS Kurulumu

Öncelikle `Bootstrap`'a odaklanalım. Bu, üçüncü parti bir paket ve üçüncü parti paketleri yüklemek için `bin/console importmap:require paket_adı` komutunu kullanırız:



```bash
php bin/console importmap:require bootstrap
```

👉 Bu komut, `bootstrap` paketini projeye ekler.

`Bootstrap` özellikle ilginçtir çünkü JavaScript paketini, onun bir bağımlılığını ve ayrıca bu paketin genellikle bir CSS dosyasına sahip olduğunu fark eder... bu yüzden onu da alır. Üç öğe de `importmap.php` dosyasına eklendi.

## 🗂️ importmap.php Dosyası / importmap.php Dosyası



```php

//importmap.php
// ... lines 1 - 15
return [
// ... lines 17 - 29
    'bootstrap' => [
        'version' => '5.3.2',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.2',
        'type' => 'css',
    ],
];
```

👉 Bu dosya, hangi paketlerin ve sürümlerin kullanılacağını tanımlar.

Projede `bootstrap` JavaScript dosyasını kullanmıyoruz. İstersek silebiliriz, ancak bir zararı olmadığı için bırakıyoruz. Asıl önemli olan bu CSS dosyasıdır. Yolunu kopyalayın ve `app.css`'deki en üst satırı kaldırın.

`AssetMapper` ile üçüncü parti CSS dosyalarını içe aktarabilirsiniz, fakat bunu başka bir CSS dosyasının içinden yapamazsınız. Teknik olarak yapılabilir, ancak işi kolaylaştırmak için bunu `app.js` üzerinden yapıyoruz. `import` deyip ardından yapıştırın.

## 📝 assets/app.js Dosyası / assets/app.js Dosyası



```javascript
//assets/app.js
// ... lines 1 - 8
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';
// ... lines 11 - 16
```

👉 Bu satırlar, Bootstrap CSS dosyasını ve uygulama CSS dosyasını projeye dahil eder.

Şimdi... Bootstrap çalışmaya başlar!

## 🖼️ FontAwesome Ekleme / FontAwesome Ekleme

Sırada `FontAwesome` var. Paketten belirli bir CSS dosyasını alıyoruz. `Encore` ve `AssetMapper` arasındaki büyük farklardan biri; eğer bir paketten belirli bir dosya içe aktarmanız gerekiyorsa, yalnızca paketi değil, o dosyayı `importmap:require` ile eklemeniz gerekir. Şu komutu yazın:

src/Controller/MainController.php

```bash
php bin/console importmap:require @fortawesome/fontawesome-free/css/all.css
```

👉 Bu komut, sadece ilgili CSS dosyasını indirip `importmap.php`ye ekler.

Bu dosyalar projeye `assets/vendor/` dizinine indirilir. `app.css`'deki ilgili satırı kaldırın ve o yolu yeni bir import olarak ekleyin.

## 📝 assets/app.js Güncellemesi / assets/app.js Güncellemesi



```javascript
// assets/app.js
// ... lines 1 - 8
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';
import './styles/app.css';
// ... lines 12 - 17
```

👉 Bu satır FontAwesome ikonlarını projeye dahil eder.

FontAwesome için artık bu yöntemi önermiyoruz, FontAwesome kitleri ya da tercihen inline SVG kullanılması tavsiye ediliyor. Yakında Symfony UX tarafından kolaylık sağlayan bir ikon paketi sunulabilir.

## 🅰️ CSS Fontları Ekleme / CSS Fontları Ekleme

`app.css`'deki son öğe bir fonttur. Bu biraz daha zordur. `importmap:require` ardından yalnızca paket ismiyle çalıştırırsanız, her zaman paketin ana JavaScript dosyası indirilir. Sadece bir CSS dosyasını almak için, tıpkı yukarıda yaptığımız gibi, tam yol ile `importmap:require` çalıştırmak gerekir.

Daha önce `import:require bootstrap` komutunu çalıştırıp CSS dosyası da aldık. Ancak şunu belirtmek gerekir: Eğer sadece paket adıyla çalıştırırsanız, ilgili paketin JavaScript dosyası alınır. Bazı durumlarda (ör. Bootstrap), paket CSS dosyasına sahip olduğunu belirtir ve `AssetMapper` bunu fark ederek otomatik olarak CSS dosyasını da ekler.

Her neyse, burada bir CSS dosyasına ihtiyacımız var. `Encore` ile, bir CSS dosyasının içinden paket içe aktardığınızda, `Encore` CSS dosyasını bulup içe aktarırdı. `AssetMapper` ile ise, CSS dosyasının yolunu belirleyip onu require etmemiz gerekir.

Ben bunu jsDelivr.com üzerinden bulmayı tercih ediyorum. `AssetMapper` arka planda bu CDN'i kullanıyor. Paketi arayın, bulduğunuzda ana CSS dosyasını kopyalayın ve aşağıdaki gibi çalıştırın:



```bash
php bin/console importmap:require @fontsource-variable/roboto-condensed/index.min.css
```

👉 Bu komut, Roboto Condensed değişken fontunun CSS dosyasını projeye ekler.

Ardından, `importmap.php` dosyasına bir kayıt eklenir.

## 🗂️ importmap.php'ye Ekleme / importmap.php'ye Ekleme



```php
// importmap.php
// ... lines 1 - 15
return [
// ... lines 17 - 43
    '@fontsource-variable/roboto-condensed/index.min.css' => [
        'version' => '5.0.1',
        'type' => 'css',
    ],
];
```

👉 Bu satır, font CSS dosyasını projenize ekler.

Son olarak, `app.css`'den import'u kaldırıp, bunu `app.js` üzerinden içe aktarın.

## 📝 assets/app.js Son Hali / assets/app.js Son Hali



```javascript
// assets/app.js
// ... lines 1 - 8
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.css';
import '@fontsource-variable/roboto-condensed/index.min.css';
import './styles/app.css';
// ... lines 13 - 18
```

👉 Bu satırlar, üçüncü parti tüm CSS dosyalarını projeye dahil eder.

Ayrıca, değişken fonta geçtiğimiz için, `app.css`'de font family değerini `Roboto Condensed Variable` olarak güncelleyin.

Siteyi yenileyince fontun değiştiğini görebilirsiniz. Üçüncü parti CSS dosyalarını almak `AssetMapper` ile yapacağınız en zor iş olabilir.

Eğer `Sass` ya da `Tailwind` kullanıyorsanız, `AssetMapper` ile çalışmak için Symfonycasts bundle'ları mevcut.

## 📄 .js Uzantısı Ekleme / .js Uzantısı Ekleme

Artık stil çalışıyor, şimdi JavaScript'e bakalım. Konsolda bir hata var: `bootstrap` adında bir şey için 404 hatası. Bunun sebebi `app.js`'deki import satırı. Bunu düzeltmek için, `app.js` dosyasını açın ve sonuna `.js` ekleyin.



```javascript
// assets/app.js
// ... lines 1 - 13
// start the Stimulus application
import './bootstrap.js';
// ... lines 16 - 18
```

👉 Gerçek bir JavaScript ortamında (tarayıcı gibi) `.js` uzantısı gereklidir.

`Webpack Encore` ile Node ortamında çalışıyoruz ve Node'da dosya ismi `.js` ile bitiyorsa bunu yazmanıza gerek yok. Fakat gerçek bir JavaScript ortamında bu gereklidir. Dönüşüm sırasında yapmanız gereken en büyük değişikliklerden biri budur.

## 🛠️ stimulus-bridge -> stimulus-bundle / stimulus-bridge -> stimulus-bundle

Şimdi sayfayı tekrar deneyin. Yeni bir hata:
`@symfony/stimulus-bridge` modül tanımlayıcısı çözümlenemedi.

Yani, bir yerde bu paketi içe aktarıyoruz... ama `importmap.php` dosyasında bu paket yok.

İki tür import vardır. İlki, `./` veya `../` ile başlayanlar: bunlar dosya ile ilgili olanlardır. İkincisi ise "bare import" denir. Bir paket veya paketteki bir dosya içe aktarılır. Bunlarda, import edilen string tam olarak `importmap.php` içinde bulunmalıdır. Bulunmazsa bu hatayı alırsınız.

Hatanın kaynağı `bootstrap.js`. Burada `@symfony/stimulus-bridge` kullanılmış, fakat bu `importmap.php`de yok. Çözüm genellikle bu paketi kurmaktır.

Ama bu durumda paket `Webpack Encore`'a özgü. Çözüm ise, bunu `@symfony/stimulus-bundle` olarak değiştirmektir.



```javascript
// assets/bootstrap.js
import { startStimulusApp } from '@symfony/stimulus-bundle';
// ... lines 2 - 8
```

👉 Bu, doğru paketin içe aktarılmasını sağlar.

Sonraki satır da sadeleşiyor.

 

```javascript
// assets/bootstrap.js
import { startStimulusApp } from '@symfony/stimulus-bundle';
// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp();
// ... lines 5 - 8
```

👉 Bu kod, Stimulus uygulamasını başlatıp controller'ları yükler.

Yeni Symfony uygulamasında bu yapı otomatik olarak gelir. Fakat dönüşüm yaptığımız için biraz daha uğraşmamız gerekiyor.

## 📦 Eksik Paketleri Kurma / Eksik Paketleri Kurma

Şimdi yenileyin. Aynı hata ama farklı paket: `axios`. Yani bir yerde içe aktarılmış... ama `importmap.php`'de yok.
Bu sefer, çözüm: paketi kurmak!



```bash
php bin/console importmap:require axios
```

👉 Bu komut, `axios` paketini projeye ekler.

Bu işlemden sonra uygulama çalışır. Artık modern ve hızlı bir ön yüzümüz var ve hiçbir derleyiciye ihtiyaç duymadan çalışıyor!

## ⬇️ Bir Bağımlılığı Düşürme / Bir Bağımlılığı Düşürme

Ama dipnot olarak, footer'daki yazı eskisine göre daha koyu. Daha önce `bootstrap 5.1` kullanıyorduk. `AssetMapper` ile yüklediğimizde en son sürüm olan `5.3` geldi. Görünüşe göre bir şeyler değişmiş.

İstersek neyin değiştiğini araştırıp düzeltebiliriz... Ama sürümü düşürmek de mümkün. `importmap.php` dosyasında sürümü `5.1.3` olarak değiştirin.



```php
// importmap.php
// ... lines 1 - 15
return [
// ... lines 17 - 29
    'bootstrap' => [
        'version' => '5.1.3',
    ],
// ... lines 33 - 35
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.1.3',
        'type' => 'css',
    ],
// ... lines 40 - 50
];
```

👉 Bu, bootstrap'ın eski sürümünü kullanmanızı sağlar.

Sadece bu değişikliği yapıp sayfayı yenilerseniz, bir şey değişmez; yeni sürüm hâlâ `assets/vendor/` dizininde duruyor. Dizini senkronize etmek için:

src/Controller/MainController.php

```bash
php bin/console importmap:install
```

👉 Bu komut, `importmap.php` ile senkronizasyon yapar ve gerekli paketleri indirir.

Bunu bir nevi `composer install` gibi düşünebilirsiniz. Değişen iki paketi algılar ve indirir. Artık AssetMapper ile çalışıyorsunuz!

Sırada, JavaScript'imizi modernleştirip sadeleştirmeye üç dakika ayırmak var.
