# Twig & Templates

### \[Installing Twig] / \[Twig Kurulumu]

Terminalde, değişikliklerinizi commit ettiğinizden emin olun; çünkü bu yeni paketin projemize neler eklediğini görmek istiyoruz. Bunu zaten yaptık. Şimdi Twig'i şu komutla yükleyin:

```bash
composer require twig
```

💡 İpucu

### \[Composer "Packs"] / \[Composer "Paketleri"]

Muhtemelen `twig`'in bir takma ad olduğunu fark ettiniz... bu sefer `symfony/twig-pack` adlı bir pakete. Ve Symfony'de "pack" kelimesi önemli. Bir pack, aynı anda birden fazla paketi kurmaya yardımcı olan sahte bir pakettir.

`composer.json` dosyasını açın. `symfony/twig-pack` yerine burada üç yeni paket olduğunu göreceksiniz... ve twig-pack dosyada bile görünmez!

```json
"require": {
    "symfony/twig-bundle": "7.0.*",
    "twig/extra-bundle": "^2.12|^3.0",
    "twig/twig": "^2.12|^3.0"
}
```

Bu üç paket, eksiksiz ve güçlü bir Twig kurulumu için gereken her şeyi sağlar. Yani "pack" kelimesini gördüğünüzde endişelenmeyin: sadece birden fazla paketi kurmak için bir kısayoldur.

✏️ Yapılandırma

### \[Symfony Bundles] / \[Symfony Paketleri]

Tarifin ne yaptığını görmek için şu komutu çalıştırın:

```bash
git status
```

Alışıldık `composer.json`, `composer.lock` ve `symfony.lock` dosyalarının yanında `config/bundles.php` dosyasında da bir değişiklik göreceksiniz. Bir bundle, Symfony ile entegre olan bir PHP paketidir... yani temelde bir Symfony eklentisidir. Bir bundle kurduğunuzda, bunu `bundles.php` dosyasında aktifleştirmeniz gerekir. Ama tarif sistemi bunu bizim yerimize yapar, bu yüzden bu dosyayı elle düzenlememize gerek yoktur.

```php
return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
];
```

✏️ Yapılandırma

### \[The Twig Recipe] / \[Twig Tarif Dosyası]

Tarifin ikinci yaptığı şey `config/packages/twig.yaml` dosyasını oluşturmaktı. `config/packages/` içindeki her dosyanın amacı bir bundle'ı yapılandırmaktır.

```yaml
twig:
    default_path: "%kernel.project_dir%/templates"
when@test:
    twig:
        strict_variables: true
```

Örneğin, `twig.yaml` dosyası TwigBundle davranışını kontrol eder. Buradaki satır Twig'e şunu söyler:

Tüm şablon dosyalarım `.twig` ile bitecek.

Daha fazla yapılandırılabilir özellik var ama şimdilik ihtiyacımız yok.

Son olarak tarif, `templates/` adında bir dizin oluşturur. Tahmin ettiğiniz gibi, şablon dosyalarımız burada yer alacak! Hatta içine `base.html.twig` dosyasını bile ekledi.

🌍 Yönlendirme

### \[Rendering a Template] / \[Bir Şablonun Render Edilmesi]

İlk şablonumuzu render edelim! Bunun için, denetleyicinizin `AbstractController` sınıfından türemesini sağlayın. Bu sınıf, bize bazı yardımcı yöntemler kazandırır.

```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/')]
    public function homepage(): Response
    {
        return $this->render('main/homepage.html.twig');
    }
}
```

Şablon dosyasının ismi size kalmış, ama yaygın standart denetleyici adıyla eşleşen bir klasör ve metod adıyla eşleşen bir dosya kullanmaktır.

Şimdi bunu oluşturalım: `templates/` içinde `main/` adında bir dizin ve içinde `homepage.html.twig` dosyası:

```twig
<h1>
    Starshop: your monopoly-busting option for Starship parts!
</h1>
```

Yenileyin. Çalışıyor!

🌍 Yönlendirme

### \[Passing Data to a Template] / \[Şablona Veri Aktarmak]

Bir veritabanı sorgusu yaptığımızı varsayalım ve yıldız gemisi sayısını şablona aktarmak isteyelim. Şimdilik sahte bir sayı kullanalım:

```php
$starshipCount = 457;
return $this->render('main/homepage.html.twig', [
    'numberOfStarships' => $starshipCount,
]);
```

Şablonda, bu değişkeni görüntülemek için:

```twig
<div>
    Browse through {{ numberOfStarships }} starships!
</div>
```

💡 İpucu

### \[Twig Syntax Overview] / \[Twig Söz Dizimi Genel Bakış]

Twig'in üç temel söz dizimi vardır:

1. **{{ ... }}** – "Bir şey söyle": bir değeri yazdırmak için kullanılır.
2. **{% ... %}** – "Bir şey yap": if, for gibi yapılandırmalar için kullanılır.
3. **{# ... #}** – Yorum satırı.

Örnek:

```twig
{% if numberOfStarships > 400 %}
    <p>
        That's a shiploads of ships!
    </p>
{% endif %}
```

Yorum satırı:

```twig
{# Bu bir yorumdur #}
```

✏️ Yapılandırma

### \[Rendering an Associative Array] / \[İlişkisel Dizi Render Etmek]

Denetleyicide bir ilişkisel dizi oluşturalım ve şablona aktaralım:

```php
$myShip = [
    'name' => 'USS LeafyCruiser (NCC-0001)',
    'class' => 'Garden',
    'captain' => 'Jean-Luc Pickles',
    'status' => 'under construction',
];
return $this->render('main/homepage.html.twig', [
    'numberOfStarships' => $starshipCount,
    'myShip' => $myShip,
]);
```

Şablonda bunu kullanmak:

```twig
<table>
    <tr>
        <th>Name</th>
        <td>{{ myShip.name }}</td>
    </tr>
    <tr>
        <th>Class</th>
        <td>{{ myShip.class }}</td>
    </tr>
    <tr>
        <th>Captain</th>
        <td>{{ myShip.captain }}</td>
    </tr>
    <tr>
        <th>Status</th>
        <td>{{ myShip.status }}</td>
    </tr>
</table>
```

💡 İpucu

### \[Twig Filters] / \[Twig Filtreleri]

Twig ayrıca filtreler de sunar. Bunlar değerleri dönüştürmek için kullanılır ve pipe (`|`) sembolüyle uygulanır.

Örneğin, bir değeri büyük harfe çevirmek için:

```twig
{{ myShip.captain|upper }}
```

Filtreler zincirleme de kullanılabilir:

```twig
{{ myShip.captain|upper|lower|title }}
```

Bu şekilde, Twig’in güçlü ve esnek bir şablon sistemi olduğunu görmüş olduk. Bir sonraki adım, **template inheritance** yani şablon mirası!

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./4_Magical Flex Recipes.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./6_ Twig Template Inheritance.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
