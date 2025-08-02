# 🛠️ Listing Parts / Parçaları Listeleme

Yeni görev: Her mevcut parçayı listeleyen bir sayfaya ihtiyacımız var. `Ferengi` satış ekibimiz bu sayfayı klasik çapraz satış için kullanacak. Bildiğiniz gibi:

Hey, bir uzay gemisi aldınız, parlak yeni `dilithium crystal organizer` ya da `cupholder stabilizer` ister misiniz?

Bize hızlı bir başlangıç sağlamak için `MakerBundle` kullanalım. Terminalinizi açın ve şunu çalıştırın:

```bash
symfony console make:controller
```

👉 Bu komut, yeni bir `controller` oluşturur.

Adını... tahmin edin... `PartController` koyun. Odaklanmak için test eklemeyin.

Ve işte! Bir sınıf ve bir şablon dosyası. Şimdiye kadar her şey yolunda. Yeni `PartController` dosyasına bir göz atın:

---

```php
// src/Controller/PartController.php
// ... lines 1 - 2
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

---

URL'yi `/parts` olarak değiştirin ve adını `app_part_index` yapın:

```php
// src/Controller/PartController.php
// ... lines 1 - 8
final class PartController extends AbstractController
{
    #[Route('/parts', name: 'app_part_index')]
    public function index(): Response
// ... lines 13 - 16
    }
}
```

👉 Bu değişiklik, rotanın yolunu ve adını günceller.

---

Rota adını kopyalayın, böylece bağlantı verebiliriz... ve `base.html.twig` dosyasını açın.

## 🔗 Linking to the Parts Page / Parçalar Sayfasına Bağlantı Verme

O boş duran "About" bağlantısını hatırlıyor musunuz? Onu kullanın ve "Parts" bağlantısına çevirin. `href` değerini `{{ path('app_part_index') }}` olarak ayarlayın:

```html
// templates/base.html.twig // ... line 1
<html>
    // ... lines 3 - 13
    <body
        class="text-white"
        style="background: radial-gradient(102.21% 102.21% at 50% 28.75%, #00121C 42.62%, #013954 100%);"
    >
        <div class="flex flex-col justify-between min-h-screen relative">
            <div>
                <header
                    class="h-[114px] shrink-0 flex flex-col sm:flex-row items-center sm:justify-between py-4 sm:py-0 px-6 border-b border-white/20 shadow-md"
                >
                    // ... lines 18 - 20
                    <nav class="flex space-x-4 font-semibold">
                        // ... lines 22 - 24
                        <a
                            class="hover:text-amber-400  pt-2"
                            href="{{ path('app_part_index') }}"
                        >
                            Parts
                        </a>
                        // ... lines 28 - 33
                    </nav>
                </header>
                // ... line 36
            </div>
            // ... lines 38 - 40
        </div>
    </body>
</html>
```

👉 Bu kodda, navigasyon çubuğundaki bağlantı artık "Parts" sayfasına yönlendiriyor.

---

Ana sayfaya gidin, yeni bağlantıya tıklayın ve... henüz çok güzel görünmese de, çalışıyor!

Kutlamadan önce, başlığı sıkıcı `Hello PartController` ifadesinden değiştirelim. `templates/part/index.html.twig` dosyasını açın. Zaten `title` bloğunu override ediyoruz, bunu `Parts` olarak ayarlayalım:

```twig
// templates/part/index.html.twig
// ... lines 1 - 2
{% block title %}Parts!{% endblock %}
// ... lines 4 - 21
```

👉 Bu kod başlığı değiştirir.

---

## 🔄 Adding Some Substance: Looping Over Parts / İçerik Eklemek: Parçalar Üzerinde Döngü Kurmak

Parçaları döngüyle göstermek için, `PartController` içinde tüm parçaları sorgulamalıyız.

Bir `StarshipPartRepository` argümanı ekleyerek otomatik bağlamasını sağlayın. Ona istediğiniz ismi verebilirsiniz, örneğin `$repository`. Tüm parçaları almak için: `$parts = $repository->findAll()` yeterli:

```php
// src/Controller/PartController.php
// ... lines 1 - 4
use App\Repository\StarshipPartRepository;
// ... lines 6 - 9
final class PartController extends AbstractController
{
    #[Route('/parts', name: 'app_part_index')]
    public function index(StarshipPartRepository $repository): Response
    {
        $parts = $repository->findAll();
// ... lines 16 - 19
    }
}
```

👉 Burada, tüm parçaları alıyoruz.

---

## 🖨️ Printing Parts in the Template / Parçaları Şablonda Yazdırmak

Artık `parts` değişkenimiz şablonda mevcut, bu yüzden döngü kurabiliriz:

```php
// src/Controller/PartController.php
// ... lines 1 - 9
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

👉 Burada, `parts` verisini şablona aktarıyoruz.

---

Ve işte şablon kodu:

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
                         class="h-[83px] w-[84px] fill-current {{ cycle(['text-red-400', 'text-blue-400', 'text-green-400', 'text-purple-400', 'text-yellow-400'], loop.index0) }}"/>
                    <div class="ml-5">
                        <h4 class="text-[22px] font-semibold">
                            <a class="hover:text-slate-200" href="#">
                                {{ part.name }} <span class="text-sm text-slate-400">(assigned to SHIP NAME)</span>
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

👉 Bu şablon, parçaları şık bir şekilde listeler.

---

## 🔄 A Little Trick: Using the Cycle Function / Küçük Bir İpucu: cycle() Fonksiyonunu Kullanmak

Burada dikkat çekenlerden biri, `cycle()` fonksiyonu:

```twig
// templates/part/index.html.twig
// ... lines 1 - 4
{% block body %}
    <div class="space-y-5 mx-5">
        {% for part in parts %}
            <div class="bg-[#16202A] rounded-2xl p-5 flex flex-col min-[1174px]:flex-row min-[1174px]:justify-between items-center">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                         class="h-[83px] w-[84px] fill-current {{ cycle(['text-red-400', 'text-blue-400', 'text-green-400', 'text-purple-400', 'text-yellow-400'], loop.index0) }}"/>
// ... lines 12 - 21
                </div>
            </div>
        {% endfor %}
    </div>
{% endblock %}
```

👉 Bu fonksiyon, listedeki her svg'ye farklı bir renk uygular.

---

Son olarak, `assigned to SHIP NAME` kısmını değiştirin ve `{{ part.starship.name }}` olarak yazın:

```twig
// templates/part/index.html.twig
// ... lines 1 - 4
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
// ... lines 19 - 20
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
{% endblock %}
```

👉 Bu kodda, parça ile ilişkilendirilmiş geminin adı gösteriliyor.

---

Sırada join işlemleri var. Bize katılın! Şaka bir yana, şimdi join konusunu ele alacağız.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./10_The Clever Criteria System.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./12_Joining to Avoid the N+1 Trap.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
