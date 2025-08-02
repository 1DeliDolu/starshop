# ⚙️ Encore, StimulusBundle & their Recipe Changes / Encore, StimulusBundle ve Recipe Değişiklikleri

Bu bölümde Symfony recipe'larını güncellemeye devam edeceğiz. TwigBundle, WebpackEncoreBundle ve StimulusBundle recipe'larını güncelleyeceğiz.

## 🎯 Bölüm İçeriği

1. **TwigBundle Recipe Update** - Twig bundle güncelleme
2. **Recipe Yeniden Düzenlemeleri** - StimulusBundle ile gelen değişiklikler
3. **WebpackEncoreBundle Recipe Update** - Encore bundle güncelleme
4. **WebpackEncoreBundle v2'ye Yükseltme** - Major versiyon güncelleme
5. **StimulusBundle Recipe Update** - Stimulus bundle güncelleme
6. **Final WebpackEncoreBundle Update** - Son düzenlemeler

---

## 1. 📦 symfony/twig-bundle Recipe Update

### Komut: Recipe güncellemesi

```bash
composer recipes:update
# TwigBundle seçilir
```

**Simüle Edilen Çıktı**:

```
Which outdated recipe would you like to update? (default: 0)
  [0] symfony/twig-bundle
  [1] symfony/console
  [2] symfony/flex
  [3] symfony/framework-bundle
  ...
> 0

Updating recipe for symfony/twig-bundle...
Conflict in templates/base.html.twig
```

**Açıklama**: TwigBundle'da `templates/base.html.twig` dosyasında conflict var. Custom içerik korunmalı, default title ve favicon kaldırılmalı.

### Komut: Git add

```bash
git add templates
```

**Simüle Edilen Çıktı**:

```
# Dosyalar staging area'ya eklendi
```

### Komut: Git diff cached

```bash
git diff --cached
```

**Simüle Edilen Çıktı**:

```diff
--- a/templates/base.html.twig
+++ b/templates/base.html.twig
@@ -8,8 +8,6 @@
         <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⚫️</text></svg>">
         {# Run `composer require symfony/webpack-encore-bundle` to start using Symfony UX #}
-        {{ encore_entry_link_tags('app') }}
-        {{ encore_entry_script_tags('app') }}
     </head>
     <body>
         {% block body %}{% endblock %}
```

**Açıklama**: `encore_entry_link_tags()` ve `encore_entry_script_tags()` kaldırıldı çünkü bunlar WebpackEncoreBundle recipe'sine taşındı.

---

## 2. 🔄 Recipe Yeniden Düzenlemeleri (The Rearranging of Recipes)

**Açıklama**: StimulusBundle'ın tanıtılmasıyla birlikte recipe'lar yeniden düzenlendi:

-   `encore_entry_*()` fonksiyonları TwigBundle'dan WebpackEncoreBundle'a taşındı
-   `assets/controllers.json` WebpackEncoreBundle'dan StimulusBundle'a taşındı
-   Bu geçici bir durum, WebpackEncoreBundle recipe'si güncellendiğinde geri gelecek

---

## 3. 📦 symfony/webpack-encore-bundle Recipe Update

### Komut: Git commit

```bash
git commit -m "Update TwigBundle recipe"
```

### Komut: Recipe güncellemesi

```bash
composer recipes:update
# WebpackEncoreBundle seçilir
```

**Simüle Edilen Çıktı**:

```
Which outdated recipe would you like to update?
> symfony/webpack-encore-bundle

Updating recipe for symfony/webpack-encore-bundle...
Conflicts detected in multiple files
```

### Komut: Git status

```bash
git status
```

**Simüle Edilen Çıktı**:

```
On branch symfony7-ep4-doctrine-relations
Changes not staged for commit:
  modified:   package.json
  modified:   webpack.config.js
  modified:   templates/base.html.twig

Untracked files:
  symfony.lock.new
```

**Açıklama**: `package.json`'da Encore v3'ten v4'e geçiş var. Webpack ve Babel paketleri artık manuel eklenmeli.

### Alternatiflerin Analizi:

#### Seçenek A: Yeni versiyonu kabul et

```json
{
    "dependencies": {
        "@babel/core": "^7.22.0",
        "@babel/preset-env": "^7.22.0",
        "webpack": "^5.88.0",
        "webpack-cli": "^5.1.0"
    }
}
```

**Sonuç**: Encore 4 avantajları, modern build tools

#### Seçenek B: Eski versiyonu koru

```json
{
    "dependencies": {
        // Eski paketler kalır
    }
}
```

**Sonuç**: Geriye uyumluluk ama yeni özellikler yok

### Komut: Git add package.json

```bash
git add package.json
```

### Komut: Git diff

```bash
git diff
```

**Simüle Edilen Çıktı**:

```diff
--- a/webpack.config.js
+++ b/webpack.config.js
@@ -8,7 +8,7 @@ Encore
     .setOutputPath('public/build/')
     .setPublicPath('/build')
-    .configureBabelPresetEnv((config) => {
-        config.corejs = 3;
+    .configureBabelPresetEnv((config) => {
+        config.corejs = '3.32';
     })
-    .addEntry('app', './assets/app.js')
-    .enableStimulusBridge('./assets/controllers.json')
```

**Açıklama**: Core.js versiyonu güncellendi, plugin-proposal-class-properties artık gerekli değil.

### Komut: Git commit

```bash
git commit -m "Update WebpackEncoreBundle recipe to Encore 4"
```

### Komut: Yarn işlemleri

```bash
# Terminalde Ctrl+C ile yarn'ı durdur
yarn install
```

**Simüle Edilen Çıktı**:

```
yarn install v1.22.19
[1/4] 🔍  Resolving packages...
[2/4] 🚚  Fetching packages...
[3/4] 🔗  Linking dependencies...
[4/4] 🔨  Building fresh packages...
✨  Done in 45.23s.
```

### Komut: Yarn watch

```bash
yarn watch
```

**Simüle Edilen Çıktı**:

```
yarn run v1.22.19
$ encore dev --watch
ℹ ｢wds｣: Project is running at http://localhost:8080/
ℹ ｢wdm｣: Compiled successfully.
```

**Açıklama**: Artık Encore 4 ile build yapılıyor!

---

## 4. ⬆️ WebpackEncoreBundle v2'ye Yükseltme

### Komut: Composer.json düzenleme

```json
{
    "require": {
        "symfony/webpack-encore-bundle": "^2.0"
    }
}
```

### Komut: Composer update

```bash
composer up
```

**Simüle Edilen Çıktı**:

```
Loading composer repositories with package information
Updating dependencies
Package operations: 0 installs, 3 updates, 0 removals
  - Updating symfony/webpack-encore-bundle (v1.17.2 => v2.1.1)
  - Updating symfony/stimulus-bundle (v2.8.0 => v2.13.0)

! [NOTE] Some constraints could not be resolved:
  sensio/framework-extra-bundle requires doctrine/annotations

But continuing with installation...
```

**Açıklama**:

-   WebpackEncoreBundle v2'ye güncellendi
-   SensioFrameworkExtraBundle hatası (sonraki bölümde düzeltilecek)
-   Stimulus helper fonksiyonları StimulusBundle'a taşındı

### Komut: Git status

```bash
git status
```

**Simüle Edilen Çıktı**:

```
On branch symfony7-ep4-doctrine-relations
Changes not staged for commit:
  modified:   composer.json
  modified:   composer.lock
  modified:   symfony.lock
```

### Komut: Git commit

```bash
git commit -m "Upgrade WebpackEncoreBundle to v2"
```

### Komut: Recipe kontrol

```bash
composer recipes
```

**Simüle Edilen Çıktı**:

```
The following packages have new recipe updates:
  [0] symfony/stimulus-bundle
  [1] symfony/webpack-encore-bundle
```

**Açıklama**: Yeni versiyonlarla birlikte yeni recipe güncellemeleri geldi.

---

## 5. 📦 symfony/stimulus-bundle Recipe Update

### Komut: Recipe güncellemesi

```bash
composer recipes:update
# stimulus-bundle seçilir
```

**Simüle Edilen Çıktı**:

```
> 0
Updating recipe for symfony/stimulus-bundle...
Conflict in assets/controllers.json
```

### Komut: Git status

```bash
git status
```

**Simüle Edilen Çıktı**:

```
On branch symfony7-ep4-doctrine-relations
Changes not staged for commit:
  modified:   assets/app.js
  modified:   assets/controllers.json

Untracked files:
  assets/controllers/hello_controller.js
  symfony.lock.new
```

**Alternatiflerin Analizi**:

#### assets/controllers.json Conflict:

-   **Seçenek A**: Mevcut dosyayı koru → ✅ Önerilen
-   **Seçenek B**: Recipe'den geleni kabul et → ❌ Aynı içerik

#### assets/app.js'teki import:

```javascript
// Recipe eklemek istiyor:
import { Application } from "@hotwired/stimulus";

// Ama zaten var:
import { Application } from "@hotwired/stimulus";
```

### Komut: Dosyaları düzenle ve git add

```bash
# controllers.json'ı olduğu gibi bırak
git add assets/controllers.json

# app.js'ten duplicate import'ı kaldır
git add assets/app.js
```

### Komut: Git diff

```bash
git diff
```

**Simüle Edilen Çıktı**:

```diff
+++ b/assets/controllers/hello_controller.js
@@ -0,0 +1,11 @@
+import { Controller } from '@hotwired/stimulus';
+
+export default class extends Controller {
+    static targets = ['name']
+
+    connect() {
+        this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/hello_controller.js';
+    }
+}
```

**Açıklama**: Yeni `hello_controller.js` eklendi (isteğe bağlı tutulabilir).

---

## 6. 📦 symfony/webpack-encore-bundle V2 Recipe Update

### Komut: Git commit

```bash
git commit -m "Update StimulusBundle recipe"
```

### Komut: Final recipe update

```bash
composer recipes:update
# webpack-encore-bundle seçilir
```

### Komut: Git status

```bash
git status
```

**Simüle Edilen Çıktı**:

```
On branch symfony7-ep4-doctrine-relations
Changes not staged for commit:
  modified:   assets/app.js (trying to remove content)
  modified:   package.json (trying to remove dependencies)
  modified:   templates/base.html.twig (adds back encore_entry functions)

Changes to be deleted:
  assets/bootstrap.js
  assets/controllers.json
  webpack.config.js (trying to remove enableStimulusBridge)
```

### Alternatiflerin Analizi:

#### Seçenek A: Tüm değişiklikleri kabul et

```html
<!-- base.html.twig'de geri gelecek -->
{{ encore_entry_link_tags('app') }} {{ encore_entry_script_tags('app') }}
```

**Sonuç**: ✅ Encore functions geri gelir, ❌ Önemli dosyalar silinir

#### Seçenek B: Seçici kabul et

-   ✅ `base.html.twig`'deki encore functions'ları kabul et
-   ❌ Diğer silinme işlemlerini reddet

### Komut: Dosyaları koruma

```bash
# assets/app.js'ı eski haline döndür
git add assets/app.js

# package.json'ı eski haline döndür
git add package.json

# Sadece base.html.twig değişikliğini kabul et
```

### Komut: Git diff son kontrol

```bash
git diff
```

**Simüle Edilen Çıktı**:

```diff
--- a/templates/base.html.twig
+++ b/templates/base.html.twig
@@ -8,6 +8,8 @@
         <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⚫️</text></svg>">
         {# Run `composer require symfony/webpack-encore-bundle` to start using Symfony UX #}
+        {{ encore_entry_link_tags('app') }}
+        {{ encore_entry_script_tags('app') }}
     </head>
```

### Komut: Tüm değişiklikleri geri al ve sadece istenenileri kabul et

```bash
git reset HEAD
git checkout assets webpack.config.js
```

**Simüle Edilen Çıktı**:

```
HEAD is now at abc1234 Update StimulusBundle recipe
Updated 2 paths from the index.
```

### Komut: Final commit

```bash
git commit -m "Add back encore_entry functions to base template"
```

**Simüle Edilen Çıktı**:

```
[symfony7-ep4-doctrine-relations abc1235] Add back encore_entry functions to base template
 2 files changed, 2 insertions(+)
```

---

## 🎯 Özet ve Sonuçlar

### ✅ Başarılı Güncellemeler:

1. **TwigBundle** → Modern template yapısı
2. **WebpackEncoreBundle** → Encore 4 desteği
3. **StimulusBundle** → Yeni Stimulus özellikleri
4. **WebpackEncoreBundle v2** → Major versiyon yükseltme

### 🔄 Recipe Değişiklikleri:

-   `encore_entry_*()` fonksiyonları WebpackEncoreBundle'a taşındı
-   `assets/controllers.json` StimulusBundle'a taşındı
-   Modern build tools (Webpack 5, Babel 7)

### ⚠️ Dikkat Edilmesi Gerekenler:

-   Recipe conflicts dikkatli çözülmeli
-   Custom kodlar korunmalı
-   SensioFrameworkExtraBundle sorunu sonraki bölümde çözülecek

### 🛠️ Sonraki Adımlar:

-   SensioFrameworkExtraBundle kaldırılması
-   App'in düzeltilmesi
-   Final testler

Bu güncellemelerle artık modern Symfony frontend stack'ine sahip olduk! 🚀
