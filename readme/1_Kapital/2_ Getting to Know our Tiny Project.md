# Getting to Know our Tiny Project / Küçük Projemizi Tanımak

Komuta merkezine (yani terminale) geri dön! Bu ilk sekmede web sunucusu çalışıyor. Durdurmak istersen Ctrl-C'ye bas... sonra tekrar başlatmak için:

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('symfony serve')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">symfony serve</code></pre>
  </div>
</div>

İpucu: `symfony serve -d` komutunu kullanarak sunucuyu "arka planda" çalıştırabilirsin ve terminal sekmesini kullanmaya devam edebilirsin.

Biz onu öylece bırakıp işini yapmasına izin vereceğiz.

## Projemizdeki 15 Dosya

Aynı dizinde ikinci bir terminal sekmesi aç. `symfony new` komutunu çalıştırdığımızda, küçük bir proje indirildi ve bir Git deposu başlatıldı, ilk commit atıldı. Harika! Dosyalarımızı görmek için, bu dizini favori editörüm PhpStorm ile açacağım. Bu editörden birazdan bahsedeceğim.

Şu anda, projemizin ne kadar küçük olduğuna dikkat etmeni istiyorum! Tüm commitlenmiş dosyaları görmek için terminalde şunu çalıştır:

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('git ls-files')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">git ls-files</code></pre>
  </div>
</div>

Evet, hepsi bu kadar. Git'e commitlenmiş sadece yaklaşık 15 dosya var!

## Symfony Nerede?

Peki... Symfony nerede? Bu 15 dosyadan biri özellikle önemli: `composer.json`.

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('{\n    // ... satır 2 - 5\n    \"require\": {\n        \"php\": \">=8.2\",\n        \"ext-ctype\": \"*\",\n        \"ext-iconv\": \"*\",\n        \"symfony/console\": \"7.0.*\",\n        \"symfony/dotenv\": \"7.0.*\",\n        \"symfony/flex\": \"^2\",\n        \"symfony/framework-bundle\": \"7.0.*\",\n        \"symfony/runtime\": \"7.0.*\",\n        \"symfony/yaml\": \"7.0.*\"\n    },\n    // ... satır 17 - 70\n}')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-json">{
    // ... satır 2 - 5
    "require": {
        "php": ">=8.2",
        "ext-ctype": "*",
        "ext-iconv": "*",
        "symfony/console": "7.0.*",
        "symfony/dotenv": "7.0.*",
        "symfony/flex": "^2",
        "symfony/framework-bundle": "7.0.*",
        "symfony/runtime": "7.0.*",
        "symfony/yaml": "7.0.*"
    },
    // ... satır 17 - 70
}</code></pre>
  </div>
</div>

Composer, PHP için paket yöneticisidir. Görevi basit: `require` anahtarı altındaki paket adlarını okur ve indirir. `symfony new` komutunu çalıştırdığımızda, bu 15 dosyayı indirdi ve ardından `composer install` komutunu çalıştırdı. Bu da tüm bu paketleri `vendor/` dizinine indirdi.

Yani Symfony nerede? `vendor/symfony/...` dizininde ve şimdiden yaklaşık 20 paketini kullanıyoruz!

## Composer'ı Çalıştırmak

`vendor/` dizini git'e commitlenmez. Başka bir dosya sayesinde - `.gitignore` - bu dizin yok sayılır.

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('###> symfony/framework-bundle ###\n/.env.local\n/.env.local.php\n/.env.*.local\n/config/secrets/prod/prod.decrypt.private.php\n/public/bundles/\n/var/\n/vendor/\n###< symfony/framework-bundle ###')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">###> symfony/framework-bundle ###
/.env.local
/.env.local.php
/.env.*.local
/config/secrets/prod/prod.decrypt.private.php
/public/bundles/
/var/
/vendor/
###< symfony/framework-bundle ###</code></pre>
  </div>
</div>

Bu, bir ekip arkadaşı projemizi klonlarsa bu dizine sahip olmayacağı anlamına gelir. Sorun değil! Her zaman `composer install` komutunu çalıştırarak tekrar oluşturabiliriz.

Bak: Sağ tıklayıp tüm `vendor/` dizinini siliyorum. Vay canına!

Şimdi uygulamayı denesek, çalışmaz. Kötü his! Düzeltmek ve günü kurtarmak için terminalde şunu çalıştır:

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('composer install')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">composer install</code></pre>
  </div>
</div>

Ve... presto! Dizin geri geldi... ve site tekrar çalışıyor.

## İlgileneceğin 2 Dizin

Dosyalara bakınca, aslında ilgilenmemiz gereken sadece iki dizin var. Birincisi `config/`: burada yapılandırma dosyaları var! Bu dosyaların ne işe yaradığını ileride öğreneceğiz.

İkincisi ise `src/`. Tüm PHP kodun burada olacak.

Gerçekten hepsi bu! Zamanının %99'unu ya bir şeyi yapılandırarak ya da PHP kodu yazarak geçirirsin. Bunlar da `config/` ve `src/` dizinlerinde olur.

Diğer 4 dizin ne olacak? `bin/` sadece bir konsol çalıştırılabilir dosyası tutar, yakında deneyeceğiz ama asla bakmayacağız veya değiştirmeyeceğiz. `public/` dizini "document root" olarak bilinir. Buraya koyduğun her şey - örneğin bir resim - herkese açık olur. Daha sonra bundan bahsedeceğiz. Ayrıca burada `index.php` dosyası var.

```
<?php
use App\Kernel;
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
```

Bu dosya "front controller" olarak bilinir: web sunucunun her isteğin başında çalıştırdığı ana PHP dosyasıdır. Çok önemli... ama asla düzenlemeyecek veya düşünecek değilsin.

Sırada `var/` var. Bu da git tarafından yok sayılır: Symfony'nin log ve cache dosyalarını sakladığı yerdir. Yani çok önemli... ama ilgilenmemiz gerekmiyor. Ve zaten `vendor/`'dan bahsettik. Hepsi bu!

## PhpStorm'u Hazırlamak

Kodlamaya başlamadan önce, PhpStorm kullandığımı söylemiştim. İstediğin editörü kullanabilirsin. Ancak PhpStorm harika. En büyük nedeni eşsiz Symfony eklentisi. PhpStorm -> Ayarlar'a gidip "Symfony" aratırsan, Eklentiler ve Marketplace altında bulabilirsin. Eklentiyi indirip kur. Kurulumdan sonra PhpStorm'u yeniden başlat. Son bir adım daha var. Ayarlara tekrar girip "Symfony" arat. Bu sefer bir Symfony bölümü olacak. Her Symfony projesi için eklentiyi etkinleştirdiğinden emin ol... yoksa benim sahip olduğum sihri göremezsin.

Tamam! Şimdi kodlamaya başlayalım ve Symfony'de ilk sayfamızı oluşturalım.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./1_ Setting up our Symfony App.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./3_Routes Controllers & Responses.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
