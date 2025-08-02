# 🗂️ MapQueryParameter & Request Payload / MapQueryParameter & İstek Yükü

İstekten veri çekmeyle ilgili yeni özelliklerden bahsetmek istiyorum. Bu genellikle... biraz sıkıcı bir iştir. Ama yeni gelen özellikler gerçekten oldukça havalı.

## 🏷️ The MapQueryParameter Attribute / MapQueryParameter Özelliği

Örneğin, URL’ye `?query=banana` ekleyin. Bunu denetleyicimizde almak için, geleneksel olarak bir argümanda `Request` türünü belirtip oradan alırdık. Ve bu hâlâ çalışsa da, artık `?string $query` argümanını da ekleyebiliyoruz. Symfony’ye bunun bir sorgu parametresinden alınması gerektiğini söylemek için, başına bir öznitelik ekliyoruz: `#[MapQueryParameter]`.

Hepsi bu kadar! Çalıştığını kanıtlamak için `$query` değerini dökün (dump).

```php
// src/Controller/VinylController.php

// ... lines 1 - 11
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
// ... lines 13 - 15
class VinylController extends AbstractController
{
// ... lines 18 - 24
    public function homepage(
        #[MapQueryParameter] string $query = '',
    ): Response
    {
        dump($query);
// ... lines 30 - 42
    }
// ... lines 44 - 62
}
```

👉 Bu kod, `$query` parametresini URL’den sorgu parametresi olarak alır ve dump eder.

Web tarayıcısında sayfayı yenileyin. Web debug araç çubuğunda... tamamdır!

## ✅ Validation from the Type-Hint / Tür İpucundan Doğrulama

Öznitelik ayrıca bazı seçenekler de barındırır. Örneğin, sorgu parametreniz argümanınızdan farklı bir isme sahipse, burada belirtebilirsiniz.

Ve yalnızca değeri istekten almakla kalmaz, bu sistem doğrulama da gerçekleştirir. Şimdi izleyin: bunu çoğaltıp bir `int $page = 1` argümanı ekleyin. Ayrıca, `$query` argümanını opsiyonel yaptığımızdan emin olun, böylece URL’de olması gerekmez. Aşağıda `$page`’i dökün.

```php 
// src/Controller/VinylController.php

// ... lines 1 - 15
class VinylController extends AbstractController
{
// ... lines 18 - 24
    public function homepage(
        #[MapQueryParameter] string $query = '',
        #[MapQueryParameter] int $page = 1,
    ): Response
    {
        dump($query, $page);
// ... lines 31 - 43
    }
// ... lines 45 - 63
}
```

👉 Bu kod, hem `$query` hem de `$page` parametrelerini sorgu parametrelerinden alıp dump eder.

Şimdi URL’ye `?page=3` eklersek... sürpriz yok: 3 değeri dökülür. Ancak burada güzel olan, gerçekten bir tamsayı (integer) 3 almamız: bir string değil. Şimdi de `page=banana` deneyin. Bir 404! Sistem, `int` türünde bir ipucu olduğunu görüp doğrulama yapar.

## 🧰 The filter\_var() Function / filter\_var() Fonksiyonu

Bu tüm sistem, `QueryParameterValueResolver` adlı bir şey tarafından yönetilir. Gerçekten derinlemesine incelemek isterseniz, bu sınıfa bakabilirsiniz. Dahili olarak, doğrulama için PHP’nin `filter_var()` fonksiyonunu kullanır. Bu fonksiyona çok aşina olmayabilirsiniz ama oldukça güçlüdür. Bir değeri, bir veya birden fazla filtreyle birlikte gönderirsiniz... ve bu değerin filtreleri karşılayıp karşılamadığını söyler. Ayrıca filtreleri kontrol etmek için seçenekler de gönderebilirsiniz.

Ekstra bir şey yapmazsanız, sistem bizim `int` tür ipucumuzu okur ve `filter_var()` fonksiyonuna tamsayı gerektiren bir filtre gönderir. Bu yüzden başarısız olur.

## 🔢 Validating an int is in a Range / Bir int’in Aralıkta Doğrulanması

Ama daha da gelişmiş olabiliriz. Varsayılan olarak 10 olan `$limit` adlı bir argüman ekleyin. Bunu aşağıda dökün. Ama ben limitin 1 ile 10 arasında olmasını istiyorum. Bunu zorlamak için, `filter_var()` fonksiyonuna özel iki seçenek geçirin: `min_range` olarak 1 ve `max_range` olarak 10.

```php
// src/Controller/VinylController.php

// ... lines 1 - 15
class VinylController extends AbstractController
{
// ... lines 18 - 24
    public function homepage(
        #[MapQueryParameter] string $query = '',
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter(options: ['min_range' => 1, 'max_range' => 10])] int $limit = 10,
    ): Response
    {
        dump($query, $page, $limit);
// ... lines 32 - 44
    }
// ... lines 46 - 64
}
```

👉 Bu kod, `$limit` parametresini 1 ile 10 arasında olacak şekilde doğrular.

Şimdi deneyelim! Örneğin `?limit=3` dersek, beklediğimiz gibi çalışır. Ama `limit=13` dersek, `filter_var()` başarısız olur ve 404 alırız! Harika!

## 🧺 Grabbing Array Query Parameters / Dizi Olarak Sorgu Parametrelerini Alma

Bu, dizileri de işleyebilir. Kopyalayın ve bir tane daha argüman ekleyin: varsayılanı boş dizi olan `$filters`. Bunu da dökün.

```php
// src/Controller/VinylController.php

// ... lines 1 - 15
class VinylController extends AbstractController
{
// ... lines 18 - 24
    public function homepage(
        #[MapQueryParameter] string $query = '',
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter(options: ['min_range' => 1, 'max_range' => 10])] int $limit = 10,
        #[MapQueryParameter] array $filters = [],
    ): Response
    {
        dump($query, $page, $limit, $filters);
// ... lines 33 - 45
    }
// ... lines 47 - 65
}
```

👉 Bu kod, `$filters` adında bir dizi sorgu parametresi alır ve dump eder.

Tarayıcıda, `?filters[]=banana&filters[]=apple` ekleyin. Web debug araç çubuğundaki diziye bakın! Ayrıca ilişkisel diziler için de çalışır: `foo` ve `bar` ekleyin köşeli parantezlere. Evet! Bir ilişkisel dizi.

Bu, sorgu parametrelerini almak için gerçekten iyi tasarlanmış bir özelliktir.

## 📦 Request Body / İstek Gövdesi

Ayrıca, isteğin gövdesini almanız gerekirse, Symfony 6.3’te yeni bir yöntem var: `$request->getPayload()`. Bir API mi oluşturuyorsunuz? İstemciniz gövdeye JSON gönderdiğinde, `$request->getPayload()` kullanarak bunu bir ilişkisel diziye ayrıştırabilirsiniz. Güzel! Ama aynı zamanda, kullanıcı normal bir HTML form gönderirse, `$request->getPayload()` orada da çalışır. HTML formunun gönderildiğini algılar ve `$_POST` verisini bir diziye ayrıştırır. Yani ister bir API ister normal form kullanıyor olun, isteğin yükünü almak için birleşik bir yöntemimiz var. Küçük ama güzel.

## 🧩 MapRequestPayload / MapRequestPayload Özelliği

JSON’dan bahsetmişken, yükü bir nesneye dönüştürmek için serializer kullanmak da yaygındır. Bu, başka bir yeni özellikle ilgilidir: `#[MapRequestPayload]`.

Bu durumda, `__invoke` denetleyici eylemidir. Bu şunu söyler: istekteki JSON’u al ve bunu yukarıda örneği verilen `ProductReviewDto` nesnesine ayrıştır. JSON serializer’dan geçirildikten sonra, doğrulama da yapılır. Yani yine iyi düşünülmüş bir özellik.

Tamam, istek ile ilgili konular bu kadar! Sırada, 6.4’teki yeni bir özelliği test edeceğiz: konsol komutlarını profil oluşturma yeteneği.
