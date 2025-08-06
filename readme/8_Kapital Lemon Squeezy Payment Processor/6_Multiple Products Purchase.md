# 🧃 Multiple Products Purchase / Birden Fazla Ürün Satın Alma

Tek bir ürün satın alma işlemi oldukça güzel görünüyor. Hatta miktar bile belirleyebiliyoruz ve bu LemonSqueezy checkout sayfasına yansıyor. Peki ya sepete başka bir ürün daha eklersek? Farklı bir ürün. Evet, burada bir sorun var. Sepette iki farklı e-limonata çeşidi varken "Checkout" butonuna bastık, ancak ödeme sayfasında sadece **ilk ürün** görünüyor. Bunu nasıl düzeltiriz? Maalesef küçük bir sorun var...

## ⚠ LemonSqueezy Sınırlaması

LemonSqueezy şu anda **sadece bir ürün** satın alımına izin veriyor. Hayal kırıklığı! Yol haritalarında bu sınırlamanın gelecekte kalkacağı belirtilmiş olsa da, şu an için bu bize yardımcı olmuyor.

## 🧠 Yaratıcı Bir Çözüm

API belgelerine baktığımızda, LemonSqueezy'nin bize **özel fiyat** (`custom_price`) belirleme imkânı sunduğunu görüyoruz. O halde bunu test edelim.

---

## 🧱 OrderController İçinde Yapılacaklar

`createLsCheckoutUrl()` içinde `attributes` yapısını dinamik hale getireceğiz. Eğer sepette yalnızca **bir ürün** varsa eski sistemi, birden fazla ürün varsa `custom_price` kullanan yeni yapıyı uygulayacağız.

```php
//src/Controller/OrderController.php
// ... lines 1 - 15
class OrderController extends AbstractController
{
// ... lines 18 - 69
    private function createLsCheckoutUrl(HttpClientInterface $lsClient, ShoppingCart $cart, ?User $user): string
    {
        $products = $cart->getProducts();

        if (count($products) === 1) {
            $attributes['checkout_data']['variant_quantities'] = [
                [
                    'variant_id' => (int) $products[0]->getLsVariantId(),
                    'quantity' => $cart->getProductQuantity($products[0]),
                ],
            ];
        } else {
            $attributes['custom_price'] = $cart->getTotal();

            $description = '';
            foreach ($products as $product) {
                $description .= $product->getName()
                    . ' for $' . number_format($product->getPrice() / 100, 2)
                    . ' x ' . $cart->getProductQuantity($product)
                    . '<br>';
            }

            $attributes['product_options'] = [
                'name' => 'E-lemonades',
                'description' => $description,
            ];
        }

        if ($user) {
            $attributes['checkout_data']['email'] = $user->getEmail();
            $attributes['checkout_data']['name'] = $user->getFirstName();
        }

        $response = $lsClient->request(Request::METHOD_POST, 'checkouts', [
            'json' => [
                'data' => [
                    'type' => 'checkouts',
                    'attributes' => $attributes,
                    'relationships' => [
                        'store' => [
                            'data' => [
                                'type' => 'stores',
                                'id' => $this->getParameter('env(LEMON_SQUEEZY_STORE_ID)'),
                            ],
                        ],
                        'variant' => [
                            'data' => [
                                'type' => 'variants',
                                'id' => $products[0]->getLsVariantId(), // herhangi bir varyant ID, zorunlu
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $lsCheckout = $response->toArray();
        return $lsCheckout['data']['attributes']['url'];
    }
}
```

👉 Bu çözümde, LemonSqueezy’ye yalnızca **bir ürün** gösteriyoruz, ancak tüm ürünleri tek bir özel fiyat (`custom_price`) altında gruplayarak toplam bedel üzerinden satış yapıyoruz.

---

## 🧪 Test Zamanı

1. Ana sayfaya gidin, iki farklı ürün seçin.
2. Miktarları belirleyin, sepete ekleyin.
3. Sepete gidin, "Checkout with LemonSqueezy" butonuna tıklayın.

Şimdi ödeme sayfasında:

* Ürün adı: `E-lemonades`
* Toplam fiyat: `$8.97` gibi
* Alt açıklamada tüm ürün isimleri, miktarları ve fiyatları listeleniyor.

👉 Bu yöntem ideal olmasa da **tek tıklamayla** birden fazla ürün satmamızı sağlıyor.

---

## 🖼 Geliştirme Fikirleri

* Checkout sayfasında görünen **ürün görselini** API üzerinden değiştirmek mümkün.
* LemonSqueezy panelinde “çeşitli e-limonatalar” için özel bir ürün oluşturup, onun varyant ID’sini burada kullanabiliriz.

**Not:** Ürün adını ve açıklamasını özelleştirsek bile **LemonSqueezy e-postalarında ve faturalarında** hâlâ **orijinal ürün adı ve görseli** yer alır. Bu şu an için değiştirilemiyor.

---

## ✅ Sonuç

Müşteri tarafında ödeme başarıyla tamamlanıyor. Ancak gelen e-postada sadece ilk ürünün adı görünüyor. Bu bir sınırlama, ama şu an için elimizdeki en iyi çözüm bu.

---

Sıradaki adım: Ödeme sonrası işlemleri (post-checkout operations) düzenleyerek süreci daha da güzelleştirelim.
