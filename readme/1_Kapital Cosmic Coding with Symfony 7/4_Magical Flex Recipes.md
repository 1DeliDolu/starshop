# ✨ Magical Flex Recipes

Bir sırrım var. Projemiz oluşturulduğunda aslında 15 dosya değildi... bir dosyaydı. Eğer symfony new komutunun koduna bakarsan, bunun iki şey için bir kısayol olduğunu görürsün. Önce symfony/skeleton adlı bir repoyu Sırada, PHP'deki en sevdiğim kütüphanelerden biriyle tanışıp kuracağız: Twig şablon motoru.

---

ki lisansı saymazsan sadece bir dosyadır. Sonra composer install çalıştırır.

Hepsi bu! Ama dur, öyleyse bu diğer dosyalar nereden geldi? Mesela bin/, config/ ve src/ içindeki şeyler? Cevap composer.json dosyamızdaki özel bir paketle başlıyor: symfony/flex. Flex, Composer’a iki süper güç ekleyen bir Composer eklentisidir: takma adlar (alias) ve tarifler (recipe).

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('composer.json\n{\n// ... satır 2 - 5\n    \"require\": {\n// ... satır 7 - 11\n        \"symfony/flex\": \"^2\",\n// ... satır 13 - 15\n    },\n// ... satır 17 - 70\n}')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-json">composer.json
{
// ... satır 2 - 5
    "require": {
// ... satır 7 - 11
        "symfony/flex": "^2",
// ... satır 13 - 15
    },
// ... satır 17 - 70
}</code></pre>
  </div>
</div>

## Flex Takma Adları

Takma adlar basit. Uygulamana yeni bir paket eklemek için - birazdan yapacağız - composer require ve paket adını yazarsın, örneğin symfony/http-client. Flex, Symfony ekosistemindeki en önemli paketlere kısa bir ad, yani alias verir. Mesela symfony/http-client’ın alias’ı http-client. Evet, composer require http-client yazarsan Flex bunu gerçek paket adına çevirir. Paket eklerken sadece bir kısayol.

Tüm alias’ları görmek istersen, symfony/recipes reposuna git... sonra RECIPES.md dosyasına tıkla. Sağda hepsi var!

## Tarifler Sistemi

Symfony Flex’in Composer’a eklediği ikinci süper güç tariflerdir. Bunlar ilginçtir. Yeni bir paket eklediğinde, bir tarifi olabilir, bu da projene eklenecek dosya setidir. Ve aslında başladığımız her dosya - bin/, config/, public/ - bunların hepsi ilk kurulan paketlerin tariflerinden geldi.

Örneğin, symfony/framework-bundle Symfony Framework’ün "çekirdek" paketidir. Tarifini görmek için symfony/recipes reposuna git, symfony, framework-bundle, sonra en son sürüme bak. Boom! config/packages/’a bak: başladığımız şeylerin çoğu bu tariften gelmiş!

Tarifleri başka bir şekilde de görebilirsin. Komut satırında şunu çalıştır:

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('composer recipes')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">composer recipes</code></pre>
  </div>
</div>

Görünüşe göre dört farklı paketin tarifi kurulmuş. Herhangi biriyle ilgili bilgi almak için komutun sonuna adını ekleyebilirsin.

Tarifler harika çünkü bir paketi kurunca ihtiyacımız olan dosyaları anında alıyoruz. Konfigürasyonla uğraşmak yerine hemen işe koyuluyoruz.

## PHP CS Fixer Kurulumu

Bunu deneyelim: Kodumuzun stilini düzeltecek bir PHP-CS-Fixer paketi ekleyelim. Mesela src/Controller/MainController.php dosyasında, PHP kodlama standartlarına göre süslü parantez fonksiyondan sonraki satırda olmalı. Eğer şöyle yaparsak, dosyamız artık bu standartlara uymuyor. Zararı yok ama kodumuzun temiz görünmesini isteriz. PHP-CS-Fixer bunu düzeltebilir.

Kurmak için şunu çalıştır:

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('composer require cs-fixer-shim')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">composer require cs-fixer-shim</code></pre>
  </div>
</div>

Evet, bu bir alias. Gerçek paket php-cs-fixer/shim.

Bu paket bir tarifle mi geldi? Evet! Configuring php-cs-fixer/shim bunu söylüyor. Ama şunu çalıştırarak da görebilirsin:

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('git status')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">git status</code></pre>
  </div>
</div>

composer.json ve composer.lock’ın değişmesi tamamen normal Composer davranışı. composer.json’da yeni kütüphaneyi require anahtarı altında görebilirsin.

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('composer.json\n{\n// ... satır 2 - 5\n    \"require\": {\n// ... satır 7 - 9\n        \"php-cs-fixer/shim\": \"^3.46\",\n// ... satır 11 - 16\n    },\n// ... satır 18 - 69\n}')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-json">composer.json
{
// ... satır 2 - 5
    "require": {
// ... satır 7 - 9
        "php-cs-fixer/shim": "^3.46",
// ... satır 11 - 16
    },
// ... satır 18 - 69
}</code></pre>
  </div>
</div>

Ama diğer tüm değişen veya yeni dosyalar paketin tarifi sayesinde.

## Tarifi İncelemek

Bunlara bakalım! .gitignore’u aç. Güzel! En altta PHP CS fixer kullanırken ignore etmek isteyeceğin iki dosya için iki yeni satır eklenmiş.

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('.gitignore\n// ... satır 1 - 11\n###> php-cs-fixer/shim ###\n/.php-cs-fixer.php\n/.php-cs-fixer.cache\n###< php-cs-fixer/shim ###')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">.gitignore
// ... satır 1 - 11
###> php-cs-fixer/shim ###
/.php-cs-fixer.php
/.php-cs-fixer.cache
###< php-cs-fixer/shim ###</code></pre>
  </div>
</div>

Tarif ayrıca yeni bir .php-cs-fixer.dist.php dosyası ekledi. Bu CS Fixer’ın konfigürasyon dosyası. Bakalım!

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('<?php\n$finder = (new PhpCsFixer\\Finder())\n    ->in(__DIR__)\n    ->exclude(\'var\')\n;\nreturn (new PhpCsFixer\\Config())\n    ->setRules([\n        \'@Symfony\' => true,\n    ])\n    ->setFinder($finder)\n;')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-php"><?php
$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;
return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;</code></pre>
  </div>
</div>

Symfony uygulamamız için önceden hazırlanmış. Tüm dosyaları mevcut dizinde düzeltmesini, var/ dizinini hariç tutmasını ve Symfony kurallarını kullanmasını söylüyor. Yani kod stilimiz Symfony’ye uygun olacak. Yani, bu varsayılan konfigürasyonu aramakla uğraşmak yerine, hazır geliyor!

Son değişen dosya symfony.lock. Hangi tariflerin hangi sürümde kurulu olduğunu takip eder. Evet, bunların hepsini repoya commitleyeceğiz.

## PHP-CS-Fixer Kullanmak

Paketi kurduğumuza göre kullanalım. Şunu çalıştır:

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('./vendor/bin/php-cs-fixer')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">./vendor/bin/php-cs-fixer</code></pre>
  </div>
</div>

Tüm komutları gösterir. İstediğimiz komut fix. Deneyelim:

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('./vendor/bin/php-cs-fixer fix')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">./vendor/bin/php-cs-fixer fix</code></pre>
  </div>
</div>

Ve... evet! MainController.php’deki ihlali buldu! Dosyaya gidince... evet! Süslü parantezi satır sonundan bir sonraki satıra taşıdı. Harika.

Sırada, PHP’deki en sevdiğim kütüphanelerden biriyle tanışıp kuracağız: Twig şablon motoru.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./3_Routes Controllers & Responses.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./5_Twig & Templates.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
