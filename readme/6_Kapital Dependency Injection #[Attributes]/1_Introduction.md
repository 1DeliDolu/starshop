# 👋 Introduction / Giriş

Merhaba arkadaşlar! Bağımlılık Enjeksiyonu `Attribute`'larıyla ilgili yepyeni bir kursa hoş geldiniz. Peki, Bağımlılık Enjeksiyonu `Attribute`'ları nedir? İşte kısa bir tarih dersi:

## 🕰️ A Brief History of Symfony Dependency Injection / Symfony Bağımlılık Enjeksiyonunun Kısa Tarihi

Uzun zaman önce, çok eski bir Symfony sürümünde, servisler (`service` - yani işleri yapan nesneler) ayrı `YAML` veya `XML` dosyalarında yapılandırılmak zorundaydı. Buna "bir servisi bağlamak" (`wiring up a service`) deniyordu. Bu dosyalarda bir `service ID` oluşturur, servis sınıfımızı referans gösterir ve gereken tüm `service ID`, parametre veya skaler değerleri eklerdik. Bu yöntem gayet iyi çalışsa da biraz zahmetliydi. Bir servisin argümanını eklemek, silmek veya değiştirmek istediğimizde başka bir yapılandırma dosyasına girip onu da güncellememiz gerekirdi. Eminim ki bunun daha iyi bir yolu bulunabilir, değil mi? Kesinlikle!

Daha sonraki bir Symfony sürümünde, yine eski zamanlarda, "autowiring" diye bir özellik eklendi. Bu sayede sadece bir PHP servis nesnesi oluşturup, gereken diğer servislerin otomatik olarak enjekte edilmesini sağladık. Tek yapmamız gereken servis sınıfı veya arayüzü üzerinde `type-hint` yapmaktı. Bu büyük bir ilerlemeydi fakat hala biraz daha gelişmiş senaryolarda (örneğin bir parametreden gelen skaler argümanlar gibi) `YAML` veya `XML` dosyalarında yapılandırma yapmamız gerekiyordu. Yapılandırma dosyalarında geçirilen zamanı azaltmıştı ama tamamen ortadan kaldırmamıştı.

## 🚀 Everything got better with PHP 8 / Her Şey PHP 8 ile Güzelleşti

Sonra `PHP 8` geldi ve sınıf özelliklerine, metotlara, metot argümanlarına ve daha fazlasına eklenebilen yerel `attribute` desteği getirdi. Bu, tüm yapılandırmayı servis sınıflarımızın içine eklememizi sağlayan mükemmel bir özellik oldu. Symfony 7.1 ile (bu kursta kullanacağımız sürüm) kullanabileceğimiz bir sürü Bağımlılık Enjeksiyonu `attribute`'ı var ve hepsine tek tek bakacağız. Ayrı bir yapılandırma dosyası düzenlemenin artık sadece çok ileri düzey, nadir senaryolarda gerektiğini göreceğiz. Genellikle her yeni Symfony uygulamasında bulunan `services.yaml` dosyası ihtiyacımız olan tek şey olacak ve çok nadiren değişecek. Tüm bu `attribute`'ları nasıl çalıştıklarını görebilmeniz için eğlenceli bir uygulamada göstereceğiz.

## ❓ The Problem / Sorun

Senaryo şöyle: Akıllı bir televizyonumuz var, fakat kumandayı kaybettik. Her yerde aradık ama hiçbir yerde yok. Muhtemelen o yaramaz kumanda cinleri kumandayı alıp "Gnomeslist"te satmaya götürdü...

İnternette yedek bir kumanda bulduk ama stokta yok ve gelmesi çok uzun sürecek. Ne yapacağız? "RemoteOverflow\.com" sitesinde bir çözüm ararken, bir kullanıcı (kesinlikle bir cüce değil) akıllı TV'miz için bir `API` olduğunu söyledi. Web geliştiricileri olarak, bu `API`'yi kullanarak ev ağımıza bağlı herhangi bir tabletten erişip kullanabileceğimiz kendi kumandamızı oluşturabiliriz. Neyse ki cüceler tabletimizi çalmamışlar, değil mi? Değil mi..?

## ⚙️ Install the App / Uygulamayı Kurun

Benimle birlikte kod yazmak için, bu videonun ders kodunu indirip tercih ettiğiniz `IDE` ile açın ve `README.md` dosyasındaki kurulum adımlarını takip edin. Ben bunu zaten yaptım. Şimdi terminalimize geçip aşağıdaki komutu çalıştırabiliriz:

src/Controller/MainController.php

```bash
symfony serve -d
```

👉 Bu komut, sunucuyu arka planda çalıştırır.

Buradaki URL'ye "command" tuşuna basılı tutup tıklayınca uygulamamız tarayıcıda açılır ve işte kumandamız! Kanal değiştirmek, TV'yi açıp kapatmak, sesi arttırıp azaltmak gibi klasik kumanda işlevlerini yapıyor. Şimdilik hepsi bu kadar. Koda bakarsak, neredeyse kutudan çıktığı gibi bir Symfony 7.1 uygulaması olduğunu göreceğiz.

## 🗺️ Tour the App / Uygulama Turu

Tek bir denetleyicimiz var - `RemoteController` - ve tek bir rota - `home`. Bu denetleyici arayüzün render edilmesini ve buton tıklamalarını yönetiyor. Bir butona tıklandığında, farklı buton mantığı işleniyor (bu bir `dump()` ile gösteriliyor), bir `flash message` ekleniyor ve aynı rotaya yönlendirme yapılıyor. Eğer bir buton tıklaması işlenmiyorsa, `index.html.twig` dosyasını render ediyor. Burası bizim kumanda şablonumuz.

`templates/` dizinimize bakalım, hızlıca `base.html.twig` dosyasını inceleyelim. Bu dosya standart bir Symfony kurulumundan geliyor. Arayüz `Tailwind CSS` kullanılarak stillendirildi ama alışık olabileceğinizden farklı bir yöntem izliyorum. `AssetMapper` veya `Webpack Encore` gibi bir varlık yönetim sistemi kurmak yerine, işleri basit tutmak için `Tailwind CSS CDN` kullanıyorum. Bu, sayfanıza biraz JavaScript enjekte ederek kullandığınız tüm Tailwind sınıflarını okuyor. Sonra özel bir CSS dosyası oluşturup sayfaya ekleyerek HTML'i stillendiriyor. Tahmin edebileceğiniz gibi, bu yöntem biraz yavaş ve asla prodüksiyonda kullanılmamalı. Prototipleme ve bizim amaçlarımız için ise başlamak kolay ve gayet güzel çalışıyor.

Şimdi `index.html.twig` dosyasına bakalım, burası gerçek kumanda şablonumuz. Oldukça standart, hangi Tailwind sınıflarını kullandığımızı görebilirsiniz. Burada bir `flash message` varsa onu gösteriyoruz ve işte gerçek butonlar burada. Hepsi bir `<form>` içinde ve her `<button>` tıklandığında o butonun ismiyle formu gönderiyor. Sadece biraz standart dışı olan şey `<twig:ux:icon ...>` etiketi. Bu etiket, Symfony UX İnisiyatifi'nin parçası olan iki üçüncü parti paketin birleşiminden oluşuyor.

## 🖼️ UX Icons / UX Simgeleri

Öncelikle, `symfony/ux-icons` paketini kullanıyoruz. Bu paket, `assets/icons` dizinine `.svg` dosyaları eklemenize olanak tanır. Şimdi bu SVG'leri, dosya adını (sonunda `.svg` olmadan) bu etiketteki `name` özniteliği olarak kullanarak Twig içinde gömebilirsiniz. Ayrıca burada olduğu gibi ek özellikler de ekleyebilirsiniz: `height`, `width`, `class` vs. Bunlar gömülen `<svg>` elementine eklenir. Bu paket başka harika şeyler de yapıyor, daha fazlasını öğrenmek için "Symfony UX Icons" araması yapabilirsiniz!

## 🧩 Twig Components / Twig Bileşenleri

Bu `<twig:ux:icons ...>` etiketinin diğer kısmı ise etiketin kendisi. Bu, `symfony/ux-twig-component` paketinden geliyor. Temelde, HTML öznitelikleri (örneğin `class`) gibi özellikleri bileşenlere geçirmenizi sağlayan daha gelişmiş bir Twig `{{ include() }}` etiketi gibi çalışıyor. Opsiyonel olarak sağladığı özelliklerden biri de bu HTML sözdizimi. Eğer Vue.js gibi frontend framework'lerdeki bileşenlere aşinaysanız, bu aslında Twig'in versiyonu. UX Icon paketi Twig Bileşenleri ile entegre olur ve `<twig:ux:icon...` etiketiyle kolayca UX\:Icon bileşeni oluşturmamızı sağlar. Bu, şablonumuzu daha okunabilir hale getirebilir. Twig bileşenleri ve yapabilecekleri tüm harika şeyler hakkında daha fazla bilgi için "Symfony Twig Components" araması yapın ve dökümantasyonu inceleyin!

## ⏭️ Next: Let's start refactoring our app to use dependency injection attributes. / Sıradaki Adım: Uygulamamızı bağımlılık enjeksiyonu attribute'ları kullanacak şekilde yeniden düzenlemeye başlayalım.
