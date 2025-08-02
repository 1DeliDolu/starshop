# 🚀 Cosmic Queries: the Repository Class / Kozmik Sorgular: Repository Sınıfı

Artık `Entity Repository`'lerinden bahsetmenin zamanı geldi – yani bir varlık (entity) için özel sorguların "bağlandığı" yer.

`homepage` kontrolcüsünde, tüm gemileri bulmak için bir sorgu yazdık. Bu işe yarar, ancak aynı sorguya başka bir yerde de ihtiyaç duyarsak, bunu tekrar etmek zorunda kalırız. Ve eğer sorguda bir değişiklik yapmak istersek, bunu birden fazla yerde yapmak zorunda kalırız. Bu oldukça kötü!

## 🛰️ Fetching The Repository Service / Repository Servisini Almak

`Entity Repository`'leri yardımımıza koşuyor! Bekleyin, `make:entity` zaten böyle bir şey oluşturmuş muydu? Evet, oluşturmuştu! Bir varlığın repository nesnesini almak için şu komutu kullanabilirsiniz: `dd($em->getRepository(Starship::class))`:


```php
// src/Controller/MainController.php
// ... lines 1 - 10
class MainController extends AbstractController
{
// ... line 13
    public function homepage(
        EntityManagerInterface $em,
    ): Response {
        dd($em->getRepository(Starship::class));
// ... lines 18 - 29
    }
}
```

👉 Bu kod, `Starship` varlığına ait repository sınıfını (`StarshipRepository`) gösterir.

Uygulamaya dönüp sayfayı yenileyin. Harika! Bir `App\Repository\StarshipRepository` nesnesi elde ettik. Bu sınıfa göz atın: `src/Repository/StarshipRepository.php`.

İlk olarak, Doctrine’in bu sınıfın `Starship` varlığına ait bir repository olduğunu nasıl bildiğini merak ediyorsanız, `src/Entity/Starship.php` dosyasına bakın. Orada `#[ORM\Entity]` özniteliğinde `repositoryClass: StarshipRepository::class` tanımı bulunur:

```php
// src/Entity/Starship.php
// ... lines 1 - 7
#[ORM\Entity(repositoryClass: StarshipRepository::class)]
class Starship
// ... lines 10 - 110
```

👉 Bu tanım, `Starship` varlığının `StarshipRepository` ile ilişkili olduğunu belirtir.

Her varlığın – örneğin `Starship` – kendine ait bir repository sınıfı vardır. Bu sınıf başlangıçta boştur, ama yakında özel sorgularla dolduracağız. Ayrıca bu sınıf bir servistir! Bu da otomatik olarak bağımlılık enjeksiyonu ile kullanılabileceği anlamına gelir.

`homepage` kontrolcüsünde, bu `dd()` satırını kaldırın. Şimdi `EntityManagerInterface` yerine doğrudan `StarshipRepository $repository` kullanalım:


```php
// src/Controller/MainController.php
// ... lines 1 - 9
class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(
        StarshipRepository $repository,
    ): Response {
// ... lines 16 - 22
    }
}
```


👉 Bu kod, `homepage` metoduna `StarshipRepository` servisini otomatik olarak enjekte eder.

Daha önce yazdığımız, tüm gemileri getiren bu sorgu o kadar yaygın ki, her repository sınıfında bunun için bir kısayol vardır: `findAll()`:


```php
// src/Controller/MainController.php
// ... lines 1 - 9
class MainController extends AbstractController
{
// ... line 12
    public function homepage(
        StarshipRepository $repository,
    ): Response {
        $ships = $repository->findAll();
// ... lines 17 - 22
    }
}
```

👉 Bu kod, tüm `Starship` varlıklarını döndürür.

Uygulamayı yenileyin. Hâlâ çalışıyor!

Aynı işlemi `StarshipController::show()` içinde de yapalım. `EntityManagerInterface` yerine `StarshipRepository $repository` kullanalım:


```php
// src/Controller/StarshipController.php
// ... lines 1 - 9
class StarshipController extends AbstractController
{
    #[Route('/starships/{id<\d+>}', name: 'app_starship_show')]
    public function show(int $id, StarshipRepository $repository): Response
    {
// ... lines 15 - 22
    }
}
```

👉 Bu kod, `show()` metoduna `StarshipRepository` servisini enjekte eder.

Her repository sınıfı ayrıca önceden tanımlanmış bir `find()` metoduna sahiptir! Ve bu bir `StarshipRepository` olduğu için, entity sınıfını belirtmemize gerek yok – sadece `$id` yeterlidir:


```php
// src/Controller/StarshipController.php
// ... lines 1 - 9
class StarshipController extends AbstractController
// ... lines 11 - 12
    public function show(int $id, StarshipRepository $repository): Response
    {
        $ship = $repository->find($id);
// ... lines 16 - 22
    }
}
```

👉 Bu kod, ilgili `id` ile bir gemi bulur.

## 🧩 Custom Queries in the Repository / Repository İçinde Özel Sorgular

`homepage` kontrolcüsüne geri dönelim. Tüm gemileri bulmak yerine, yalnızca durumu `completed` olmayan gemileri bulmak istesek ne olurdu? Yani sadece `waiting` veya `in progress` olanları. Özel bir sorguya ihtiyacımız var! Ama bu kez sorguyu kontrolcüye değil, repository sınıfına yazalım.

Yeni bir `public function findIncomplete()` metodu ekleyin. Bu metod bir dizi dönecek ve bir docblock içerecek – böylece IDE bunun `Starship` nesnelerinden oluşan bir dizi olduğunu bilecek:


```php
// src/Repository/StarshipRepository.php
// ... lines 1 - 12
class StarshipRepository extends ServiceEntityRepository
{
// ... lines 15 - 19
    /**
     * @return Starship[]
     */
    public function findIncomplete(): array
    {
// ... lines 25 - 31
    }
// ... lines 33 - 57
}
```

👉 Bu metod, tamamlanmamış gemileri döndürmek için kullanılacak.

Metodun içinde şu sorguyu döndürün:


```php
// src/Repository/StarshipRepository.php
// ... lines 1 - 12
class StarshipRepository extends ServiceEntityRepository
{
// ... lines 15 - 22
    public function findIncomplete(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.status != :status')
            ->orderBy('s.arrivedAt', 'DESC')
            ->setParameter('status', StarshipStatusEnum::COMPLETED)
            ->getQuery()
            ->getResult()
        ;
    }
// ... lines 33 - 57
}
```

👉 Bu metod, durumu `COMPLETED` olmayan gemileri tarihe göre sıralayıp döndürür.

`homepage` kontrolcüsüne dönüp `findAll()` metodunu `findIncomplete()` ile değiştirin:


```php
// src/Controller/MainController.php
// ... lines 1 - 9
class MainController extends AbstractController
{
// ... line 12
    public function homepage(
// ... line 14
    ): Response {
        $ships = $repository->findIncomplete();
// ... lines 17 - 22
    }
}
``` 

👉 Bu kod, sadece tamamlanmamış gemileri getirir.

## 🧪 Another Custom Query, Another Repository Method / Başka Bir Özel Sorgu, Başka Bir Repository Metodu

Kontrolcüdeki `$myShip` mantığını beğenmedik. Bunun sebebi sadece "benim gemim" fikrini sahte bir şekilde ilk gemi olarak almak değil; bu mantığın nerede gerekirse orada tekrar kullanılabilmesi için repository'e taşınması daha doğru.

`StarshipRepository` içinde yeni bir `public function findMyShip()` metodu ekleyin. Bu metod bir `Starship` nesnesi döndürecek. Şimdilik basit olsun: `return $this->findAll()[0];` – yani tablodaki ilk gemiyi döndürsün:

```php
// src/Repository/StarshipRepository.php

// ... lines 1 - 12
class StarshipRepository extends ServiceEntityRepository
{
// ... lines 15 - 33
    public function findMyShip(): Starship
    {
        return $this->findAll()[0];
    }
// ... lines 38 - 62
}
```

👉 Bu metod, varsayılan olarak ilk gemiyi "benim gemim" olarak döndürür.

Kontrolcüde bunu `$repository->findMyShip()` olarak kullanın:

```php 
// src/Controller/MainController.php
// ... lines 1 - 9
class MainController extends AbstractController
{
// ... line 12
    public function homepage(
// ... line 14
    ): Response {
// ... line 16
        $myShip = $repository->findMyShip();
// ... lines 18 - 22
    }
}
```

👉 Bu kod, kullanıcının gemisini döndürmek için repository metodunu çağırır.

---

Sonraki adımda, `Foundry` kütüphanesiyle fixture’larımızı eğlenceli hâle getireceğiz – sanki elimizde bir çoğaltıcı varmış gibi bir gemi filosu oluşturacağız. Haydi başlayalım!


---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./6_Fetching with DQL, the QueryBuilder & find().md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./8_Alien Tech for Fixtures Foundry & Faker.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
