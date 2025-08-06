# 🗂️ Centralize LemonSqueezy Business Logic / LemonSqueezy İş Mantığını Merkezi Hale Getirin

Şimdiye kadar, ödeme işlemlerimizi başlatmaya odaklandık ve bunu başardık! Ancak kodumuz şu anda denetleyici (controller) içinde dağınık halde. LemonSqueezy'nin API'sı ile ilgili her şeyin ayrı bir sınıfta olması çok daha kullanışlı olmaz mıydı? Tabii ki öyle olurdu! Hadi kodumuzu düzenleyelim!

Denetleyicideki tüm LemonSqueezy ile ilgili kodları bulmamız ve bunları bakım, tekrar kullanım ve test açısından daha kolay olması için ayrı bir servise taşımamız gerekiyor. Bunun için `src/Store/` dizininde `LemonSqueezyApi` adında yeni bir sınıf oluşturun. Bu sınıfı `final readonly` olarak yapın. Şimdi, `createLsCheckoutUrl()` metodumuzu taşıyabiliriz. Bu büyük bloğu kopyalayın, çıkarın ve yeni sınıfımıza yapıştırın – ve bu sefer, metodu `public` yapın. LemonSqueezy ile ilgili olduğunu zaten bildiğimiz için, ismini sadeleştirip `createCheckoutUrl()` olarak değiştirebiliriz.

---


```php
// src/Store/LemonSqueezyApi.php
// ... lines 1 - 9
final readonly class LemonSqueezyApi
{
    public function createCheckoutUrl(HttpClientInterface $lsClient, ShoppingCart $cart, ?User $user): string
    {
        if ($cart->isEmpty()) {
            throw new \LogicException('Nothing to checkout!');
        }
        $products = $cart->getProducts();
        $variantId = $products[0]->getLsVariantId();
        $attributes = [];
        if ($user) {
            $attributes['checkout_data']['email'] = $user->getEmail();
// ... line 24
        }
// ... lines 26 - 75
    }
}
```

👉 Bu sınıf, LemonSqueezy ile ilgili işlemleri merkezi hale getirmek için oluşturulmuştur.

---

Sonraki adımda, `$lsClient` ve `$cart` değişkenlerini alıp, bunları yapıcıda (`__construct`) bağımlılık olarak tanımlayacağız. Ayrıca `$lsClient` ismini sadeleştirip `$client` olarak kullanacağız. Bu argümanın üzerine `#[Target('lemonSqueezyClient')]` ekleyin ve her özelliğin başına `private` yazın.

---


```php
//src/Store/LemonSqueezyApi.php
// ... lines 1 - 9
use Symfony\Contracts\HttpClient\HttpClientInterface;
// ... line 11
final readonly class LemonSqueezyApi
{
    public function __construct(
        #[Target('lemonSqueezyClient')]
        private HttpClientInterface $client,
        private ShoppingCart $cart,
// ... lines 18 - 20
    ) {
    }
// ... lines 23 - 88
}
```

👉 Bu yapıcı, bağımlılıkları (HTTP istemcisi ve alışveriş sepeti) sınıfa enjekte eder.

---

Şimdi, `$cart` değişkenini bir özellik olarak `$this->cart` şeklinde kullanın. Aynı işlemi kalan `$cart` değişkenleri için de yapın. Ayrıca `$lsClient` yerine `$this->client` kullanın.

---


```php
// src/Store/LemonSqueezyApi.php
// ... lines 1 - 23
    public function createCheckoutUrl(?User $user): string
    {
        if ($this->cart->isEmpty()) {
            throw new \LogicException('Nothing to checkout!');
        }
// ... line 29
        $products = $this->cart->getProducts();
// ... lines 31 - 37
        if (count($products) === 1) {
// ... lines 39 - 44
        } else {
            $attributes['custom_price'] = $this->cart->getTotal();
// ... lines 47 - 57
        }
// ... lines 59 - 61
        $response = $this->client->request(Request::METHOD_POST, 'checkouts', [
// ... lines 63 - 87
    }
// ... lines 89 - 90
```

👉 Burada, tüm alışveriş sepeti ve istemci işlemleri sınıf özellikleri üzerinden yapılır.

---

Şimdi URL oluşturmak için bir servise ihtiyacımız var. Bunu yapıcıya `UrlGeneratorInterface $urlGenerator` olarak enjekte edebiliriz. Ardından, `$this->generateUrl()` yerine `$this->urlGenerator->generate()` kullanılır.

---


```php
// src/Store/LemonSqueezyApi.php
// ... lines 1 - 8
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
// ... lines 10 - 11
final readonly class LemonSqueezyApi
{
    public function __construct(
// ... lines 15 - 17
        private UrlGeneratorInterface $urlGenerator,
// ... lines 19 - 20
    ) {
    }
// ... line 23
    public function createCheckoutUrl(?User $user): string
    {
// ... lines 26 - 59
        $attributes['product_options']['redirect_url'] = $this->urlGenerator->generate('app_order_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
// ... lines 61 - 87
    }
}
```

👉 Bu yapı ile yönlendirme URL'leri otomatik olarak oluşturulur.

---

Ayrıca, parametrelere erişmemiz gerekiyor. Tüm `ParameterBagInterface` servisini enjekte etmek yerine, yalnızca bir tanesine (`storeId`) ihtiyacımız olduğundan, bunu doğrudan enjekte edelim.

Yapıcıya şu satırı ekleyin: `private readonly string $storeId,`. Üzerine ise `#[Autowire('%env(LEMON_SQUEEZY_STORE_ID)%')]` ekleyin. Son olarak, `$this->getParameter()` kullanımını `$this->storeId` ile değiştirin. Bu örnekte yalnızca bir kez kullanıldığı için işimiz kolay.

---


```php
// src/Store/LemonSqueezyApi.php
// ... lines 1 - 11
final readonly class LemonSqueezyApi
{
    public function __construct(
// ... lines 15 - 18
        #[Autowire('%env(LEMON_SQUEEZY_STORE_ID)%')]
        private string $storeId,
    ) {
    }
// ... line 23
    public function createCheckoutUrl(?User $user): string
    {
// ... lines 26 - 61
        $response = $this->client->request(Request::METHOD_POST, 'checkouts', [
            'json' => [
                'data' => [
// ... lines 65 - 66
                    'relationships' => [
                        'store' => [
                            'data' => [
// ... line 70
                                'id' => $this->storeId,
                            ],
                        ],
// ... lines 74 - 79
                    ],
                ],
            ],
        ]);
// ... lines 84 - 87
    }
}
```

👉 Burada, mağaza kimliği doğrudan ortam değişkeninden alınır.

---

Şimdi, `OrderController::checkout()` metodunda kullanılmayan bağımlılıkları kaldırıp, bunun yerine `LemonSqueezyApi $lsApi` enjekte edin. Aşağıda ise servisi `$lsCheckoutUrl = $lsApi->createCheckoutUrl();` ile kullanın.

---


```php
// src/Controller/OrderController.php
// ... lines 1 - 17
class OrderController extends AbstractController
{
// ... lines 20 - 59
    #[Route('/checkout', name: 'app_order_checkout')]
    public function checkout(
        LemonSqueezyApi $lsApi,
// ... line 63
    ): Response {
        $lsCheckoutUrl = $lsApi->createCheckoutUrl($user);
        return $this->redirect($lsCheckoutUrl);
    }
// ... lines 69 - 89
}
```

👉 Artık ödeme URL'si, merkezi servisten oluşturulup yönlendirme yapılır.

---

Test zamanı! Sitemizi tekrar yükleyin, "Classic Lemonade" seçin, sepete ekleyin ve "Checkout with LemonSqueezy" butonuna tıklayın. Evet! LemonSqueezy ödeme sayfasındayız ve her şey harika görünüyor!

---

Şimdi, başarılı sipariş sonrası kullanılan `$lsStoreUrl` değerini dinamik hale getirebilir miyiz? Evet! LemonSqueezy'nin bu iş için bir API uç noktası var! "Retrieve a store" uç noktasını API dokümantasyonunda bulun. Örnek yanıtta, URL'yi `attributes` içinden okuyabileceğimizi görebilirsiniz. Kodumuzda, `LemonSqueezyApi` içinde yeni bir public metod oluşturun. İsmi `retrieveStoreUrl()` olsun ve bir string döndürsün. İçinde, `$response = $this->client->request(Request::METHOD_GET, 'stores/' . $this->storeId)` satırını ekleyin. Altına `$lsStore = $response->toArray()` yazın ve son olarak, `return $lsStore['data']['attributes']['url']` satırını ekleyin.

---


```php
// src/Store/LemonSqueezyApi.php
// ... lines 1 - 11
final readonly class LemonSqueezyApi
{
// ... lines 14 - 89
    public function retrieveStoreUrl(): string
    {
        $response = $this->client->request(Request::METHOD_GET, 'stores/' . $this->storeId);
        $lsStore = $response->toArray();
        return $lsStore['data']['attributes']['url'];
    }
}
```

👉 Bu metot, mağaza URL'sini dinamik olarak LemonSqueezy API'dan çeker.

---

`success()` metodunda ise `LemonSqueezyApi $lsApi` servisini enjekte edip, sabit URL yerine `$lsStoreUrl = $lsApi->retrieveStoreUrl();` kullanın.

---


```php
// src/Controller/OrderController.php
// ... lines 1 - 17
class OrderController extends AbstractController
{
// ... lines 20 - 69
    #[Route('/checkout/success', name: 'app_order_success')]
    public function success(
// ... lines 72 - 74
    ): Response
    {
// ... line 77
        $lsStoreUrl = $lsApi->retrieveStoreUrl();
// ... lines 79 - 89
    }
}
```

👉 Başarılı sipariş sonrası yönlendirme için mağaza URL'si artık dinamik olarak elde edilir.

---

Tekrar test edin! Siteden bir limonata seçin, sepete ekleyin, "Checkout" butonuna tıklayın, gerekli bilgileri doldurun, "Pay" butonuna basın ve "successful" modaldan "Continue"ya tıklayın. İşte başarı mesajı! Her şey hala çalışıyor!

---

Sonraki adım: Şimdi, LemonSqueezy'deki bir müşteriyi sistemimizdeki karşılık gelen kullanıcıya atayacağız, böylece hangi kullanıcının hangi alışverişi yaptığını bileceğiz.
