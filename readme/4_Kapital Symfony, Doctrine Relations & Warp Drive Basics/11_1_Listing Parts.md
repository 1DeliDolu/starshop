# 🛠️ Listing Parts / Parçaları Listeleme

Yeni görev: Her mevcut parçayı listeleyen bir sayfaya ihtiyacımız var. `Ferengi` satış ekibimiz bu sayfayı klasik çapraz satış için kullanacak. Bildiğiniz gibi:

_"Hey, bir uzay gemisi aldınız, parlak yeni `dilithium crystal organizer` ya da `cupholder stabilizer` ister misiniz?"_

Bize hızlı bir başlangıç sağlamak için `MakerBundle` kullanalım. Terminalinizi açın ve şunu çalıştırın:

```bash
php bin/console make:controller
```

👉 Bu komut, yeni bir `controller` oluşturur.

Adını... tahmin edin... `PartController` koyun. Odaklanmak için test eklemeyin (no).

Ve işte! Bir sınıf ve bir şablon dosyası. Şimdiye kadar her şey yolunda. Yeni `PartController` dosyasına bir göz atın:

```php
// src/Controller/PartController.php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PartController extends AbstractController
{
    #[Route('/part', name: 'app_part')]
    public function index(): Response
    {
        return $this->render('part/index.html.twig', [
            'controller_name' => 'PartController',
        ]);
    }
}
```

👉 Bu sınıf, bir şablonu render ediyor. Şimdilik başka bir şey yapmıyor.

Fazla görülecek bir şey yok: sadece bir şablonu render ediyor. Vay canına!

## 🔧 Route Configuration / Rota Yapılandırması

URL'yi `/parts` olarak değiştirin ve adını `app_part_index` yapın:

```php
// src/Controller/PartController.php
// ... lines 1 - 9
final class PartController extends AbstractController
{
    #[Route('/parts', name: 'app_part_index')]
    public function index(): Response
    {
        return $this->render('part/index.html.twig', [
            'controller_name' => 'PartController',
        ]);
    }
}
```

👉 Bu değişiklik, rotanın yolunu ve adını günceller.

Rota adını kopyalayın, böylece bağlantı verebiliriz... ve `base.html.twig` dosyasını açın.

## 🔗 Linking to the Parts Page / Parçalar Sayfasına Bağlantı Verme

O boş duran "About" bağlantısını hatırlıyor musunuz? Onu kullanın ve "Parts" bağlantısına çevirin. `href` değerini `{{ path('app_part_index') }}` olarak ayarlayın:

```twig
// templates/base.html.twig
// ... line 1
<html>
// ... lines 3 - 13
    <body class="text-white" style="background: radial-gradient(102.21% 102.21% at 50% 28.75%, #00121C 42.62%, #013954 100%);">
        <div class="flex flex-col justify-between min-h-screen relative">
            <div>
                <header class="h-[114px] shrink-0 flex flex-col sm:flex-row items-center sm:justify-between py-4 sm:py-0 px-6 border-b border-white/20 shadow-md">
// ... lines 18 - 20
                    <nav class="flex space-x-4 font-semibold">
                        <a class="hover:text-amber-400 pt-2" href="{{ path('app_homepage') }}">
                            Home
                        </a>
                        <a class="hover:text-amber-400  pt-2" href="{{ path('app_part_index') }}">
                            Parts
                        </a>
                        <a class="hover:text-amber-400 pt-2" href="#">
                            Contact
                        </a>
                        <a class="rounded-[60px] py-2 px-5 bg-white/10 hover:bg-white/20" href="#">
                            Get Started
                        </a>
                    </nav>
                </header>
// ... line 34
            </div>
// ... lines 36 - 42
        </div>
    </body>
</html>
```

👉 Bu kodda, navigasyon çubuğundaki bağlantı artık "Parts" sayfasına yönlendiriyor.

Ana sayfaya gidin, yeni bağlantıya tıklayın ve... henüz çok güzel görünmese de, çalışıyor!

Kutlamadan önce, başlığı sıkıcı `Hello PartController` ifadesinden değiştirelim. `templates/part/index.html.twig` dosyasını açın. Zaten `title` bloğunu override ediyoruz, bunu `Parts!` olarak ayarlayalım:

```twig
// templates/part/index.html.twig
{% extends 'base.html.twig' %}

{% block title %}Parts!{% endblock %}
// ... lines 5 - 23
```

👉 Bu kod başlığı değiştirir.

## 🔄 Adding Some Substance: Looping Over Parts / İçerik Eklemek: Parçalar Üzerinde Döngü Kurmak

Parçaları döngüyle göstermek için, `PartController` içinde tüm parçaları sorgulamalıyız.

Bir `StarshipPartRepository` argümanı ekleyerek otomatik bağlamasını sağlayın. Ona istediğiniz ismi verebilirsiniz, örneğin `$leeroyJenkins` ya da... `$repository`. Tüm parçaları almak için: `$parts = $repository->findAll()` yeterli:

```php
// src/Controller/PartController.php
<?php

namespace App\Controller;

use App\Repository\StarshipPartRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PartController extends AbstractController
{
    #[Route('/parts', name: 'app_part_index')]
    public function index(StarshipPartRepository $repository): Response
    {
        $parts = $repository->findAll();

        return $this->render('part/index.html.twig', [
            'parts' => $parts,
        ]);
    }
}
```

👉 Burada, tüm parçaları alıyoruz ve şablona aktarıyoruz.

## 🖨️ Printing Parts in the Template / Parçaları Şablonda Yazdırmak

Artık `parts` değişkenimiz şablonda mevcut, bu yüzden döngü kurabiliriz. İşleri renklendirmek için, şu şablonu yapıştıracağım:

```twig
// templates/part/index.html.twig
{% extends 'base.html.twig' %}

{% block title %}Parts!{% endblock %}

{% block body %}
    <div class="space-y-5 mx-5">
        {% for part in parts %}
            <div class="bg-[#16202A] rounded-2xl p-5 flex flex-col min-[1174px]:flex-row min-[1174px]:justify-between items-center">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                         class="h-[83px] w-[84px] fill-current {{ cycle(['text-red-400', 'text-blue-400', 'text-green-400', 'text-purple-400', 'text-yellow-400'], loop.index0) }}"
                    ><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M308.5 135.3c7.1-6.3 9.9-16.2 6.2-25c-2.3-5.3-4.8-10.5-7.6-15.5L304 89.4c-3-5-6.3-9.9-9.8-14.6c-5.7-7.6-15.7-10.1-24.7-7.1l-28.2 9.3c-10.7-8.8-23-16-36.2-20.9L199 27.1c-1.9-9.3-9.1-16.7-18.5-17.8C173.9 8.4 167.2 8 160.4 8l-.7 0c-6.8 0-13.5 .4-20.1 1.2c-9.4 1.1-16.6 8.6-18.5 17.8L115 56.1c-13.3 5-25.5 12.1-36.2 20.9L50.5 67.8c-9-3-19-.5-24.7 7.1c-3.5 4.7-6.8 9.6-9.9 14.6l-3 5.3c-2.8 5-5.3 10.2-7.6 15.6c-3.7 8.7-.9 18.6 6.2 25l22.2 19.8C32.6 161.9 32 168.9 32 176s.6 14.1 1.7 20.9L11.5 216.7c-7.1 6.3-9.9 16.2-6.2 25c2.3 5.3 4.8 10.5 7.6 15.6l3 5.2c3 5.1 6.3 9.9 9.9 14.6c5.7 7.6 15.7 10.1 24.7 7.1l28.2-9.3c10.7 8.8 23 16 36.2 20.9l6.1 29.1c1.9 9.3 9.1 16.7 18.5 17.8c6.7 .8 13.5 1.2 20.4 1.2s13.7-.4 20.4-1.2c9.4-1.1 16.6-8.6 18.5-17.8l6.1-29.1c13.3-5 25.5-12.1 36.2-20.9l28.2 9.3c9 3 19 .5 24.7-7.1c3.5-4.7 6.8-9.5 9.8-14.6l3.1-5.4c2.8-5 5.3-10.2 7.6-15.5c3.7-8.7 .9-18.6-6.2-25l-22.2-19.8c1.1-6.8 1.7-13.8 1.7-20.9s-.6-14.1-1.7-20.9l22.2-19.8zM112 176a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zM504.7 500.5c6.3 7.1 16.2 9.9 25 6.2c5.3-2.3 10.5-4.8 15.5-7.6l5.4-3.1c5-3 9.9-6.3 14.6-9.8c7.6-5.7 10.1-15.7 7.1-24.7l-9.3-28.2c8.8-10.7 16-23 20.9-36.2l29.1-6.1c9.3-1.9 16.7-9.1 17.8-18.5c.8-6.7 1.2-13.5 1.2-20.4s-.4-13.7-1.2-20.4c-1.1-9.4-8.6-16.6-17.8-18.5L583.9 307c-5-13.3-12.1-25.5-20.9-36.2l9.3-28.2c3-9 .5-19-7.1-24.7c-4.7-3.5-9.6-6.8-14.6-9.9l-5.3-3c-5-2.8-10.2-5.3-15.6-7.6c-8.7-3.7-18.6-.9-25 6.2l-19.8 22.2c-6.8-1.1-13.8-1.7-20.9-1.7s-14.1 .6-20.9 1.7l-19.8-22.2c-6.3-7.1-16.2-9.9-25-6.2c-5.3 2.3-10.5 4.8-15.6 7.6l-5.2 3c-5.1 3-9.9 6.3-14.6 9.9c-7.6 5.7-10.1 15.7-7.1 24.7l9.3 28.2c-8.8 10.7-16 23-20.9 36.2L315.1 313c-9.3 1.9-16.7 9.1-17.8 18.5c-.8 6.7-1.2 13.5-1.2 20.4s.4 13.7 1.2 20.4c1.1 9.4 8.6 16.6 17.8 18.5l29.1 6.1c5 13.3 12.1 25.5 20.9 36.2l-9.3 28.2c-3 9-.5 19 7.1 24.7c4.7 3.5 9.5 6.8 14.6 9.8l5.4 3.1c5 2.8 10.2 5.3 15.5 7.6c8.7 3.7 18.6 .9 25-6.2l19.8-22.2c6.8 1.1 13.8 1.7 20.9 1.7s14.1-.6 20.9-1.7l19.8 22.2zM464 304a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"/></svg>
                    <div class="ml-5">
                        <h4 class="text-[22px] font-semibold">
                            <a class="hover:text-slate-200" href="#">
                                {{ part.name }} <span class="text-sm text-slate-400">(assigned to {{ part.starship.name }})</span>
                            </a>
                        </h4>
                        <div class="text-lg text-green-400 font-medium">✦{{ part.price }}</div>
                        <p class="text-slate-400 text-sm">{{ part.notes }}</p>
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
{% endblock %}
```

👉 Bu şablon, parçaları şık bir şekilde listeler ve her birini ayrı bir kart içinde gösterir.

Bu sadece güzel görünmesi için bir sürü şey. Bu sayfadaki kod bloğundan bu kodu alabilirsiniz.

Yenileyin ve... çok daha iyi!

## 🎨 A Little Trick: Using the Cycle Function / Küçük Bir İpucu: cycle() Fonksiyonunu Kullanmak

Burada kullandığım ilginç şeylerden biri `cycle()` fonksiyonu:

```twig
// templates/part/index.html.twig
// ... lines 1 - 5
{% block body %}
    <div class="space-y-5 mx-5">
        {% for part in parts %}
            <div class="bg-[#16202A] rounded-2xl p-5 flex flex-col min-[1174px]:flex-row min-[1174px]:justify-between items-center">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                         class="h-[83px] w-[84px] fill-current {{ cycle(['text-red-400', 'text-blue-400', 'text-green-400', 'text-purple-400', 'text-yellow-400'], loop.index0) }}"
// ... lines 13 - 23
                </div>
            </div>
        {% endfor %}
    </div>
{% endblock %}
```

👉 Bu fonksiyon, her dişliye rastgele bir renk vermek için kullanılır ve görünümü daha çekici hale getirir.

Her dişliye rastgele bir renk vermek istiyordum ki daha çekici görünsün. `cycle()` fonksiyonu bir dizi string almamızı sağlar, ardından `loop.index0` bunlar arasında döngü yapar. Küçük bir dokunuş, ama Ferengi'lerin sevdiği görkemliliği ekler.

## 🔗 Displaying the Related Starship / İlişkili Yıldız Gemisini Göstermek

Son olarak, `assigned to SHIP NAME` kısmını `{{ part.starship.name }}` ile değiştirin - bu sefer `ship.part` değil, ilişkinin diğer tarafını kullanıyoruz: `part.starship.name`:

```twig
// templates/part/index.html.twig
// ... lines 1 - 5
{% block body %}
    <div class="space-y-5 mx-5">
        {% for part in parts %}
            <div class="bg-[#16202A] rounded-2xl p-5 flex flex-col min-[1174px]:flex-row min-[1174px]:justify-between items-center">
                <div class="flex items-center">
// ... lines 10 - 12
                    <div class="ml-5">
                        <h4 class="text-[22px] font-semibold">
                            <a class="hover:text-slate-200" href="#">
                                {{ part.name }} <span class="text-sm text-slate-400">(assigned to {{ part.starship.name }})</span>
                            </a>
                        </h4>
                        <div class="text-lg text-green-400 font-medium">✦{{ part.price }}</div>
                        <p class="text-slate-400 text-sm">{{ part.notes }}</p>
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
{% endblock %}
```

👉 Bu kodda, parça ile ilişkilendirilmiş geminin adı gösteriliyor.

Ve... işte oldu!

## 🔍 Understanding the Relationship / İlişkiyi Anlamak

Bu örnekte önemli bir nokta var: İlişkinin **ters tarafını** kullanıyoruz. Daha önce `$ship->getParts()` ile geminin parçalarını alıyorduk. Şimdi ise `$part->getStarship()` ile parçanın hangi gemiye ait olduğunu alıyoruz.

**İlişki Yönleri:**

-   **Starship → Parts**: `$ship->getParts()` (OneToMany - bir geminin birçok parçası)
-   **Part → Starship**: `$part->getStarship()` (ManyToOne - birçok parçanın bir gemisi)

Bu iki yönlü ilişki, Doctrine'in en güçlü özelliklerinden biridir.

## 🎯 Template Features Explained / Şablon Özelliklerinin Açıklaması

Şablonumuzda kullandığımız özellikler:

### 1. **Responsive Design**

```twig
class="flex flex-col min-[1174px]:flex-row min-[1174px]:justify-between"
```

-   Küçük ekranlarda dikey düzen
-   Büyük ekranlarda yatay düzen

### 2. **Color Cycling**

```twig
{{ cycle(['text-red-400', 'text-blue-400', 'text-green-400', 'text-purple-400', 'text-yellow-400'], loop.index0) }}
```

-   Her parça için farklı renk
-   `loop.index0` ile döngü indeksi

### 3. **Price Display**

```twig
<div class="text-lg text-green-400 font-medium">✦{{ part.price }}</div>
```

-   Özel ✦ simgesi ile kredi gösterimi
-   Yeşil renk para için uygun

### 4. **Hover Effects**

```twig
<a class="hover:text-slate-200" href="#">
```

-   Fare üzerine gelince renk değişimi
-   Kullanıcı deneyimini iyileştirir

## 🚀 Performance Considerations / Performans Değerlendirmeleri

Şu anki implementasyonumuz `$repository->findAll()` kullanıyor. Bu basit ama potansiyel performans problemleri var:

**N+1 Problem Riski:**

-   Her `{{ part.starship.name }}` için ayrı sorgu
-   100 parça = 1 ana sorgu + 100 starship sorgusu

**Çözümler:**

1. **Eager Loading** (JOIN kullanarak)
2. **Pagination** (büyük listeler için)
3. **Caching** (sık kullanılan veriler için)

Bu konuları sonraki bölümlerde ele alacağız!

## 🔗 Navigation Links / Navigasyon Bağlantıları

⬅️ **Önceki:** [10*1* The Clever Criteria System.md](./10_1_%20The%20Clever%20Criteria%20System.md) - Akıllı Kriter Sistemi

➡️ **Sonraki:** 12_JOINs and Performance.md - JOIN'ler ve Performans

📚 **Ana Menü:** [README.md](../README.md) - Symfony Starshop Eğitim Serileri

## 🎉 Sonuç

Tebrikler! Parçaları listeleyen güzel bir sayfa oluşturduk. Bu sayfada:

-   MakerBundle ile hızlı controller oluşturma
-   Repository injection ile veri çekme
-   Twig şablonlarında döngü kullanma
-   Responsive tasarım ilkeleri
-   İlişkisel veri görüntüleme
-   Color cycling gibi UI tricks

Sırada JOIN işlemleri var. Bize katılın! Şaka bir yana, şimdi JOIN konusunu ele alacağız. 🚀

## 📋 Quick Reference / Hızlı Referans

**Controller Oluşturma:**

```bash
php bin/console make:controller PartController
```

**Repository Injection:**

```php
public function index(StarshipPartRepository $repository): Response
```

**Tüm Kayıtları Getirme:**

```php
$parts = $repository->findAll();
```

**Twig Döngüsü:**

```twig
{% for part in parts %}
    {{ part.name }}
{% endfor %}
```

**Renk Döngüsü:**

```twig
{{ cycle(['text-red-400', 'text-blue-400'], loop.index0) }}
```

**İlişkisel Veri:**

```twig
{{ part.starship.name }}
```
