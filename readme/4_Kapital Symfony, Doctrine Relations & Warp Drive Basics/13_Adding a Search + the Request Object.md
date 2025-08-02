## 🔎 Adding a Search + the Request Object / Arama Ekleme + Request Nesnesi

Doctrine İlişkilerinden kısa ama faydalı bir şekilde ayrılıyoruz. Doctrine ilişkileri harika, ama bu özellik de öyle olacak! Sayfamıza bir arama çubuğu eklemek istiyorum. Bana güvenin, bu güzel olacak.

`index.html.twig` şablonunu açın. Sayfanın en üstüne bir arama girişi ekleyeceğim:

```twig
// templates/part/index.html.twig
// ... lines 1 - 4
{% block body %}
    <div class="flex justify-end mt-6 mb-6">
        <div class="relative w-full max-w-md">
            <input type="text"
                   placeholder="Search..."
                   class="w-full p-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
            <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A8.5 8.5 0 1011 19.5a8.5 8.5 0 005.65-2.85z" />
            </svg>
        </div>
    </div>
// ... lines 18 - 38
{% endblock %}
```

👉 Bu kod parçası, üst kısma bir arama kutusu ekler.

Burada özel bir şey yok: sadece bir `<input type="text" placeholder="Search..."`, ardından birkaç sınıf ve SVG ile görsel olarak hoş bir görünüm elde edilmiş.

Bu kutunun gönderebilmesi için, onu bir `form` etiketiyle sarmalayın. `action` olarak yine bu sayfaya göndermesini sağlayın: `{{ path('app_part_index') }}`. Ayrıca `name="query"` ve `method="get"` ekleyin:

```twig
// ... lines 1 - 4
// templates/part/index.html.twig
{% block body %}
    <div class="flex justify-end mt-6 mb-6">
        <div class="relative w-full max-w-md">
            <form method="get" action="{{ path('app_part_index') }}">
                <input type="text"
                       placeholder="Search..."
                       name="query"
                       class="w-full p-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A8.5 8.5 0 1011 19.5a8.5 8.5 0 005.65-2.85z" />
                </svg>
            </form>
        </div>
    </div>
// ... lines 21 - 41
{% endblock %}
```

👉 Bu kodda, form gönderildiğinde arama sorgusu URL'ye query parametresi olarak eklenir.

## 📥 Getting the Request / Request Nesnesini Alma

Şimdi `PartController` dosyasına gidin. URL'deki `query` isimli parametreyi nasıl okuyacağız? Bu, istekten (request) gelen bir bilgidir, tıpkı başlıklar veya POST verisi gibi. Symfony tüm bu verileri bir `Request` nesnesinde toplar. Bunu nasıl alırız? Kontrolcüde bunu almak çok kolay. Kontrolcü metoduna bir `Request` parametresi ekleyin.

Daha önce servisleri böyle otomatik olarak (autowire) alabildiğinizi hatırlıyorsunuzdur. `Request` nesnesi teknik olarak bir servis değildir ama Symfony bunu yine de otomatik olarak sağlar. Bunu `Symfony\Component\HttpFoundation\Request` içinden alın. İsmini istediğiniz gibi verebilirsiniz, ama tutarlı olmak için `$request` diyelim:

```php
// src/Controller/PartController.php
// ... lines 1 - 6
use Symfony\Component\HttpFoundation\Request;
// ... lines 8 - 10
final class PartController extends AbstractController
{
    #[Route('/parts', name: 'app_part_index')]
    public function index(StarshipPartRepository $repository, Request $request,): Response
    {
// ... lines 16 - 23
    }
}
```

👉 Bu kodda, `index` metoduna `Request` nesnesi parametre olarak eklenmiştir.

`$query = $request->query->get('query')` satırını ekleyin: ilk `query` sorgu parametrelerini, ikinci `query` ise input alanının adını temsil eder. Bunun çalıştığından emin olmak için `dd($query);` kullanın:

```php
// src/Controller/PartController.php
// ... lines 1 - 10
final class PartController extends AbstractController
{
    #[Route('/parts', name: 'app_part_index')]
    public function index(StarshipPartRepository $repository, Request $request,): Response
    {
        $query = $request->query->get('query');
        dd($query);
// ... lines 18 - 23
    }
}
```

👉 Bu kod, gelen `query` parametresini ekrana döker.

Deneyin; "holodeck" gibi bir değerin geldiğini göreceksiniz.

## 🚀 Enhancing the Search / Aramayı Geliştirme

Şimdi `findAllOrderedByPrice()` metodunu arama yapacak şekilde geliştirelim. `dd($query);` satırını kaldırın ve bunu metoda parametre olarak iletin:

```php
// src/Controller/PartController.php
// ... lines 1 - 10
final class PartController extends AbstractController
{
    #[Route('/parts', name: 'app_part_index')]
    public function index(StarshipPartRepository $repository, Request $request,): Response
    {
        $query = $request->query->get('query');
        $parts = $repository->findAllOrderedByPrice($query);
// ... lines 19 - 22
    }
}
```

👉 Bu kodda, arama sorgusu repository metoduna parametre olarak iletiliyor.

Bunu birkaç satıra bölün ve bir if bloğu ekleyin. Ayrıca return'u `$qb = $this->createQueryBuilder('sp')` olarak değiştirin ve `getQuery()` ile `getResult()` kısımlarını kaldırın; şimdilik sadece QueryBuilder lazım.

Şimdi sihir zamanı. Eğer bir arama varsa, Starship parça adının küçük harfe çevrilmiş halinin aramayla eşleşip eşleşmediğini kontrol eden bir `andWhere()` ekleyin. PostgreSQL büyük/küçük harfe duyarlı olduğu için böyle yazıyoruz.

Sonunda, sorgunun sonucunu döndürün:

```php
// src/Repository/StarshipPartRepository.php
// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 40
    public function findAllOrderedByPrice(string $search = ''): array
    {
        $qb = $this->createQueryBuilder('sp')
            ->orderBy('sp.price', 'DESC')
            ->innerJoin('sp.starship', 's')
            ->addSelect('s')
        ;
        if ($search) {
            $qb->andWhere('LOWER(sp.name) LIKE :search')
                ->setParameter('search', '%'.strtolower($search).'%');
        }
        return $qb->getQuery()
            ->getResult();
    }
}
```

👉 Bu kodda, arama kelimesi girildiğinde hem `name` alanı üzerinden filtreleme yapılır.

## 💾 Preserving the Search Value / Arama Değerini Korumak

Arama yaptıktan sonra, arama kutusunda yazdığımız değerin kaybolduğunu görebilirsiniz. Bunu düzeltmek için şablona `value="{{ app.request.query.get('query') }}"` ekleyin. Evet, bu `Request` nesnesi şablonlarda `app.request` ile zaten mevcut:

```twig
// templates/part/index.html.twig
// ... lines 1 - 4
{% block body %}
    <div class="flex justify-end mt-6 mb-6">
        <div class="relative w-full max-w-md">
            <form method="get" action="{{ path('app_part_index') }}">
                <input type="text"
// ... lines 11 - 12
                       value="{{ app.request.query.get('query') }}"
// ... line 14
                >
// ... lines 16 - 18
            </form>
        </div>
    </div>
// ... lines 22 - 42
{% endblock %}
```

👉 Bu kodda, arama kutusunun değeri arama sorgusuyla otomatik olarak doldurulur.

## 🧩 Searching on Multiple Fields / Birden Fazla Alanda Arama

Ayrıca parça notlarında (`notes`) da arama yapmak istemez miyiz? Örneğin, "controls" diye arayın. Şu an bir sonuç yok. Hem ad hem de notlar alanında arama yapmak istiyoruz.

`OR` mantığına ihtiyacımız var. Repository'ye dönüp, `andWhere()` ifadesine bir `OR` ekleyin:

```php
// src/Repository/StarshipPartRepository.php
// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 40
    public function findAllOrderedByPrice(string $search = ''): array
    {
// ... lines 43 - 48
        if ($search) {
            $qb->andWhere('LOWER(sp.name) LIKE :search OR LOWER(sp.notes) LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }
// ... lines 53 - 55
    }
}
```

👉 Bu kodda, arama hem `name` hem de `notes` alanlarında yapılır.

`orWhere()` kullanmak isteyebilirsiniz ama bu bir tuzak! Mantıksal parantezlerin nerede olacağını garanti edemezsiniz. Bunun yerine `andWhere()` ile `OR`'u doğrudan içinde yazarak tam kontrol elde edersiniz.

Artık arama hem notlarda hem de isimde çalışıyor. Sonuç olarak, `orWhere()` yerine `andWhere()` içinde `OR` kullanarak mantıksal kontrolü elinizde tutabilirsiniz.

Araya bu güzel deturu ekledik, şimdi son ilişki tipimiz olan many to many ilişkisine geçebiliriz.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./12_Joining to Avoid the N+1 Trap.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./14_Droid Entity for the ManyToMany Relationship.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
