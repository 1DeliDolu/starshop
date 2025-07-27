## 🛠️ Kendi Servisini Oluşturmak

### \[Creating your own Service] / \[Kendi Servisini Oluşturmak]

Servislerin iş yaptığını ve Symfony'nin kullanabileceğimiz servislerle dolu olduğunu biliyoruz. Şu komutu çalıştırın:

```
php bin/console debug:autowiring
```

Bu size servislerin bir “menüsünü” verir. Buradan, uygun sınıf veya arayüzle type-hint edilmiş bir argüman yazarak istediğinizi "sipariş" edebilirsiniz.

Biz de kendi kodumuzda elbette işler yapıyoruz... umarız!
Şu anda bu işlerin tamamı controller içinde yapılıyor, örneğin Starship verilerini oluşturmak.
Evet, şimdilik bu veriler sabit, ama bunun karmaşık bir veritabanı sorgusu olduğunu hayal edin.
Bu mantığı controller içinde yazmak "eh işte"... ama bu kodu başka bir yerde kullanmak istersek?
Örneğin ana sayfada Starship sayısını dinamik olarak almak istersek?

---

## 🏗️ Servis Sınıfını Oluşturmak

Bunun için bu "iş"i kendi servis sınıfımıza taşımamız gerekir.
Her iki controller da bu servisi kullanabilir.

`src/` dizininde yeni bir `Repository/` dizini oluşturun ve içinde `StarshipRepository.php` adında yeni bir sınıf tanımlayın:

```php
namespace App\Repository;

class StarshipRepository
{
}
```

Tıpkı `Starship` sınıfı gibi, bu sınıf da Symfony’ye özel değildir.
Yani Symfony bu sınıfın adına, konumuna veya yapısına aldırmaz.
`StarshipRepository` adını verdik ve `Repository` dizinine koyduk çünkü bu, veri getirmekle ilgili sınıflar için yaygın bir isimlendirme biçimidir.

---

## 🔗 Yeni Servisi Autowire Etmek

Bu sınıf içinde henüz bir şey yapmadan önce, bunu controller’da kullanmayı deneyelim.
İyi haber: Bu sınıfı oluşturduğumuz anda autowiring için uygundur.

Controller metoduna `StarshipRepository $repository` parametresi ekleyin ve `dd($repository)` diyerek kontrol edin:

```php
use App\Repository\StarshipRepository;

class StarshipApiController extends AbstractController
{
    public function getCollection(LoggerInterface $logger, StarshipRepository $repository): Response
    {
        $logger->info('Starship collection retrieved');
        dd($repository);
    }
}
```

Sayfayı yenileyin... çalışıyor! Symfony, `StarshipRepository` type-hint’ini gördü, nesneyi oluşturdu ve bize verdi.
Şimdi `dd()` satırını silin ve Starship verilerini bu sınıfa taşıyalım.

---

## 🧾 findAll() Metodu Oluşturmak

```php
use App\Model\Starship;

class StarshipRepository
{
    public function findAll(): array
    {
        return [
            new Starship(
                1,
                'USS LeafyCruiser (NCC-0001)',
                'Garden',
                'Jean-Luc Pickles',
                'taken over by Q'
            ),
            new Starship(
                2,
                'USS Espresso (NCC-1234-C)',
                'Latte',
                'James T. Quick!',
                'repaired',
            ),
            new Starship(
                3,
                'USS Wanderlust (NCC-2024-W)',
                'Delta Tourist',
                'Kathryn Journeyway',
                'under construction',
            ),
        ];
    }
}
```

Şimdi controller’a dönün ve bunu şöyle sade bir şekilde kullanın:

```php
$starships = $repository->findAll();
```

---

## 🧱 Constructor ile Autowiring

Şimdi bir seviye daha ileri gidelim.
`StarshipRepository` içinde başka bir servise ihtiyaç duyduğumuzu düşünelim.
Sorun değil! Otomatik bağlama (autowiring) kullanabiliriz.

Bu sefer `findAll()` metoduna argüman eklemeyeceğiz.
Bunun yerine `__construct()` metoduna ekleyeceğiz:

```php
use Psr\Log\LoggerInterface;

class StarshipRepository
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function findAll(): array
    {
        $this->logger->info('Starship collection retrieved');
        // ...
    }
}
```

Artık controller’daki `LoggerInterface $logger` parametresine ihtiyacımız kalmadı, silebiliriz.

Sayfayı yenileyin. Hata yoksa her şey yolunda.
Log’un yazıldığını görmek için `/ _profiler` adresine gidin, son isteğe tıklayın, Logs bölümüne bakın... işte mesaj!

**Neden construct metoduna servis tanımladık?**
Çünkü bir servise erişmek istiyorsak (`logger`, veritabanı bağlantısı vs.), autowiring bu şekilde yapılır.
Controller metoduna argüman eklemek özel bir kolaylıktır ve yalnızca controller metodlarında çalışır.
Ama `__construct()` yöntemi Symfony’de her yerde geçerlidir — gerçek autowiring budur.

---

## 🧪 Servisi Başka Sayfada Kullanmak

Yeni servisimizin keyfini çıkaralım ve ana sayfada kullanalım.
`MainController`’ı açın.

Bu sabit `$starshipCount` değeri artık eski kaldı.
Autowiring ile `StarshipRepository $starshipRepository` ekleyin, sonra:

```php
$ships = $starshipRepository->findAll();
$starshipCount = count($ships);
$myShip = $ships[array_rand($ships)];
```

Şimdi sayfayı yenileyin.
Ekranda rastgele gemi ve doğru gemi sayısını göreceksiniz (şablonda 10 ile çarpılıyor).

---

## ✨ Twig ile Nesne Yazdırma

Ve inanılmaz bir şey oldu!
Az önce `$myShip` bir dizi idi.
Şimdi ise bir `Starship` nesnesi.
Ama sayfa hala çalışıyor.

Twig bir harika!
Şablonda `myShip.name` yazınca, eğer bu bir dizi ise `name` anahtarını, bir nesne ise `getName()` metodunu çağırır.

Özelliğe doğrudan erişilemese bile (örneğin `private` ise), Twig bunu algılar ve varsa `getName()` metodunu çağırır.

---

## 🧹 Temizleme ve Optimize Etme

Şimdi küçük bir son dokunuş yapalım:
PHP’de `count()` ile saymak yerine, Twig içinde `|length` filtresini kullanalım.

Controller’da `starshipCount` değişkenini silin, onun yerine `ships` dizisini gönderin:

```php
return $this->render('main/homepage.html.twig', [
    'myShip' => $myShip,
    'ships' => $ships,
]);
```

Şablonda:

```twig
<div>
    Browse through {{ ships|length * 10 }} starships!
    {% if ships|length > 2 %}
        ...
    {% endif %}
</div>
```

Böylece hem kod daha sade, hem de Twig'in gücünden faydalanıyoruz.
Sayfa yine sorunsuz çalışıyor!

