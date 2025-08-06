# 🍋 Meet LemonSqueezy - Your Merchant of Record / LemonSqueezy ile Tanışın - Kayıtlı Satıcınız

Merhaba ve başka bir e-ticaret eğitimine hoş geldiniz! Bu eğitimi çok abartmak istemem ama gerçekten harika olduğunu düşünüyorum! Çevrimiçi ürün satmak artık oldukça kolay ve heyecan verici çünkü birçok ödeme sağlayıcısı mevcut... ta ki vergi ve yasal uyumluluk işleriyle ilgilenmeniz gerektiğini fark edene kadar.

Tüm bu bilgileri yönetmeyi basitleştirebilir miyiz? Elbette! Bunu yapmak için, bir `Merchant of Record` (MoR), yani `Kayıtlı Satıcı` olan bir ödeme sağlayıcısına ihtiyacımız var. "Bu da ne şimdi?" diye sorabilirsiniz. Bu, sıradan bir ödeme sağlayıcısı gibidir ama süper güçleri vardır. `Merchant of Record`, sizin yerinize vergi gibi tüm karmaşık yasal ve finansal işleri halleder. Artık KDV kabusları ya da uyumluluk sorunları yok — sadece tatlı tatlı satış yapabilirsiniz.

Bu işlemi Lemon Squeezy ile nasıl kuracağınızı göstereceğiz, ancak anlatılanlar Paddle veya Polar gibi diğer hizmetlere de uygulanabilir. Hangi hizmeti seçeceğiniz size kalmış: biz LemonSqueezy’yi anlatıyoruz ama özel bir terciğimiz yok.

İlk iş olarak kayıt olmalıyız. Ürün satmaya başlamadan önce "Get started" butonuna tıklayıp adınızı, e-posta adresinizi ve süper güçlü bir parolayı girin — malum, ödeme işleriyle uğraşıyoruz. Daha fazla güvenlik için daha sonra `2FA` yapılandırabilirsiniz. Gerçek e-posta adresinizi kullandığınızdan emin olun çünkü başlamadan önce onaylamanız gerekecek. Ayrıca bazı test e-postaları göndereceğiz ve onları görmek isteyeceksiniz.

## 🧭 A Quick Tour of the dashboard / Pano’ya Hızlı Bir Bakış

Ben zaten kaydoldum, bu yüzden sadece giriş yapacağım. Ve... "Squeeze the Day"e hoş geldiniz — dijital tasarım limonata tezgâhım! Bu eğitimi hazırlarken bazı veriler topladım, bu nedenle kontrol panelimde birkaç grafik var. Sizinkinin tamamen boş olması normaldir.

Kayıt olduktan sonra mağazanızı çalışır hale getirmek için tamamlamanız gereken birkaç adımın yer aldığı bir kurulum sayfası göreceksiniz, ancak şimdilik onları erteleyebiliriz. `Test modu`nda entegrasyonumuza başlayabiliriz. `Test modunda` olup olmadığımızı kenar çubuğundaki küçük anahtardan kontrol edebiliriz. Bu, gerçek para harcamadan test kart numaralarıyla ödeme simülasyonu yapabileceğimiz anlamına geliyor. Geliştirme sırasında bu çok işe yarayacak çünkü çok fazla sahte para harcayacağım. Kurulum adımlarını tamamlayıp mağazanızı etkinleştirdiğinizde, bu anahtarı kullanarak `gerçek mağaza` ve `test mağazası` arasında geçiş yapabilirsiniz, bunu unutmayın.

Teknik olarak LemonSqueezy ile satış yapmaya başlamak için bir web sitesine bile ihtiyacınız yok. Harika, değil mi? LemonSqueezy mağaza URL’nizi müşterilerinizle paylaşmanız yeterli ve ürünlerinizi doğrudan oradan satın alabilirler. Peki biz ne satacağız? Haydi ilk ürünümüzü oluşturalım ve paraları kazanalım!

## 🛒 Create a Product / Bir Ürün Oluşturun

İlk olarak "Store"... "Products" sekmesine gidiyoruz ve "New Product" butonuna tıklıyoruz. Bir limonata işimiz olduğuna göre, ilk ürüne "Classic Lemonade" adını verelim — sade ve şık. Açıklama olarak "A classic citrus lemonade" yazalım.

Şimdi fiyatlandırma modelini ayarlayabiliriz ve birkaç seçenek mevcut: müşteriden tek seferlik ücret alan `Single payment`, bir sonraki derste göreceğimiz `Subscription`, ücretsiz erişim sağlayan `Lead magnet` ve müşterinin istediği miktarı ödeyebileceği `Pay what you want` seçeneği.

En basit olanla başlayalım — `Single payment` ve `Standard pricing` modeliyle. Fiyatı da... ne dersiniz, \$0.99 yapalım? `Tax category` (vergi kategorisi) kısmında birkaç seçenek var. Bizim için en uygun olanı: `Digital goods or services (excluding ebooks)`. Evet! Limonatamız dijital. Daha önce de belirttiğim gibi, LemonSqueezy bir `Merchant of Record` olduğu için, platformda yalnızca dijital ürünler satılmasına izin veriliyor, bu nedenle gerçek limonata satamayız. Kötü haber. Ama hey! Biz bir ilki yapıyoruz ve dünyanın ilk dijital limonatasını sunuyoruz! Ürün adımızı "Classic E-Lemonade" olarak değiştiriyoruz, daha net olması için.

Tamam, ürünümüzü resmi olarak listelemeden önce, `Prohibited products` listesinde olup olmadığını kontrol etmeliyiz. Bu belgeye `docs.lemonsqueezy.com` adresinden ulaşabilirsiniz. "Resources"... "Help Docs"... ve aşağıda "Prohibited products" bölümünü göreceksiniz:

[https://docs.lemonsqueezy.com/help/getting-started/prohibited-products](https://docs.lemonsqueezy.com/help/getting-started/prohibited-products)

Belgeyi inceledikten sonra ürününüz hakkında hala emin değilseniz, destek ekibine danışabilirsiniz.

Tamam, ürünümüze geri dönelim! Başarılı bir ürünün harika bir görsele ihtiyacı vardır! Lezzetli limonatamızın... pardon, e-limonatamızın fotoğrafını yükleyelim ki insanlar "Satın Al" butonuna basmak istesin! Ve sürpriz! Görselimiz hazır! Kurs dosyalarını indirip zip dosyasını açarsanız, içinde bu eğitimdeki kodun yer aldığı `start/` dizinini bulacaksınız. Görsel `tutorial/` dizininde yer alıyor. Görseli sürükleyip medya bölümüne bırakmanız yeterli. Ohhh evet! O kadar iştah açıcı görünüyor ki hemen almak istiyorum! Eğer dosya satışı yapıyorsanız, ürüne bir dosya ekleyebilirsiniz ve müşterileriniz satın alma işleminden hemen sonra erişim sağlayabilir. Linkler için de aynı şey geçerli! E-limonatamız için linke gerek yok, şimdilik boş bırakıyoruz.

Sırada "Variants" var. Farklı seçeneklere (tat, boyut, renk vb.) sahip ürünleriniz olduğunda bu bölüm faydalıdır. Kesinlikle daha fazla e-limonata aroması satacağız ve müşterilerimiz her aromanın ne kadar lezzetli olduğunu görürse diğerlerini de alma olasılığı artar, değil mi? Ancak `variants` görsel içermez, bu yüzden her yeni aromayı ayrı bir ürün olarak oluşturmak daha mantıklı olacaktır. Bu kısmı boş geçiyoruz. Bu konuya sonra değineceğiz!

`License keys` satıyorsanız bu seçeneği etkinleştirebilirsiniz. Bizim ihtiyacımız yok, bu yüzden olduğu gibi bırakıyoruz. Bu anahtar varsayılan olarak etkindir ve ürünü LemonSqueezy mağaza sayfasında göstermenizi sağlar — henüz bir web mağazanız yoksa oldukça işe yarar. "Confirmation modal" ve "Email receipt" bölümlerini de özelleştirebilirsiniz ama varsayılan ayarlar şimdilik yeterli. Bu arada, LemonSqueezy tüm e-postaları sizin yerinize gönderir. Bir müşteri mağazanızdan bir şey satın aldığında, LemonSqueezy otomatik olarak sipariş e-postasını gönderir. Karmaşık e-posta işleriyle uğraşmanıza gerek yok. Büyük zaman tasarrufu!

Son olarak, ürünümüzü `Publish` edelim. "Publish product" butonuna tıklayın ve... tamam! İlk LemonSqueezy ürününüzü oluşturdunuz! "Product Details" sayfasını kapatın ve işte orada! "Published" durumunda ve mağazanızda zaten listelenmiş durumda! Harika!

Sıradaki adım, müşterilerimizin ne göreceğine bakmak — yepyeni mağazamıza göz atalım!
