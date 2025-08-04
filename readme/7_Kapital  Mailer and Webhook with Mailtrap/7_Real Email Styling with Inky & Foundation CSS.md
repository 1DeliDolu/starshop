# 💎 Real Email Styling with Inky & Foundation CSS / Inky ve Foundation CSS ile Gerçek E-posta Stili

Bu e-postanın gerçekten şık görünmesini sağlamak için HTML ve CSS'i geliştirmemiz gerekiyor.

## 🏗️ Foundation CSS for Emails / E-postalar için Foundation CSS

Web sitelerinde muhtemelen Tailwind (bizim uygulamamızda da var), Bootstrap ya da Foundation gibi bir CSS framework’ü kullandın. E-postalar için de benzeri var mı? Evet! Üstelik e-postalar için bir framework kullanmak daha da önemli çünkü farklı e-posta istemcileri aynı stili farklı şekilde işler.

E-postalar için Foundation öneriyoruz çünkü bunun e-postalara özel bir framework’ü var. Google’da "Foundation CSS" araması yap ve ilgili sayfayı bul.

"CSS Version" için başlangıç kitini indir. Bu zip dosyasında asıl framework olan `foundation-emails.css` dosyası var.

Bunu zaten `tutorials/` dizininde mevcut olarak bulabilirsin. `assets/styles/` dizinine kopyala.

`booking_confirmation.html.twig` dosyamızda, `inline_css` filtresi birden fazla argüman alabilir. İlk argüman olarak `source('@styles/foundation-emails.css')`, ikinci argüman olarak ise `email.css` kullan:

````twig
//templates/email/booking_confirmation.html.twig
```twig
{% apply inline_css(source('@styles/foundation-emails.css'), source('@styles/email.css')) %}
// ... lines 2 - 24
{% endapply %}
````

👉 Bu kod, Foundation ve kendi özel stillerimizi e-posta içeriğine inline olarak uygular.

## 📝 Custom Email CSS / Özel E-posta CSS’i

`email.css` dosyasına aşağıdaki özel CSS kodunu ekle:

````css
//assets/styles/email.css
```css
.trip-name {
    font-size: 32px;
}
.accent-title {
    color: #666666;
}
.trip-image {
    border-radius: 12px;
}
````

👉 Bu kod, e-posta için özel başlık ve görsel stilleri tanımlar.

## 📑 Tables! / Tablolar!

HTML’i geliştirmemiz gerekiyor. Ancak tuhaf bir durum var! Web siteleri için kullandığımız çoğu stil yöntemi e-postalarda çalışmaz. Mesela Flexbox veya Grid kullanamayız. Onun yerine, düzen için tablolar kullanmalıyız. Tablolar, tabloların içinde tablolar! (Kabusa hoş geldin!)

## 💡 Inky Templating Language / Inky Şablon Dili

Neyse ki, bunu kolaylaştıracak bir şablon dili var: `Inky`. Bunu "inky templating language" diye aratarak bulabilirsin. Inky, Zurb Foundation tarafından geliştirilmiş. Zurb, Inky, Foundation... bu isimler uzay temamıza da tam uyuyor! Hepsi birlikte çalışıyor.

Nasıl çalıştığını hızlıca görebilirsin. Bir e-posta için gerekli HTML’i gösteriyor, sonra "Switch to Inky" sekmesine tıkla; çok daha okunabilir! Biz daha okunabilir biçimde yazıyoruz, Inky bunu e-posta için gereken tablo-hell HTML’ine dönüştürüyor.

Buton, callout, grid gibi Inky bileşenleri de var.

Terminalde, Inky işaretlemesini HTML’e dönüştüren bir Twig filtresi yükle:

```bash
# Proje kök dizininde çalıştır
composer require twig/inky-extra
```

👉 Bu komut, Twig için inky\_to\_html filtresini kurar.

## 🏷️ inky\_to\_html Twig Filter / inky\_to\_html Twig Filtresi

`booking_confirmation.html.twig` dosyasında, `apply` etiketinde önce `inky_to_html`, ardından pipe ile `inline_css` filtresini kullan:

````twig
//templates/email/booking_confirmation.html.twig
```twig
{% apply inky_to_html|inline_css(source('@styles/foundation-emails.css'), source('@styles/email.css')) %}
    <container>
        <row>
            <columns>
                <spacer size="40"></spacer>
                <p class="accent-title">Get Ready for your trip to</p>
                <h1 class="trip-name">{{ trip.name }}</h1>
            </columns>
        </row>
        <row>
            <columns>
                <p class="accent-title">Departure: {{ booking.date|date('Y-m-d') }}</p>
            </columns>
        </row>
        <row>
            <columns>
                <button class="expanded rounded center" href="{{ url('booking_show', {uid: booking.uid}) }}">
                    Manage Booking
                </button>
                <button class="expanded rounded center secondary" href="{{ url('bookings', {uid: customer.uid}) }}">
                    My Account
                </button>
            </columns>
        </row>
        <row>
            <columns>
                <p>We can't wait to see you there,</p>
                <p>Your friends at Universal Travel</p>
            </columns>
        </row>
    </container>
{% endapply %}
````

👉 Bu şablonda Inky dilinde yazılmış markup, otomatik olarak tablo tabanlı HTML’e ve inline CSS’e dönüştürülür.

Bir `container` içinde, `row` ve `columns` ile tek sütunlu bir e-posta var. Ne kadar sütun istersen ekleyebilirsin. `<spacer>` etiketi dikey boşluk ekler.

Uygulamada tekrar yeni bir seyahat rezervasyonu yap (gelecek bir tarih olmalı), ardından Mailtrap’ı kontrol et.

E-posta çok daha iyi görünüyor! Mailtrap’te mobil ve tablet görünümüne de bakabilirsin.

"HTML Check" kısmında bazı sorunlar görünebilir ama Foundation ve Inky’yi doğru kullandığın sürece genelde sorun olmaz.

Butonları dene: "Manage Booking", evet çalışıyor. "My Account", o da çalışıyor. Foundation ve Inky sayesinde hızlıca şık bir e-posta oluşturduk!

Sırada, seyahat görselini gömüp, hukuk ekibini sevindirecek "hizmet şartları" PDF ekini e-postaya eklemek var.
