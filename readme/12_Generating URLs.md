🌍 Yönlendirme

### Generating URLs / URL'ler Oluşturmak

---

🌍 Yönlendirme

### Creating the Show Page / Detay Sayfası Oluşturmak

Starship'ler için bir "detay sayfası" oluşturalım: yalnızca bir geminin bilgilerini gösteren bir sayfa. Ana sayfa `MainController` içinde yer alıyor. Buraya yeni bir route ve metod ekleyebilirdik. Ama sitemiz büyüdükçe, muhtemelen starship'lerle ilgili başka sayfalarımız da olacak: düzenleme, silme gibi. Bu yüzden bunun yerine `Controller/` dizininde yeni bir sınıf oluşturalım. Adı `StarshipController` olsun ve her zamanki gibi `AbstractController` sınıfını genişletsin.

Sınıf içinde işe koyulalım! `show()` adında bir public fonksiyon ekleyin. `Response` dönüş türünü belirleyin, ardından route'u yazın: `/starships/` ve bir joker karakter `{id}`. Yine isteğe bağlı ama şık olsun diye `\d+` regex'i ekleyerek sadece sayılarla eşleşmesini sağlayalım.

Ve `{id}` olduğu için, aşağıda `$id` adlı bir argüman kullanabiliriz. `dd($id)` diyerek test edelim.

```php
#[Route('/starships/{id<\d+>}')]
public function show(int $id): Response
{
    dd($id);
}
```

Tarayıcıda `/starships/2` adresine gidin. Harika!

Şimdi tanıdık bir şey yapacağız: bu `$id` ile eşleşen starship'i veritabanından (hayali olan) sorgulayacağız. Bunun anahtarı `StarshipRepository` servisimiz ve içindeki `find()` metodudur.

Kontrolcüde `StarshipRepository $repository` argümanını ekleyin... sonra `$ship = $repository->find($id)` deyin. Eğer `$ship` yoksa `throw $this->createNotFoundException()` ile 404 sayfası fırlatın.

Son olarak, JSON döndürmek yerine bir şablonu render edin: `return $this->render('starship/show.html.twig', ['ship' => $ship])`.

```php
#[Route('/starships/{id<\d+>}')]
public function show(int $id, StarshipRepository $repository): Response
{
    $ship = $repository->find($id);
    if (!$ship) {
        throw $this->createNotFoundException('Starship not found');
    }
    return $this->render('starship/show.html.twig', [
        'ship' => $ship,
    ]);
}
```

---

🌍 Yönlendirme

### Creating the Template / Şablon Oluşturmak

Kontrolcü hazır! Şimdi `templates/` dizininde bir `starship/` dizini ve içine `show.html.twig` dosyası oluşturun.

Neredeyse tüm Twig şablonları aynı şekilde başlar: `{% extends 'base.html.twig' %}`. Ardından bazı blokları ezelim! `title` bloğunu ezelim ve bu kez `ship.name` kullanalım. `endblock` ile kapatın.

Ana içerik için `body` bloğu, bir `h1` etiketi ve yine `ship.name`. Ardından bazı bilgileri içeren bir tablo ekleyelim.

```twig
{% extends 'base.html.twig' %}

{% block title %}{{ ship.name }}{% endblock %}

{% block body %}
    <h1>{{ ship.name }}</h1>
    <table>
        <tbody>
            <tr>
                <th>Class</th>
                <td>{{ ship.class }}</td>
            </tr>
            <tr>
                <th>Captain</th>
                <td>{{ ship.captain }}</td>
            </tr>
        </tbody>
    </table>
{% endblock %}
```

Sadece gemi verilerini yazdırıyoruz. Sayfayı deneyin... çalışıyor!

---

🌍 Yönlendirme

### Linking Between Pages / Sayfalar Arasında Bağlantı Kurmak

Sıradaki soru: Ana sayfadan bu yeni detay sayfasına nasıl bağlantı ekleriz? En bariz yöntem URL’yi elle yazmak olur: `/starships/` ve ardından id. Ama daha iyi bir yol var.

Symfony’ye şunu diyebiliriz:

> "Bu route’a bir URL oluşturmak istiyorum."

Avantajı şu: route’un URL’sini sonradan değiştirsek bile, ona giden tüm bağlantılar otomatik olarak güncellenir.

Bunu göstermek için terminalde şu komutu çalıştırın:

```bash
php bin/console debug:router
```

Henüz bahsetmedik ama her route’un dahili bir adı vardır. Şu an Symfony bu adları otomatik oluşturuyor, ama bir URL üretmek istediğimizde bu adı bizim belirlememiz gerekir.

Show sayfası rotasını bulun ve `name` parametresi ekleyin. Örneğin: `app_starship_show`.

```php
#[Route('/starships/{id<\d+>}', name: 'app_starship_show')]
public function show(int $id, StarshipRepository $repository): Response
```

İsim keyfi olabilir ama şu konvansiyonu takip ediyorum: `app` – çünkü uygulamaya ait bir route – ardından kontrolcü sınıf adı ve metot adı.

İsmi tanımlamak, rotanın çalışmasını değiştirmez. Ama artık ona URL üretebiliriz.

`templates/main/homepage.html.twig` dosyasını açın. Gemi adını bir bağlantıya dönüştürelim. `a` etiketiyle `href` içine `{{ path() }}` fonksiyonunu kullanalım. Route adını verin.

Ancak burada durursak çalışmaz. Çünkü:

> "URL oluşturmak için gereken parametreler eksik – id"

Mantıklı! Symfony bize şöyle der:

> "Tamam... ama bu rota bir joker içeriyor. Peki id kısmı için ne yazayım?"

Yani `path()` fonksiyonuna ikinci bir argüman daha eklemeliyiz. Bu Twig'de bir nesne (assoc array) gibi yazılır: `{ id: myShip.id }`.

```twig
<a href="{{ path('app_starship_show', {
    id: myShip.id
}) }}">{{ myShip.name }}</a>
```

Ve şimdi... oldu! URL şöyle görünüyor: `/starships/3`.

Sırada: sitemiz hâlâ çirkin görünüyor. Bunu düzeltmeye başlayalım: Tailwind CSS’i entegre edelim ve Symfony'nin AssetMapper bileşenini öğrenelim.
