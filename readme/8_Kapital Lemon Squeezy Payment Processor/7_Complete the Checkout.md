# ✅ Complete the Checkout / Satın Alma İşlemini Tamamla

Bir ürün daha satın aldım ve LemonSqueezy bana bir başarı mesajı gösterdi:

> Thanks for your order!

Kısa, öz ve net. Peki, bu mesajı özelleştirmek mümkün mü? Evet, mümkün! Ve bu ayar **ürüne özel** yapılabiliyor.

---

## 🛠 Confirmation Modal'ı Özelleştir

Kontrol panelinden şu adımları izleyin:

* `"Store"` → `"Products"` → Ürünü seçin
* Aşağıda `"Confirmation modal"` bölümüne gelin
* `"Title"` ve `"Message"` alanlarını özelleştirin

👉 Bu değişiklikler sadece **seçilen ürüne** uygulanır. Diğer ürünlerde de aynı mesajı görmek istiyorsanız hepsine tek tek ayarlamanız gerekir.

Ayrıca, bu modaldaki **butonun metni ve bağlantısı** da özelleştirilebilir. Varsayılan bağlantı `"my-orders"` sayfasına gider. Biz bu bağlantıyı kendi uygulamamıza yönlendirmek istiyoruz.

---

## 🧹 Satın Alma Sonrası Sepeti Temizle

Şu an sitemizde, satın aldığımız ürün **hala sepette duruyor**. Bunu düzeltmek için özel bir **başarı (success)** rotası oluşturacağız.

```php
// src/Controller/OrderController.php
// ... lines 1 - 15
class OrderController extends AbstractController
{
// ... lines 18 - 69
    #[Route('/checkout/success', name: 'app_order_success')]
    public function success(
        Request $request,
        ShoppingCart $cart,
    ): Response {
        $referer = $request->headers->get('referer');
        $lsStoreUrl = 'https://squeeze-the-day.lemonsqueezy.com';

        if (!str_starts_with($referer, $lsStoreUrl)) {
            return $this->redirectToRoute('app_homepage');
        }

        if ($cart->isEmpty()) {
            return $this->redirectToRoute('app_homepage');
        }

        $cart->clear();
        $this->addFlash('success', 'Thanks for your order!');
        return $this->redirectToRoute('app_homepage');
    }
}
```

👉 Bu metot:

* `referer` başlığını kontrol eder (doğrudan erişimi engellemek için)
* Sepet boşsa anasayfaya yönlendirir
* Aksi halde sepeti temizler ve başarı mesajı gösterir

---

## 🔗 redirect\_url Ayarını API Üzerinden Yap

Şu an bu bağlantıyı her ürün için panelden tek tek girmeniz gerekir. Ama daha kolay bir yol var: **API ile redirect\_url** göndermek.

API belgelerinde `"Create a checkout"` bölümünde `product_options.redirect_url` parametresi yer alır:

> A custom URL to redirect to after a successful purchase.

İşte aradığımız şey!

`createLsCheckoutUrl()` metoduna şu satırı ekleyin:

```php
$attributes['product_options']['redirect_url'] =
    $this->generateUrl('app_order_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
```

```php
// src/Controller/OrderController.php
// ... lines 1 - 91
    private function createLsCheckoutUrl(HttpClientInterface $lsClient, ShoppingCart $cart, ?User $user): string
    {
// ... lines 94 - 127
        $attributes['product_options']['redirect_url'] = $this->generateUrl('app_order_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
// ... lines 129 - 155
    }
```

👉 Bu sayede, her satın alma işleminden sonra kullanıcı otomatik olarak bizim oluşturduğumuz `/checkout/success` sayfasına yönlendirilir.

---

### 🧪 Son Test

1. Sepete ürün ekleyin
2. Checkout’a tıklayın
3. Ödeme bilgilerini girin
4. "Continue" butonuna tıklayın

Ve... evet! 👏

* Başarı mesajını gördük: **"Thanks for your order!"**
* Sepet artık **boş**

---

Sıradaki adım: **LemonSqueezy ile ilgili iş mantığını controller’dan ayırmak ve merkezi bir servis sınıfına taşımak.**
