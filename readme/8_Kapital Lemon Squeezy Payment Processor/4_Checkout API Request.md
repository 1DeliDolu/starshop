# 🔁 Checkout API Request / Checkout API İsteği

Sitemizin alt kısmında, bir ürüne tıklayınca ürün sayfası açılıyor ve sepete ekleyebiliyoruz. "Checkout" butonuna tıkladığımızda ise... hiçbir şey olmuyor. Hadi bunu düzeltelim!

Önceki bölümde, her bir LemonSqueezy ürünü için kontrol panelinden özel bir `checkout` bağlantısı (URL) oluşturup müşterilerle nasıl paylaşabileceğimizi gördük. Güzel haber: aynı şeyi `checkout API endpoint` ile de yapabiliriz!

## 🧾 Create a Checkout URL / Checkout URL’si Oluştur

Zaten yapılandırılmış bir `scoped HTTP client`'ımız var, o halde LemonSqueezy API’ye ilk isteğimizi yapalım. URL’yi bulmak için API belgelerini açın, aşağı kaydırın ve "Create a checkout" başlığını bulun. Burada ihtiyacımız olan `endpoint`, kullanmamız gereken `method` ve çok sayıda `config` yer alıyor. Hatta bir JSON yanıt örneği bile mevcut!

Kod örneğine göz atarsak... işte burada — `url` anahtarı! `data`, ardından `attributes` içinde yer alıyor. Müşteri "Checkout" butonuna tıkladığında, bu URL’ye yönlendirmek istiyoruz. Sorun değil!

Kodumuza geri dönelim, elimizde `OrderController.php` dosyası zaten var ve burada sepetle ilgili metodlarımız yer alıyor. Yeni bir metod oluşturalım: `checkout()`. Bunu şu route ile bağlayalım: `#[Route('/checkout', name: 'app_order_checkout')]`.

```php
// src/Controller/OrderController.php
// ... lines 1 - 11
class OrderController extends AbstractController
{
// ... lines 14 - 53
    #[Route('/checkout', name: 'app_order_checkout')]
    public function checkout(): Response {
    }
}
```

👉 Bu kod, "/checkout" adresine istek geldiğinde çalışacak yeni bir kontrolör metodu oluşturur.

`cart.html.twig` dosyasını açın... "Checkout with LemonSqueezy" bağlantısının `href` özelliğini şu şekilde ayarlayın: `path('app_order_checkout')`.

```twig
//templates/order/cart.html.twig
// ... lines 1 - 4
{% block content %}
// ... lines 6 - 46
    <div class="mt-9">
        <a class="w-[345px] flex ml-2 rounded-3xl border border-[#50272B] bg-[#4F272B] hover:bg-[#1C0000] shadow-inner poppins-bold text-white py-3 pl-5 uppercase"
           href="{{ path('app_order_checkout') }}"
        >
            Checkout with LemonSqueezy
        </a>
    </div>
// ... lines 55 - 66
{% endblock %}
```

👉 Bu bağlantıya tıklandığında müşteriler checkout rotasına yönlendirilir.

Şimdi hedefimiz, `LemonSqueezy API` aracılığıyla checkout URL’sini oluşturmak ve müşteriyi bu bağlantıya yönlendirmek.

Bunun için `HttpClientInterface $lsClient` ve `ShoppingCart $cart` bağımlılıklarını `checkout()` metoduna ekleyin. API çağrısı için iş mantığını ayrı bir metoda taşıyacağız. Aşağıya şunu yazın: `$lsCheckoutUrl = $this->createLsCheckoutUrl($lsClient, $cart);` ve ardından `return $this->redirect($lsCheckoutUrl);`.

```php
//src/Controller/OrderController.php
// ... lines 1 - 12
class OrderController extends AbstractController
{
// ... lines 15 - 54
    #[Route('/checkout', name: 'app_order_checkout')]
    public function checkout(
        HttpClientInterface $lsClient,
        ShoppingCart $cart,
    ): Response {
        $lsCheckoutUrl = $this->createLsCheckoutUrl($lsClient, $cart);
        return $this->redirect($lsCheckoutUrl);
    }
// ... lines 64 - 83
```

👉 Bu kod, dinamik olarak checkout URL’sini alır ve kullanıcıyı bu adrese yönlendirir.

## 🛠 Create Method and Send Request / Metodu Oluştur ve İsteği Gönder

Yeni metodu oluşturmak için PhpStorm’da Mac üzerinde `Option + Enter` tuşlarına basarak otomatik oluşturabilirsiniz. Bu metod bir `string` döndürecek. İçeride önce temel bir kontrol ekleyelim:

```php
if ($cart->isEmpty()) throw new \LogicException('Nothing to checkout!');
```

Aşağıya LemonSqueezy API’ye istek gönderecek kodu yazalım:

```php
$response = $lsClient->request(Request::METHOD_POST, 'checkouts', [
    'json' => [
        'data' => [
            'type' => 'checkouts',
        ],
    ],
]);
```

```php
// src/Controller/OrderController.php
// ... lines 1 - 64
    private function createLsCheckoutUrl(HttpClientInterface $lsClient, ShoppingCart $cart): string
    {
        if ($cart->isEmpty()) {
            throw new \LogicException('Nothing to checkout!');
        }

        $response = $lsClient->request(Request::METHOD_POST, 'checkouts', [
            'json' => [
                'data' => [
                    'type' => 'checkouts',
                ],
            ],
        ]);

        $lsCheckout = $response->toArray();
        return $lsCheckout['data']['attributes']['url'];
    }
// ... lines 82 - 83
```

👉 Bu metod, checkout API isteğini yapar ve dönen yanıt içinden `url` değerini alır.

Sayfayı yenileyip "Checkout" butonuna tıkladığınızda hata alabilirsiniz:

`Invalid URL: scheme is missing in "checkouts". Did you forget to add "http(s)://"?`

Bu, `scoped client`'ın doğru enjekte edilmediğini gösteriyor. Terminalde şu komutu çalıştırın:

```bash
bin/console debug:autowiring HttpClientInterface
```

👉 Bu komut, `HttpClientInterface`’in hangi servislere bağlandığını listeler.

Görüldüğü üzere, `lemonSqueezy.client` adını kullanmak için değişken adını `lemonSqueezyClient` olarak ayarlamalıyız. Ama kısa olan `$lsClient` adını sevmiştik. Neyse ki, Symfony’de `#[Target]` özelliğini kullanarak doğru servise bağlayabiliriz:

```php
use Symfony\Component\DependencyInjection\Attribute\Target;

#[Target('lemonSqueezy.client')]
HttpClientInterface $lsClient
```

```php
// src/Controller/OrderController.php
// ... lines 1 - 7
use Symfony\Component\DependencyInjection\Attribute\Target;
// ... lines 9 - 13
class OrderController extends AbstractController
{
// ... lines 16 - 55
    #[Route('/checkout', name: 'app_order_checkout')]
    public function checkout(
        #[Target('lemonSqueezy.client')]
        HttpClientInterface $lsClient,
// ... line 60
    ): Response {
// ... lines 62 - 64
    }
```

👉 Bu sayede `HttpClientInterface` değişkeni doğru client ile eşleştirilmiş olur.

Yeni hata: `HTTP/2 422 returned`. Bu, geçersiz veri nedeniyle istek işlenemedi demektir. Daha fazla detay görmek için şu satırı geçici olarak ekleyin:

```php
dd($response->getContent(false));
```

Yanıt içeriğinde şu alanların zorunlu olduğu belirtilir:

* `data/relationships/store/data/id`
* `data/relationships/variant/data/id`

Şimdi bu alanları ekleyelim. `store.id` için LemonSqueezy kontrol panelinde `Settings > Stores` kısmına gidin ve ID’yi alın. `variant.id` için ise ürün menüsünden `"Copy variant ID"` seçeneğini kullanın.

```php
$response = $lsClient->request(Request::METHOD_POST, 'checkouts', [
    'json' => [
        'data' => [
            'type' => 'checkouts',
            'relationships' => [
                'store' => [
                    'data' => [
                        'type' => 'stores',
                        'id' => '132127',
                    ],
                ],
                'variant' => [
                    'data' => [
                        'type' => 'variants',
                        'id' => '123456',
                    ],
                ],
            ],
        ],
    ],
]);
```

👉 `store.id` ve `variant.id` değerlerini `"string"` olarak yazdığınızdan emin olun!

Son olarak `dd()` satırını yorum satırına alın ve sayfayı yenileyin... İşte bu! `LemonSqueezy` ödeme sayfasına yönlendirildik ve e-limonatamızı satın almaya hazırız!

Sıradaki adım: Sabit (hard-coded) verileri dinamik hale getirelim.
