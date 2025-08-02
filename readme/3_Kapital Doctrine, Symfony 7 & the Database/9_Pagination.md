# 📄 Pagination / Sayfalama

Foundry bize 20 uzay gemisi eklememize yardımcı oldu. Bu, uygulamamızı daha gerçekçi gösterdi. Ancak üretim ortamında binlerce uzay gemimiz olabilir. Bu sayfa devasa ve kullanılmaz hale gelir. Muhtemelen yüklenmesi de çok uzun sürer ve bu sürede asimile edilebiliriz!

Çözüm? Sonuçları sayfalara bölmek: her seferinde - veya her sayfada - birkaçını gösterin.

## 📦 Install Pagerfanta / Pagerfanta Kurulumu

Bunu yapmak için `Pagerfanta` adlı bir kütüphane kullanacağız - ne havalı bir isim! Bu, genel amaçlı bir sayfalama kütüphanesidir ancak `Doctrine` ile mükemmel bir entegrasyona sahiptir! Gerekli iki paketi ekleyin:

```bash
composer require babdev/pagerfanta-bundle pagerfanta/doctrine-orm-adapter
```

👉 Bu komut, `Pagerfanta` ve `Doctrine` arasında bağ kuran `pagerfanta/doctrine-orm-adapter` paketini de yükler.

## 🔍 Paginate a Query / Bir Sorguyu Sayfalama

Ana sayfamızda `StarshipRepository` içindeki `findIncomplete()` metodunu kullanıyoruz. Bu metodu açın ve dönüş türünü `Pagerfanta` olarak değiştirin: bu, sayfalama ile ilgili süper güçlere sahip bir nesnedir. Ancak bu nesne üzerinde bir dizi gibi döngü kurabilirsiniz, bu yüzden docblock'u olduğu gibi bırakın:

```php
//src/Repository/StarshipRepository.php

// ... lines 1 - 14
class StarshipRepository extends ServiceEntityRepository
{
// ... lines 17 - 21
    /**
     * @return Starship[]
     */
    public function findIncomplete(): Pagerfanta
    {
// ... lines 27 - 34
    }
// ... lines 36 - 65
}
```

👉 Bu metot artık `Pagerfanta` nesnesi döndürüyor.

Sorguyu sayfalarken dikkat edilmesi gereken çok önemli bir şey var: öngörülebilir bir sıralama yapmanız gerekir. `->orderBy('s.arrivedAt', 'DESC')` satırını ekleyin:

```php
// src/Repository/StarshipRepository.php

// ... lines 1 - 14
class StarshipRepository extends ServiceEntityRepository
{
// ... lines 17 - 24
    public function findIncomplete(): Pagerfanta
// ... lines 26 - 28
            ->orderBy('s.arrivedAt', 'DESC')
// ... lines 30 - 34
    }
// ... lines 36 - 65
}
```

👉 Bu satır, sorgu sonuçlarını `arrivedAt` alanına göre azalan sırada getirir.

Ancak doğrudan döndürmek yerine bunu `$query` adlı bir değişkene atayın, ardından `getResult()` çağrısını kaldırın: artık amacımız sorguyu çalıştırmak değil, sadece oluşturmak. Gerçek çalıştırmayı `Pagerfanta` yapacak. `return new Pagerfanta(new QueryAdapter($query))` şeklinde döndürün ve bu iki sınıfı içe aktardığınızdan emin olun:

```php
// src/Repository/StarshipRepository.php

// ... lines 1 - 14
class StarshipRepository extends ServiceEntityRepository
{
// ... lines 17 - 24
    public function findIncomplete(): Pagerfanta
// ... line 26
        $query = $this->createQueryBuilder('s')
// ... lines 28 - 30
            ->getQuery()
        ;
// ... line 33
        return new Pagerfanta(new QueryAdapter($query));
    }
// ... lines 36 - 65
}
```

👉 `QueryBuilder` ile oluşturulan sorgu artık `Pagerfanta` adaptörü ile döndürülür.

## ⚙️ Configure the Page / Sayfayı Yapılandırma

`MainController` içinde, `$ships` artık bir `Pagerfanta` nesnesidir. Bunu kullanmak için iki şeyi belirtmemiz gerekir: her sayfada kaç gemi gösterileceği - `$ships->setMaxPerPage(5)` - ve kullanıcının şu an hangi sayfada olduğu: şimdilik `$ships->setCurrentPage(1)` kullanın. Ve `setCurrentPage()` metodunu **her zaman** `setMaxPerPage()`'den sonra çağırın, yoksa zaman yolculuğu gibi tuhaf şeyler olur:

```php
// src/Controller/MainController.php

// ... lines 1 - 12
    public function homepage(
// ... line 14
    ): Response {
        $ships = $repository->findIncomplete();
        $ships->setMaxPerPage(5);
        $ships->setCurrentPage(1);
// ... lines 19 - 25
    }
}
```

👉 Bu kod her sayfada 5 gemi gösterir ve ilk sayfayı görüntüler.

Şimdi `setCurrentPage(2)` olarak değiştirin:

```php
// src/Controller/MainController.php

// ... lines 1 - 12
    public function homepage(
// ... line 14
    ): Response {
// ... lines 16 - 17
        $ships->setCurrentPage(2);
// ... lines 19 - 25
    }
}
```

👉 Bu, ikinci sayfayı görüntüler.

Hâlâ 5 gemi ama farklıları: bu ikinci sayfa. Sorguya bakalım. Birden fazla sorgu çalışıyor! Biri toplam sonuç sayısını sayıyor, diğeri ise sadece bu sayfaya ait verileri getiriyor. Gerçekten harika.

Sayfa numarasını 1 veya 2 olarak sabitlemek yerine, bunu URL’den dinamik olarak okuyalım, örneğin `?page=1` veya `?page=2` şeklinde.

## 🌐 Current Page from Request / Geçerli Sayfayı İstekten Almak

Bunu yapmak için, `HttpFoundation` sınıfından `Request $request` parametresini metodumuza ekleyin ve `setCurrentPage()` metodundaki değeri `$request->query->get('page', 1)` olarak değiştirin: bu değer URL'den okunur ve yoksa varsayılan olarak 1 olur:

```php
// src/Controller/MainController.php

// ... lines 1 - 10
class MainController extends AbstractController
{
// ... line 13
    public function homepage(
// ... line 15
        Request $request,
    ): Response {
// ... lines 18 - 19
        $ships->setCurrentPage($request->query->get('page', 1));
// ... lines 21 - 27
    }
}
```

👉 Bu, sayfa numarasını URL'deki `page` parametresinden alır.

## 🧮 Display Pagination Info / Sayfalama Bilgisini Gösterme

Şimdi `homepage.html.twig` dosyasını açın.

Bu bilgileri `<h1>` etiketinin altına yerleştirin. Alt kenar boşluğunu değiştirin ve yeni bir `<div>` (biraz stil ile) ekleyin. İçerisine şu şekilde yazın: `{{ ships.nbResults }}`. Ardından: `Page {{ ships.currentPage }} of {{ ships.nbPages }}`:

```twig
//templates/main/homepage.html.twig

// ... lines 1 - 4
{% block body %}
    <main class="flex flex-col lg:flex-row">
// ... lines 7 - 8
        <div class="px-12 pt-10 w-full">
            <h1 class="text-4xl font-semibold mb-3">
// ... line 11
            </h1>
// ... line 13
            <div class="text-slate-400 mb-4">
                {{ ships.nbResults }} ships (Page {{ ships.currentPage }} of {{ ships.nbPages }})
            </div>
// ... lines 17 - 57
        </div>
    </main>
{% endblock %}
```

👉 Bu blok, toplam gemi sayısını, geçerli sayfa numarasını ve toplam sayfa sayısını gösterir.

## 🔗 Pagination Links / Sayfalama Bağlantıları

Şimdi sayfalar arasında gezinmek için bağlantılar ekleyelim. Liste altına aşağıdaki kodu yapıştırın. İlk olarak `if ships.haveToPaginate` kontrolü: eğer sadece bir sayfa varsa bağlantı gerekmez. Sonra `if ships.hasPreviousPage`, eğer önceki sayfa varsa bir bağlantı oluşturur. İçeride, bu sayfaya bir URL üretin: `app_homepage`, ancak `page` parametresi olarak `ships.getPreviousPage` geçin. Bu rota tanımında `page` belirtilmediği için bir `query parameter` olarak eklenecek. Aynı şekilde `Next` bağlantısını da tekrar edin:

```twig
// templates/main/homepage.html.twig

// ... lines 1 - 4
{% block body %}
    <main class="flex flex-col lg:flex-row">
// ... lines 7 - 8
        <div class="px-12 pt-10 w-full">
// ... lines 10 - 51
            </div>
// ... line 53
            {% if ships.haveToPaginate %}
                <div class="flex justify-around mt-3 underline font-semibold">
                    {% if ships.hasPreviousPage %}
                        <a href="{{ path('app_homepage', {page: ships.getPreviousPage}) }}">&lt; Previous</a>
                    {% endif %}
                    {% if ships.hasNextPage %}
                        <a href="{{ path('app_homepage', {page: ships.getNextPage}) }}">Next &gt;</a>
                    {% endif %}
                </div>
            {% endif %}
// ... lines 64 - 68
        </div>
    </main>
{% endblock %}
```

👉 Bu kod, geçerli sayfaya göre "Previous" ve "Next" bağlantılarını gösterir.

## ➕ Devamı

Bağlantıları elle oluşturduk, bu da bize sınırsız özelleştirme gücü sağlıyor. Ancak `Pagerfanta`, bu bağlantıları bizim yerimize oluşturabilir. Nasıl yapılacağını görmek için `Pagerfanta` belgelerine göz atabilirsiniz. Dezavantajı, HTML'yi özelleştirmenin biraz daha zor olmasıdır.

Sırada ne var? `Starship` varlığına daha fazla alan ekleyelim. En güzel kısmı mı? Bu sütunu veritabanına eklemenin ne kadar kolay olduğunu görmek. Haydi yapalım!

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./8_Alien Tech for Fixtures Foundry & Faker.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./10_ Starship Upgrade Adding Slug and Timestamp Fields.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
