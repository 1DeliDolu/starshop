# 🚀 Fetching with DQL, the QueryBuilder & find() / DQL, QueryBuilder ve find() ile Verileri Getirme

Veritabanımız artık parıldayan, sahte yıldız gemileriyle dolu! Ama ana sayfa hâlâ sabit (hardcoded) gemileri gösteriyor. Sıkıcı! Artık bunları veritabanından yükleme zamanı. Bu, uygulamamızın harikalığını 10 kat artıracak!

Terminalinize geçin. Tüm yıldız gemilerini seçmek için olan SQL sorgusunu hatırlıyor musunuz? Tekrar çalıştırın:

```bash
symfony console doctrine:query:sql "select * from starship"
```

👉 Bu komut, tüm `starship` tablosunu SQL ile listeler.

Bu ham SQL, ancak Doctrine ORM'in kendine ait bir sorgulama dili vardır: `DQL` yani Doctrine Query Language! SQL'e benzer, fakat tablolar yerine varlık (entity) nesneleri üzerinden sorgulama yaparsınız. Yukarıdaki sorguyu DQL olarak çalıştırın:

```bash
symfony console doctrine:query:dql "select s from App\Entity\Starship s"
```

👉 Bu komut, `Starship` varlıklarını DQL ile getirir.

Görünüşü biraz garip, ama bu PHP'nin `Starship` nesnelerini dökümlemesi – ve gerçekten üç tane var, tıpkı ham sorguda olduğu gibi.

Şimdi bunu ana sayfa denetleyicimizde kullanalım. `src/Controller/MainController.php` dosyasını açın ve `homepage()` metodunu bulun. Bu metotta `StarshipRepository` yerine (eski `Model` dizininden kalma), `EntityManagerInterface $em` enjekte edin.

```php
// ... lines 1 - 9
class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(
        EntityManagerInterface $em,
    ): Response {
// ... lines 16 - 22
    }
}
```

👉 Bu yapı ile Doctrine'in entity yöneticisi otomatik olarak enjekte edilir.

## EntityManagerInterface / EntityManagerInterface

Son bölümde, Doctrine'in `AppFixture::load()` metoduna bir `ObjectManager` ilettiğini gördük. Bu `EntityManagerInterface`, bir tür `ObjectManager`'dır ve Doctrine entity yöneticisini otomatik olarak bağlamak (autowire) için kullanacağımız arayüzdür.

## Using createQuery() / createQuery() Kullanımı

Aşağıya şu kodu yazın: `$ships = $em->createQuery()` ve DQL sorgusunu `SELECT s FROM App\Entity\Starship s` olarak geçin. Son olarak `->getResult()` çağırın.

```php
// ... lines 1 - 9
class MainController extends AbstractController
{
// ... line 12
    public function homepage(
// ... line 14
    ): Response {
        $ships = $em->createQuery('SELECT s FROM App\Entity\Starship s')->getResult();
// ... lines 17 - 22
    }
}
```

👉 Bu sorgu, verileri getirir ama ham veri yerine `Starship` nesneleri dizisi döner.

Metodun kalanını olduğu gibi bırakın.

Ana sayfayı yenileyin. Görünüş aynı... bu iyi bir işaret! Web hata ayıklama araç çubuğuna yakından bakın – yeni bir "Doctrine" bölümü var. OooooooOooo.

## Doctrine Profiler / Doctrine Profiler

"Doctrine" profiler panelini açmak için tıklayın. Harika! Bu panel, son istekte yürütülen tüm sorguları gösterir. Yalnızca bir tane var – bu mantıklı!

Daha okunabilir biçimde biçimlendirilmiş sorguyu görebilir, SQL aracınıza kopyalayıp yapıştırabileceğiniz çalıştırılabilir sorguya erişebilir, "Explain query" düğmesiyle veritabanına özel sorgu açıklamasını görebilir ve "View query backtrace" ile sorguyu tetikleyen kod izini görebilirsiniz.

Bu benim favorim! Sorguya yol açan çağrı yığınını gösterir – bu durumda `homepage()` metodumuz.

## Using the QueryBuilder / QueryBuilder Kullanımı

DQL çok da güzel görünmüyor! Neyse ki Doctrine'in bir de `query builder`'ı var. Bu harika bir araç: DQL dizesini elle yazmak yerine, bir nesne ile oluştururuz. `homepage()` metoduna dönün, `$em->createQuery()` yerine `$em->createQueryBuilder()` yazın. Üzerinden `->select('s')`, sonra `->from(Starship::class, 's')` zincirleyin. `App\Entity`'den `use` satırını eklemeyi unutmayın. Bonus: `'App\Entity\Starship'` dizesi yerine `Starship::class` kullanabiliriz.

Son olarak, `->getResult()` çağrısından önce `->getQuery()` ekleyin.

```php
// ... lines 1 - 10
class MainController extends AbstractController
// ... lines 12 - 13
    public function homepage(
// ... line 15
    ): Response {
        $ships = $em->createQueryBuilder()
            ->select('s')
            ->from(Starship::class, 's')
            ->getQuery()
            ->getResult();
// ... lines 22 - 27
    }
}
```

👉 Bu kod, `QueryBuilder` ile oluşturulmuş bir sorguyu çalıştırır ve `Starship` nesnelerini getirir.

Uygulamayı tekrar yenileyin… hâlâ çalışıyor!

Şimdi bir şeyi daha düzenlememiz gerekiyor. Bir gemiye tıklayın… ah hayır!

**Starship not found.**

Ahh, `StarshipController::show()` eylemi hâlâ eski `StarshipRepository` ile sabit veriyi kullanıyor. Bunu düzeltmeliyiz!

`src/Controller/StarshipController.php` dosyasını açın ve `show()` metodunu bulun. Veri sorgulamamız gerektiği için `StarshipRepository $repository` yerine `EntityManagerInterface $em` kullanın.

```php
// ... lines 1 - 10
class StarshipController extends AbstractController
{
    #[Route('/starships/{id<\d+>}', name: 'app_starship_show')]
    public function show(int $id, EntityManagerInterface $em): Response
    {
// ... lines 16 - 23
    }
}
```

👉 Bu yapı ile entity yöneticisi üzerinden sorgulama yapılabilir.

## Using find() / find() Kullanımı

`$ship = $em->find(Starship::class, $id)` yazın.

```php
// ... lines 1 - 10
class StarshipController extends AbstractController
{
// ... line 13
    public function show(int $id, EntityManagerInterface $em): Response
    {
        $ship = $em->find(Starship::class, $id);
// ... lines 17 - 23
    }
}
```

👉 `find()` metodunun ilk parametresi getirilecek varlık sınıfıdır, ikincisi ise kimlik (ID)'dir.

Uygulamaya dönün ve… yenileyin. Çalışıyor! Web hata ayıklama çubuğuna bakın – tek bir sorgu çalıştı.

`Model/` dizinimizle işimiz bitti. Gerçi `StarshipStatusEnum` hâlâ lazım, bu yüzden düzenli olması için onu `Entity/` klasörüne taşıyın. PhpStorm yeniden adlandırmayı halleder. Şimdi `src/Model` dizinini silin ve kutlayın! Kullanılmayan kodları silmeyi seviyorum!

Sırada ne var? Sorgulama mantığını denetleyicilerden çıkarmak için varlık depolarına (repository) bakalım.
