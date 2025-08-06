# 🔄 Dynamic Data / Dinamik Veri

Tamam! İlk `API` isteğimizi başarıyla yaptık! Bu işlem, müşteriye özel bir `checkout` URL’si oluşturuyor ve onu LemonSqueezy ödeme sayfasına yönlendiriyor. Ancak bu süreci oluştururken birçok değeri sabit (hard-coded) olarak girdik. Artık bu verileri dinamik hale getirme zamanı!

## 📦 Use Dynamic Data in the Checkout Object / Checkout Nesnesinde Dinamik Veri Kullanımı

### 1. Store ID’yi Ortam Değişkeni Olarak Tanımlayın

`Store ID`, hem `test` hem de `canlı` ortamlar için benzersiz olduğundan, bunu bir `ortam değişkeni` olarak tanımlamak mantıklıdır. `.env` dosyasını açın, `LEMON_SQUEEZY_API_KEY` altına aşağıdaki satırı ekleyin:

```bash
LEMON_SQUEEZY_STORE_ID=132127
```

👉 Bu satır, LemonSqueezy mağaza kimliğini ortam değişkeni olarak ayarlar.

Ardından `config/services.yaml` dosyasında `parameters` bölümüne şu satırı ekleyin:

```yaml
parameters:
    env(LEMON_SQUEEZY_STORE_ID): '%env(LEMON_SQUEEZY_STORE_ID)%'
```

👉 Bu, ortam değişkeninin Symfony konteyner parametresi olarak kullanılmasını sağlar.

Kontrolördeki sabit ID değerini şu şekilde değiştirin:

```php
'id' => $this->getParameter('env(LEMON_SQUEEZY_STORE_ID)'),
```

---

### 2. Store Variant IDs in the Database / Ürün Varyant ID’lerini Veritabanında Saklayın

Varyant ID’yi dinamik hale getirmek için `Product` entity’sine yeni bir alan ekleyin:

```bash
bin/console make:entity
```

Yeni alanın adı: `lsVariantId`
Türü: `string`
Uzunluk: varsayılan
`nullable`: `yes`

Ardından `src/Entity/Product.php` dosyasında aşağıdaki gibi tanımlandığını göreceksiniz:

```php
#[ORM\Column(length: 255, unique: true, nullable: true)]
private ?string $lsVariantId = null;

public function getLsVariantId(): ?string
{
    return $this->lsVariantId;
}

public function setLsVariantId(?string $lsVariantId): static
{
    $this->lsVariantId = $lsVariantId;
    return $this;
}
```

👉 Bu alan, her ürün için LemonSqueezy varyant ID’sini saklamamızı sağlar.

---

### 3. Migration Oluşturun ve Uygulayın

```bash
bin/console make:migration
bin/console doctrine:migrations:migrate
```

👉 Bu komutlar, yeni sütunu veritabanına ekler.

---

#### 4. Fixtures Üzerinden Varyant ID Atayın

`src/DataFixtures/AppFixtures.php` içinde ürünlere `lsVariantId` ekleyin. Örneğin:

```php
ProductFactory::new()->create([
    // ...
    'lsVariantId' => '737914',
]);
```

Yeni ürünler oluşturup panoda "Copy variant ID" diyerek diğer ürünler için de aynı işlemi tekrarlayın.

Sonrasında fixtures'ı tekrar yükleyin:

```bash
bin/console doctrine:fixtures:load
```

---

### 🧮 Checkout İşleminde Dinamik Ürün ve Miktar

`createLsCheckoutUrl()` içinde aşağıdaki kodu ekleyin:

```php
$products = $cart->getProducts();
$variantId = $products[0]->getLsVariantId();
$quantity = $cart->getProductQuantity($products[0]);
```

İstek verilerine `quantity`’yi de dahil edin:

```php
'attributes' => [
    'checkout_data' => [
        'variant_quantities' => [
            [
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ],
        ],
    ],
],
```

👉 Bu şekilde kullanıcı, sepetten doğru ürünü ve doğru miktarı LemonSqueezy’ye iletmiş olur.

---

### 👤 Pre-fill User Data / Kullanıcı Bilgilerini Otomatik Doldur

Kullanıcı zaten sisteme giriş yaptıysa, onun `email` ve `name` bilgilerini LemonSqueezy ödeme sayfasına aktarabiliriz.

`checkout()` metoduna şu bağımlılığı ekleyin:

```php
#[CurrentUser] ?User $user,
```

`createLsCheckoutUrl()` metoduna da aynı `$user` değişkenini gönderin:

```php
$lsCheckoutUrl = $this->createLsCheckoutUrl($lsClient, $cart, $user);
```

Ve metod imzasını şu şekilde güncelleyin:

```php
private function createLsCheckoutUrl(HttpClientInterface $lsClient, ShoppingCart $cart, ?User $user): string
```

Daha sonra, checkout istek verisinde şunu ekleyin:

```php
if ($user) {
    $attributes['checkout_data']['email'] = $user->getEmail();
    $attributes['checkout_data']['name'] = $user->getFirstName();
}
```

👉 Bu sayede, kullanıcı bilgilerinin önceden doldurulması sağlanır.

---

Şimdi her şey dinamik hale geldi:

* Mağaza ve varyant ID’leri sabit değil
* Miktar sepete göre belirleniyor
* Kullanıcı bilgileri önceden dolduruluyor

🎉 Artık sıradaki adım: Aynı anda birden fazla ürün (farklı aromalar) satın almayı desteklemek!
