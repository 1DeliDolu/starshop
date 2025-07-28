### Creating JSON API Endpoints / JSON API Uç Noktaları Oluşturmak

Bir API oluşturmak istiyorsanız, Symfony ile bunu kesinlikle yapabilirsiniz. Hatta bu konuda **API Platform** sayesinde Symfony mükemmel bir seçenektir. API Platform, Symfony üzerine inşa edilmiş bir API oluşturma çatısıdır ve API'nizi hem hızlı geliştirmenizi sağlar hem de oldukça güçlü hale getirir.

Ama bir controller'dan doğrudan JSON döndürmek de oldukça kolaydır. Haydi bazı yıldız gemisi verilerini JSON olarak döndürelim.

---

### Creating the new Route & Controller / Yeni Route ve Controller Oluşturmak

Bu bizim ikinci sayfamız olacak. Aslında bir sayfa değil, bir "endpoint", yani ikinci route/controller çiftimiz. `MainController` içine yeni bir method ekleyebilirdik ama yapıyı düzenli tutmak adına yeni bir controller sınıfı oluşturalım.

`src/Controller` içinde `StarshipApiController` adında bir sınıf oluşturun ve `AbstractController` sınıfından türetin:

```php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StarshipApiController extends AbstractController
{
    #[Route('/api/starships')]
    public function getCollection(): Response
    {
        $starships = [
            [
                'name' => 'USS LeafyCruiser (NCC-0001)',
                'class' => 'Garden',
                'captain' => 'Jean-Luc Pickles',
                'status' => 'taken over by Q',
            ],
            [
                'name' => 'USS Espresso (NCC-1234-C)',
                'class' => 'Latte',
                'captain' => 'James T. Quick!',
                'status' => 'repaired',
            ],
            [
                'name' => 'USS Wanderlust (NCC-2024-W)',
                'class' => 'Delta Tourist',
                'captain' => 'Kathryn Journeyway',
                'status' => 'under construction',
            ],
        ];
        return $this->json($starships);
    }
}
```

Tarayıcıda `/api/starships` adresine gidin. İşte bu kadar kolay. JSON görünümü güzelse, bu Symfony’den değil; örneğin **JSONVue** gibi bir tarayıcı uzantısı kullanıyor olabilirsiniz.

---

### Adding a Model Class / Model Sınıfı Eklemek

Gerçek dünyada veritabanından veri çekerken ilişkisel diziler değil, **nesneler** (objects) kullanırız. Veritabanı eklemeyeceğiz ama nesnelerle çalışmaya başlayabiliriz.

`src/` içinde `Model/` adında yeni bir klasör oluşturun ve içinde `Starship` adında bir sınıf oluşturun:

```php
namespace App\Model;

class Starship
{
    public function __construct(
        private int $id,
        private string $name,
        private string $class,
        private string $captain,
        private string $status,
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getClass(): string { return $this->class; }
    public function getCaptain(): string { return $this->captain; }
    public function getStatus(): string { return $this->status; }
}
```

---

### Creating the Model Objects / Model Nesnelerini Oluşturmak

Controller içinde ilişkisel dizileri `Starship` nesnelerine dönüştürelim:

```php
use App\Model\Starship;

$starships = [
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

return $this->json($starships);
```

Ancak sayfayı yenilediğinizde büyük ihtimalle **boş nesneler** dönecektir. Çünkü `json_encode()` fonksiyonu, private özelliklere erişemez.

---

### Hello Symfony Serializer / Symfony Serializer ile Tanışın

Peki bu durumda ne yapacağız? Symfony’nin bileşenlerinden biri olan **serializer**, tam da bu iş için var: nesneleri JSON’a dönüştürmek (serialize) veya JSON’dan nesnelere çevirmek (deserialize). Ve getter metodlarını kullanarak private property’lere ulaşabilir!

Şu komutla serializer bileşenini kurun:

```bash
composer require serializer
```

Bu bir alias’tır, yani bir pack kurar: `symfony/serializer` paketi ve onunla birlikte çalışan bazı diğer bağımlılıkları yükler.

Hiçbir kod değişikliği yapmadan sayfayı yenileyin... ve artık çalışıyor! Nasıl oldu?

Symfony’nin `AbstractController` sınıfındaki `$this->json()` metodu, eğer sistemde serializer varsa onu otomatik olarak kullanır.

Ama diyelim ki controller dışında bir yerde JSON’a dönüştürme yapmanız gerekiyor. İşte o zaman **servisler** devreye girer.

Ve şimdi Symfony’deki en önemli kavramlardan biri olan **servisler**i öğrenmenin zamanı geldi.
