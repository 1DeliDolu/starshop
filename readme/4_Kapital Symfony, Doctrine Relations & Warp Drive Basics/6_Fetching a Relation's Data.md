# 🔗 Fetching a Relation's Data / Bir İlişkinin Verisini Getirmek

Anasayfaya gidin ve `In Progress` durumundaki herhangi bir yıldız gemisine tıklayın.

Hey! Parçaları zaten listeliyoruz... bir bakıma... ama bunların hepsi sabit kodlanmış!

Şimdi, bu gemiyle ilişkili olan parçaları nasıl alırız?

Bu sayfanın denetleyicisini açın: `src/Controller/StarshipController.php`

## 🏗️ Querying for Related Parts Like any Other Property / İlişkili Parçaları Diğer Özellikler Gibi Sorgulama

Parçaları sorgulamak için genellikle `StarshipPartRepository`'yi autowire ederiz. Burada da aynı şekilde başlayın: `StarshipPartRepository $partRepository` argümanını ekleyin:


```php
// src/Controller/StarshipController.php
// ... lines 1 - 5
use App\Repository\StarshipPartRepository;
// ... lines 7 - 12
class StarshipController extends AbstractController
{
    #[Route('/starships/{slug}', name: 'app_starship_show')]
    public function show(
// ... lines 17 - 18
        StarshipPartRepository $partRepository,
    ): Response {
// ... lines 21 - 25
    }
}
```

👉 Bu kod, denetleyiciye `StarshipPartRepository`'yi ekler.

Sonraki adımda, `$parts` değişkenini `$partRepository->findBy()` ile ayarlayın:

Bu oldukça standart bir işlem: Bir özelliğin bir değere eşit olduğu kayıtları sorgulamak isterseniz, `findBy()` kullanıp özellik adını ve değeri iletin. İlişkiler söz konusu olduğunda da aynısı geçerli!

`$parts = $partRepository->findBy(['starship' => $ship])`

Ve hayır, burada herhangi bir şekilde `Starship ID` kullanmıyoruz. ID'leri bu işin dışında tutun! Bunun yerine, doğrudan `Starship` nesnesini iletin. İsterseniz id de iletebilirsiniz ama Doctrine, ilişkiler ve nesne mantığı açısından tüm `Starship` nesnesini iletmek en doğru yol.

Ne aldığımızı görmek için hata ayıklayalım: `dd($parts)`:


```php
// src/Controller/StarshipController.php
// ... lines 1 - 12
class StarshipController extends AbstractController
{
// ... line 15
    public function show(
// ... lines 17 - 18
        StarshipPartRepository $partRepository,
    ): Response {
        $parts = $partRepository->findBy(['starship' => $ship]);
        dd($parts);
// ... lines 23 - 25
    }
}
```

👉 Bu kod, ilgili parçaları bulur ve onları döker.

Sayfayı yenileyin ve işte! Bu yıldız gemisiyle ilişkili 10 adet `StarshipPart` nesnesinden oluşan bir dizi. Harika, değil mi? Öyleyse, sıkı durun.

## 🪄 Grabbing the Related Parts the Easy Way / İlişkili Parçaları Kolayca Almak

`$parts` değişkenini `$ship->getParts()` ile değiştirin:


```php
// src/Controller/StarshipController.php
// ... lines 1 - 12
class StarshipController extends AbstractController
{
// ... line 15
    public function show(
// ... lines 17 - 19
    ): Response {
// ... line 21
        dd($ship->getParts());
// ... lines 23 - 25
    }
}
```

👉 Bu kod, gemiye ait parçaları doğrudan getirir.

Yenileyin! Artık `StarshipPart` nesnelerinden oluşan bir dizi yerine, boş gibi görünen bir `PersistentCollection` nesnesi görürsünüz. `make:entity` komutunun, `Starship` yapıcısına eklediği `ArrayCollection`'ı hatırlayın mı? `PersistentCollection` ve `ArrayCollection`, aynı koleksiyon ailesindendir. Nesne olsalar da dizi gibi davranırlar. Güzel... ama neden bu koleksiyon boş görünüyor? Çünkü Doctrine akıllıdır: Parçalar sorgulanana kadar onları çekmez. `$ship->getParts()` üzerinden döngü yapıp `$part`'ı dökelim:


```php
// src/Controller/StarshipController.php
// ... lines 1 - 12
class StarshipController extends AbstractController
{
// ... line 15
    public function show(
// ... lines 17 - 19
    ): Response {
// ... lines 21 - 22
        foreach ($ship->getParts() as $part) {
            dump($part);
        }
// ... lines 26 - 29
    }
}
```

👉 Bu kod, koleksiyondaki tüm parçaları döker.

Bir anda o boş görünen koleksiyon, 10 adet `StarshipPart` nesnesiyle doluyor. Sihir gibi!

## ⏳ Lazy Relation Queries / Tembel (Lazy) İlişki Sorguları

Burada iki sorgu işliyor. Birincisi `Starship` için; ikincisi ise ona ait tüm `StarshipPart`'lar için. İlki, Symfony'nin slug'a göre `Starship` sorgulamasından geliyor. İkincisi ise daha ilginç: parçalar üzerinde döngü yaptığımız an gerçekleşiyor. Tam o anda Doctrine diyor ki:

"Şimdi hatırladım: Bu yıldız gemisi için elimde `StarshipParts` verisi yok. Hemen gidip alayım."

Bu harika değil mi? Doctrine için parti yapmak istiyorum.

## 🧹 Tidying Up and Looping Over Parts / Temizlik ve Parçalar Üzerinde Döngü

Parçalar değişkenini tamamen kaldırın... ve `StarshipPartRepository`'yi de kaldırın: bu gereğinden fazla işti. Bunun yerine, `parts` değişkenini `$ship->getParts()` olarak ayarlayın:


```php
// src/Controller/StarshipController.php
// ... lines 1 - 12
class StarshipController extends AbstractController
{
    #[Route('/starships/{slug}', name: 'app_starship_show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Starship $ship,
    ): Response {
        return $this->render('starship/show.html.twig', [
            'ship' => $ship,
            'parts' => $ship->getParts(),
        ]);
    }
}
```

👉 Bu kod, şablona gemiyi ve parçalarını gönderir.

Artık yepyeni `parts` değişkenimiz olduğuna göre, şablonda bunun üzerinde döngü yapalım. `templates/starship/show.html.twig` dosyasını açın ve sabit kodlanmış bölümü şu döngüyle değiştirin: for part in parts, part.name, part.price, part.notes, endfor:


```twig
// templates/starship/show\.html.twig
// ... lines 1 - 4
{% block body %}
// ... lines 6 - 19
    <div class="md:flex justify-center space-x-3 mt-5 px-4 lg:px-8">
// ... lines 21 - 25
        <div class="space-y-5">
            <div class="mt-8 max-w-xl mx-auto">
                <div class="px-8 pt-8">
// ... lines 29 - 61
                    <ul class="text-sm font-medium text-slate-400 tracking-wide">
                        {% for part in parts %}
                            <li class="border-b border-slate-600 py-2">
                                <span class="block text-white font-semibold">
                                    {{ part.name }} (✦ {{ part.price }})
                                </span>
                                <span class="text-xs text-slate-500 italic">
                                    {{ part.notes }}
                                </span>
                            </li>
                        {% endfor %}
                    </ul>
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```

👉 Bu şablon, parçaları listeler.

## 💤 Still too much Work? / Hâlâ Fazla İş mi?

Ve başardık! `parts` değişkeni sayesinde tüm ilişkili parçaları ciddi bir iş yükü olmadan listeledik.

Ama biliyor musunuz? Bu bile fazla iş! `parts` değişkenini tamamen kaldırın:


```php
// src/Controller/StarshipController.php
// ... lines 1 - 12
class StarshipController extends AbstractController
{
// ... line 15
    public function show(
// ... lines 17 - 18
    ): Response {
        return $this->render('starship/show.html.twig', [
            'ship' => $ship,
        ]);
    }
}
```

👉 Şablona sadece `ship` değişkenini gönderir.

Şablonda ise:

templates/starship/show\.html.twig

```twig
// ... lines 1 - 4
{% block body %}
// ... lines 6 - 19
    <div class="md:flex justify-center space-x-3 mt-5 px-4 lg:px-8">
// ... lines 21 - 25
        <div class="space-y-5">
            <div class="mt-8 max-w-xl mx-auto">
                <div class="px-8 pt-8">
// ... lines 29 - 61
                    <ul class="text-sm font-medium text-slate-400 tracking-wide">
                        {% for part in ship.parts %}
// ... lines 64 - 71
                        {% endfor %}
                    </ul>
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```

👉 Parçalar doğrudan `ship.parts` ile döngüye alınır.

Ve... hâlâ çalışıyor! Keyif için, bu geminin parça sayısını da gösterelim: `ship.parts|length`


```twig
// templates/starship/show\.html.twig
// ... lines 1 - 4
{% block body %}
// ... lines 6 - 19
    <div class="md:flex justify-center space-x-3 mt-5 px-4 lg:px-8">
// ... lines 21 - 25
        <div class="space-y-5">
            <div class="mt-8 max-w-xl mx-auto">
                <div class="px-8 pt-8">
// ... lines 29 - 58
                    <h4 class="text-xs text-slate-300 font-semibold mt-2 uppercase">
                        Parts ({{ ship.parts|length }})
                    </h4>
// ... lines 62 - 73
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```

👉 Bu satır, parçaların sayısını gösterir.

İki sorgumuz hâlâ var, ancak Doctrine yine akıllı: Tüm `StarshipPart`'ları sorguladığımızı bildiği için, sayıyı hesaplarken ekstra bir sorguya gerek duymaz.

Sıradaki: Doctrine ilişkilerinde sıkça yanlış anlaşılan bir konu olan "sahip olan (owning)" ve "ters (inverse)" tarafı konuşacağız.
