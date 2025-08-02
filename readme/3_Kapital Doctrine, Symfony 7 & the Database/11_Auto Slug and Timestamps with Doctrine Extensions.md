# 🕓 Auto Slug and Timestamps with Doctrine Extensions / Doctrine Uzantılarıyla Otomatik Slug ve Zaman Damgaları

`Starship` varlığına üç yeni alan ekledik: `slug`, `updatedAt` ve `createdAt`. Ancak artık `fixtures` verilerimiz yüklenmiyor! Bunun nedeni basit: bu 3 alan veritabanında zorunlu, ancak `StarshipFactory` bunları ayarlamıyor. Bunları ekleyebiliriz ama eklememize gerek olmamalı. Mükemmel bir dünyada, `slug` otomatik olarak `name` alanından oluşturulmalı, `updatedAt` varlık değiştiğinde şu anki zamana ayarlanmalı ve `createdAt` varlık oluşturulduğunda şu anki zamana ayarlanmalı.

## DoctrineExtensions / 

Ve bunu yapabilen bir paket var: `DoctrineExtensions`! Terminalde şunu çalıştırın:

```bash 
composer require stof/doctrine-extensions-bundle
```

👉 Bu komut, `DoctrineExtensions` eklentisini Symfony projesine dahil eder.

Bu paket bir tarif (recipe) içeriyor... ama resmi olarak kabul edilmiyor. Bu, üçüncü parti yani "contrib" bir tarif. Genellikle sorun olmaz, sadece şunu bilin: contrib tarifleri topluluk tarafından eklenir ve Symfony çekirdek ekibi tarafından denetlenmez.

Yukarıya bakarsanız şunları göreceksiniz: en önemli paketler `gedmo/doctrine-extensions` (asıl mantığı içerir) ve `stof/doctrine-extensions-bundle` (Symfony ile entegrasyon sağlar). Diğerleri hakkında endişelenmenize gerek yok.

Şunu çalıştırın:

```bash
git status
```

👉 Bu komut, tarifin neleri eklediğini gösterir. Paket yapılandırılmış ve yeni bir yapılandırma dosyası eklenmiş. Güzel! Bu paket için yapılandırma dosyasını düzenleyip uzantıları etkinleştirmemiz gerekiyor. Her bir uzantı, varlıklarınız için bir süper güç gibidir.

## 🔧 Enabling Extensions / Uzantıları Etkinleştirme

`config/packages/stof_doctrine_extensions.yaml` dosyasını açın. `default_locale` altına yeni bir anahtar ekleyin: `orm:`, ardından `default:` ve bunun içine iki uzantıyı etkinleştirin: `timestampable: true` ve `sluggable: true`:

```yaml
stof_doctrine_extensions:
    default_locale: de_DE
    orm:
        default:
            timestampable: true
            sluggable: true
```

👉 Bu yapılandırma, zaman damgası ve slug uzantılarını genel olarak etkinleştirir.

Ama bu uzantıları `Starship` varlığı için hayata geçirmek üzere biraz daha yapılandırma yapmamız gerekiyor. Şimdi bu varlığı tekrar açalım.

## ✨ Using Extensions / Uzantıları Kullanma

`$slug` özelliğinin üzerine şu niteliği ekleyin: `#[Slug]` ve sınıfı `Gedmo\Mapping\Annotation` altından içe aktarın. İçine `fields:` ekleyip `name` içeren bir dizi olarak ayarlayın:

```php
// src/Entity/Starship.php

use Gedmo\Mapping\Annotation\Slug;

// ... lines 1 - 10
class Starship
{
// ... lines 13 - 33
    #[Slug(fields: ['name'])]
    private ?string $slug = null;
// ... lines 36 - 158
}
```


👉 Bu uzantıya, `slug` değerinin `name` alanından türetilmesi gerektiğini söyler.

`$updatedAt` özelliğinin üzerine şu niteliği ekleyin: `#[Timestampable(on: 'update')]`, böylece bu alan, varlık her güncellendiğinde mevcut zamanla doldurulur:

```php
// src/Entity/Starship.php
use Gedmo\Mapping\Annotation\Timestampable;

// ... lines 1 - 10
class Starship
{
// ... lines 13 - 41
    #[Timestampable(on: 'update')]
    private ?\DateTimeImmutable $updatedAt = null;
// ... lines 44 - 158
}
```


👉 Bu, varlık güncellendiğinde `updatedAt` alanının ayarlanmasını sağlar.

`$createdAt` için de aynısını yapın ama `on: 'create'` ile:

```php
// src/Entity/Starship.php

// ... lines 1 - 10
class Starship
{
// ... lines 13 - 37
    #[Timestampable(on: 'create')]
    private ?\DateTimeImmutable $createdAt = null;
// ... lines 40 - 158
}
```

👉 Bu, varlık oluşturulduğunda `createdAt` alanının ayarlanmasını sağlar.

## 🔁 Reloading the Fixtures / Fixtures'ı Yeniden Yüklemek

Hadi deneyelim! Terminalde şunu çalıştırın:

```bash
symfony console doctrine:fixtures:load
```

👉 Bu komut, test verilerini sıfırdan yükler.

Ve... çalıştı! Değerleri görmek için SQL sorgumuzu çalıştırın:

```bash
symfony console doctrine:query:sql "SELECT name, slug, updated_at, created_at FROM starship"
```

👉 Bu komut, `starship` tablosundaki ilgili alanları listeler.

Evet! `slug` `name` alanından oluşturulmuş, `updatedAt` ve `createdAt` ise varlık oluşturulurkenki zamana ayarlanmış. Doctrine, ilk kaydı aynı zamanda bir güncelleme olarak kabul eder; bu yüzden `updatedAt` ve `createdAt` aynı değere sahiptir.

## 🔢 Slugs are Kept Unique / Slug'lar Benzersiz Kalır

Biraz aşağı kaydırın. Bu `slug` değerlerinin `-1` ile bittiğini fark ettiniz mi? Bu neden böyle? Çünkü `slug` alanı benzersiz (`unique`), ancak `name` alanı değil. Örneğin bazı `starship` nesneleri (mesela `Lunar Marauder`) aynı isme sahip. `slug` uzantısı bunu akıllıca algılar ve benzersizliği sağlamak için otomatik olarak sayısal bir sonek (`-1`, `-2` vb.) ekler. Akıllıca!

Artık `starship` varlıklarımız için benzersiz, insan tarafından okunabilir bir `slug` değerimiz var. Şimdi bu değeri çirkin `id` yerine URL’lerde kullanalım. Ayrıca `Controller Value Resolvers` adlı bir şey kullanarak denetleyicilerimizi ileri teknolojiye kavuşturacağız! Sırada bu var!


---
<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./10_ Starship Upgrade Adding Slug and Timestamp Fields.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./12_High-Tech Controllers Auto-inject Entities.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
