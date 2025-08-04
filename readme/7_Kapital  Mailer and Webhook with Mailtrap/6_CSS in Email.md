# 🎨 CSS in Email / E-postada CSS

E-postalarda CSS kullanımı... biraz özel ilgi gerektirir. Ama, pffff, biz Symfony geliştiricisiyiz! Cesurca devam edelim ve sonucu görelim!

## ➕ Add a CSS Class / CSS Sınıfı Ekle

`email/booking_confirmation.html.twig` dosyasında `<head>` içine bir `<style>` etiketi ekle ve `color: red;` olarak ayarlanan `.text-red` sınıfını ekle:

````twig
// templates/email/booking_confirmation.html.twig
```twig
<html>
<head>
    <style>
        .text-red {
            color: red;
        }
    </style>
</head>
// ... lines 9 - 26
</html>
````

👉 Bu kod, HTML şablonunun `<head>` kısmına bir CSS sınıfı ekler.

Şimdi, bu sınıfı ilk `<p>` etiketine ekle:

````twig
//templates/email/booking_confirmation.html.twig
```twig
// ... lines 1 - 8
<body>
<p class="text-red">Hey {{ customer.name|split(' ')|first }},</p>
// ... lines 11 - 25
</body>
// ... lines 27 - 28
````

👉 Bu kod, ilk paragrafı kırmızıya boyar.

Uygulamada tekrar Steve için bir seyahat rezervasyonu yap. Mailtrap’ta e-postayı kontrol et. Metin beklendiği gibi kırmızı... peki sorun ne? HTML kaynağını incele. İlk hatanın üzerine gel:

`<style>` etiketi tüm e-posta istemcilerinde desteklenmez.

Daha da önemlisi, `class` özniteliği de tüm e-posta istemcilerinde desteklenmez. Uzaya gidebiliyoruz ama e-postada CSS sınıfı kullanamıyoruz! Evet, dünya tuhaf bir yer.

## 🏷️ Inline CSS / Satır İçi CSS

Çözüm? Sanki 1999’dayız gibi tüm stilleri satır içine ekle. Yani, sınıf atanmış her etikete, tüm stilleri `style` özniteliği olarak eklemeliyiz. Bunu elle yapmak çok sıkıcı olurdu... Neyse ki, Symfony Mailer bunu kolaylaştırıyor!

## 🧩 inline\_css Twig Filter / inline\_css Twig Filtresi

Bu dosyanın en üstüne, `inline_css` filtresi ile bir Twig `apply` etiketi ekle. `apply` etiketi, bir içerik bloğuna herhangi bir Twig filtresini uygulamanı sağlar. Dosyanın sonunda ise `endapply` yaz:

````twig
// templates/email/booking_confirmation.html.twig
```twig
{% apply inline_css %}
<html>
// ... lines 3 - 27
</html>
{% endapply %}
````

👉 Bu kod, tüm şablon içeriğine inline CSS uygular.

Tekrar Steve için bir rezervasyon yap. Oops, bir hata! `inline_css` filtresi henüz kurulu değil, ama hata mesajında composer require komutu var! Kopyala ve terminalde çalıştır:

```bash
# Proje kök dizininde çalıştır
composer require twig/cssinliner-extra
```

👉 Bu komut, inline\_css filtresini kurar.

Uygulamaya dön, Steve’in seyahatini tekrar rezerve et ve Mailtrap’ta e-postayı kontrol et.

HTML aynı görünüyor ama HTML kaynağına bak. `style` özniteliği otomatik olarak `<p>` etiketine eklendi! Bu harika ve elle yapmaktan çok daha iyi.

## 📁 External CSS File / Harici CSS Dosyası

Uygulaman çoklu e-posta gönderiyorsa, her şablonda `<style>` etiketiyle stil tanımlamak yerine gerçek bir CSS dosyasından ortak stil vermek istersin. Ne yazık ki, `<head>` içine CSS dosyası eklemek e-posta istemcilerinde çalışmaz.

Sorun değil!

`assets/styles/` dizininde yeni bir `email.css` dosyası oluştur. E-posta şablonundaki CSS’yi buraya kopyala:

````css
//assets/styles/email.css
```css
.text-red {
    color: red;
}
````

👉 Bu dosya, e-posta için harici CSS stillerini tanımlar.

Şablona geri dön, `<style>` etiketini kaldır.

## 🗂️ Twig "styles" Namespace / Twig "styles" Ad Alanı

`config/packages/twig.yaml` dosyasını aç ve `paths` anahtarı ekle. İçine `%kernel.project_dir%/assets/styles: styles` satırını ekle:

````yaml
config/packages/twig.yaml
```yaml
twig:
// ... line 2
    paths:
        '%kernel.project_dir%/assets/styles': styles
// ... lines 5 - 9
````

👉 Bu ayar, assets/styles klasörüne @styles/ Twig ad alanı ile erişilmesini sağlar.

Bu sayede, bu dizindeki dosyaları `@styles/` önekiyle çağırabilirsin. email.css dosyası bir Twig şablonu değil, sadece içeriğine erişmemiz yeterli.

## 🔗 inline\_css() with source() / inline\_css ve source Kullanımı

`booking_confirmation.html.twig` dosyasında, `inline_css` filtresinin argümanı olarak `source('@styles/email.css')` kullan:

````twig
//templates/email/booking_confirmation.html.twig
```twig
{% apply inline_css(source('@styles/email.css')) %}
// ... lines 2 - 24
{% endapply %}
````

👉 Bu kod, harici email.css dosyasındaki stilleri inline olarak uygular.

Uygulamadan bir rezervasyon daha yap ve Mailtrap’ta e-postayı kontrol et. Görünüş aynı! Metin kırmızı. HTML kaynağına bakarsan, artık `<head>` kısmında class yok, ama stiller inline olarak eklendi: dışarıdan yükleniyorlar, harika!

Sırada, HTML ve CSS’i daha da geliştirip Steve’in gelen kutusuna ve satın aldığı pahalı seyahate layık bir e-posta yapacağız!
