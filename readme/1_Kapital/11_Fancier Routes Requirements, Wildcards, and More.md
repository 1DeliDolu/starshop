

# Fancier Routes: Requirements, Wildcards, and More / Gelişmiş Rotalar: Gereksinimler, Joker Karakterler ve Daha Fazlası

Tüm bu yeni kod organizasyonuyla birlikte, hadi bir yıldız gemisini getirecek yeni bir API uç noktası oluşturarak bunu kutlayalım. Her zamanki gibi başlayın: `get()` adında bir public fonksiyon oluşturun. Yanına isteğe bağlı olarak `Response` dönüş türünü ekleyelim. Bunun üstüne `#[Route]` anotasyonunu ekleyin ve URL'yi `/api/starships/...` olarak ayarlayın. Bu sefer URL’nin son kısmı dinamik olmalı: `/api/starships/5` veya `/api/starships/25` gibi adreslerle eşleşmeli. Bunu nasıl yapabiliriz? Rota joker karakterle nasıl eşleşir?

Cevap: `{`, bir isim ve `}` kullanarak.

Parantez içindeki isim herhangi bir şey olabilir. Bu rota artık `/api/starships/*` ile eşleşir. Ve bu ismi ne koyarsanız koyun, artık eşleşen bir argüman kullanabilirsiniz: `$id`.

Aşağıda bu değeri dump ederek çalışıp çalışmadığını kontrol edin:

```php
#[Route('/api/starships/{id}')]
public function get($id): Response
{
    dd($id);
}
```

---

⚠️ Uyarı

## Restricting the Wildcard to be a Number / Joker Karakteri Sayıyla Sınırlamak

Tarayıcıda `/api/starships/2` adresine gidin… çalışıyor!

Uygulamamızda ID bir tam sayı olacak. Ama eğer `wharf` gibi bir şey denersek, rota yine de eşleşir ve kontrolcümüz çağrılır. Gerçek bir uygulamada, veritabanında `WHERE ID = 'wharf'` gibi bir sorgu hata vermez; sadece eşleşen kayıt bulamaz. Bu durumda bir 404 sayfası gösterilir (birazdan bunu nasıl yapacağımızı göreceğiz).

Ama bazen bu değeri kısıtlamak isteyebiliriz:

Sadece joker karakter tam sayıysa rota eşleşsin.

Bunun için, süslü parantezin içine isimden sonra `<`, `>` ve içine bir regex ekleyin: `\d+`.

```php
#[Route('/api/starships/{id<\d+>}')]
public function get(int $id): Response
{
    dd($id);
}
```

Bu, herhangi bir uzunluktaki rakamlarla eşleşir. `wharf` URL’sini yenilediğimizde 404 alırız. Çünkü rota eşleşmedi ve kontrolcü çağrılmadı. Ama `/2` hâlâ çalışır.

Ek olarak, artık sadece rakamlarla eşleştiği için, argümana `int` türü ekleyebiliriz. Artık `'2'` (string) yerine `2` (integer) alırız.

---

🌍 Yönlendirme

## Restricting the Route HTTP Method / Rota HTTP Yöntemini Sınırlamak

API'lerde yaygın olan şey, rotaları belirli HTTP yöntemleriyle eşleştirmektir (örneğin, sadece GET veya POST). Örneğin, tüm yıldız gemilerini almak istiyorsak kullanıcılar bir GET isteği yapmalı… aynı şekilde tek bir gemi için de.

Eğer yeni bir yıldız gemisi oluşturacak bir uç nokta ekleseydik, standart yöntem aynı URL'yi kullanmak olurdu: `/api/starships`, ama bu sefer POST isteğiyle.

Şu anda, kullanıcı `/api/starships` adresine GET veya POST ile gelse bile ilk rota eşleşir.

Bu nedenle, API'lerde genellikle `methods` seçeneği eklenir. Aşağıda bunu da yapalım: `methods: ['GET']`.

```php
#[Route('/api/starships', methods: ['GET'])]
public function getCollection(StarshipRepository $repository): Response
```

```php
#[Route('/api/starships/{id<\d+>}', methods: ['GET'])]
public function get(int $id): Response
```

Bunu tarayıcıda kolayca test edemeyiz, ama `/api/starships/2` adresine POST isteği yapsaydık, rota eşleşmezdi.

Ancak bunu terminalden görebiliriz:

```bash
php bin/console debug:router
```

Mükemmel! Çoğu rota tüm yöntemlerle eşleşir… ama bizim iki API rotamız sadece GET istekleriyle eşleşir.

---

🌍 Yönlendirme

## Prefixing Every Route URL / Tüm Rota URL'lerine Ön Ek Eklemek

Bir rota numarası daha! Bu eğlenceli!

Bu kontrolcünün her rotası aynı URL ile başlıyor: `/api/starships`. Her rotaya tam URL yazmak sorun değil. Ama istersek tüm rotaların URL’sine otomatik olarak bir ön ek verebiliriz.

Sınıfın üstüne `#[Route('/api/starships')]` ekleyin.

Yöntemlerin üstündeki `#[Route]` ifadesinden farklı olarak, bu bir rota oluşturmaz. Sadece bu sınıftaki her rota bu URL ile başlasın demektir.

Bu durumda, ilk rotanın yol kısmını tamamen kaldırabiliriz. İkincisi içinse sadece joker kısmı yeterlidir:

```php
#[Route('/api/starships')]
class StarshipApiController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function getCollection(StarshipRepository $repository): Response

    #[Route('/{id<\d+>}', methods: ['GET'])]
    public function get(int $id): Response
}
```

Yeniden `debug:router` komutunu çalıştırın:

```bash
php bin/console debug:router
```

URL'ler değişmedi! Harika.

---

🌍 Yönlendirme

## Finishing the new API Endpoint / Yeni API Uç Noktasını Tamamlama

Şimdi uç noktamızı tamamlayalım. Bu ID ile eşleşen tek gemiyi bulmamız gerekiyor. Normalde veritabanında `SELECT * FROM ship WHERE id = ?` şeklinde sorgularız. Ama gemilerimiz şu an hardcoded. Yine de gerçek veritabanı mantığına benzer bir yapı kurabiliriz.

Zaten elimizde `StarshipRepository` adında bir servis var – yıldız gemisi verisi getirme işinden sorumlu. Ona yeni bir yetenek ekleyelim: Belirli bir ID için tek bir yıldız gemisi getirme yeteneği.

Yeni bir public fonksiyon ekleyin: `find()`. `int $id` alacak ve `?Starship` dönecek. Yani bir yıldız gemisi bulunursa onu, bulunamazsa `null` dönecek.

En kolay yöntem şu an `findAll()` döngüsünden geçmek:

```php
public function find(int $id): ?Starship
{
    foreach ($this->findAll() as $starship) {
        if ($starship->getId() === $id) {
            return $starship;
        }
    }
    return null;
}
```

Bu sayede kontrolcümüz çok basit hale gelir. Sadece repository'yi otomatik olarak bağlayın ve `find($id)` çağırın:

```php
public function get(int $id, StarshipRepository $repository): Response
{
    $starship = $repository->find($id);
    return $this->json($starship);
}
```

Yenileyin. Mükemmel!

---

⚠️ Uyarı

## Triggering a 404 Page / 404 Sayfasını Tetiklemek

Ama eğer veritabanımızda olmayan bir ID denersek – mesela `/200` – ekranda `null` görürüz. Bu pek hoş değil.

Bu durumda 404 durum kodu dönen bir yanıt göndermeliyiz.

Bunu yapmak için yaygın bir desen takip edilir: bir nesne sorgulanır, sonuç kontrol edilir. Eğer sonuç yoksa 404 fırlatılır.

Bunun için: `throw $this->createNotFoundException()` kullanılır. Mesaj da verebiliriz:

```php
public function get(int $id, StarshipRepository $repository): Response
{
    $starship = $repository->find($id);
    if (!$starship) {
        throw $this->createNotFoundException('Starship not found');
    }
    return $this->json($starship);
}
```

Dikkat edin: `throw` anahtar kelimesi ile özel bir exception fırlatılır ve bu da Symfony'de otomatik olarak 404 yanıtı üretir. Bu satıra gelindiğinde sonrasındaki hiçbir şey çalışmaz.

Deneyin! Evet! Bir 404 yanıtı! "Starship not found" mesajı yalnızca geliştiricilere dev modda gösterilir. Üretimde tamamen farklı bir sayfa veya JSON döner. Detaylar için dökümana bakabilirsiniz.


