# 🔧 Setting the Relation / İlişkiyi Kurmak {#setting-relation}

Peki, ilişkiyi gerçekten nasıl kurarız? Nasıl deriz:

Bu `StarshipPart`, şu `Starship`'e ait?

Şimdiye kadar `AppFixtures` içinde `Foundry` ile çalışıyorduk. Birazdan `Foundry`'ye geri döneceğiz, ama bu işin nasıl çalıştığını anlamak için biraz eski usul ilerleyelim.

Yeni bir `Starship()` ile başlayın... ardından gerekli özellikleri ayarlamak için bazı kodlar yapıştıracağız. Sonra `\$manager->persist(\$starship)` ekleyin:

```php
//src/DataFixtures/AppFixtures.php

// ... lines 1 - 4
use App\Entity\Starship;
// ... lines 6 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        StarshipFactory::createOne([
// ... lines 18 - 22
        ]);
        $starship = new Starship();
        $starship->setName('USS Taco Tuesday');
        $starship->setClass('Tex-Mex');
        $starship->checkIn();
        $starship->setCaptain('James T. Nacho');
        $manager->persist($starship);
// ... lines 31 - 55
    }
}
```

👉 Bu kod, manuel olarak yeni bir `Starship` nesnesi oluşturur ve `persist()` ile kaydeder.

Sonra yeni bir `StarshipPart` oluşturun ve öncekine benzer şekilde özellikleri doldurun. Ardından `\$manager->persist(\$part)` ile kaydedin ve sonunda `\$manager->flush()` çağırın:

```php

//src/DataFixtures/AppFixtures.php

// ... lines 1 - 4
use App\Entity\Starship;
use App\Entity\StarshipPart;
// ... lines 7 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 24
        $starship = new Starship();
        $starship->setName('USS Taco Tuesday');
        $starship->setClass('Tex-Mex');
        $starship->checkIn();
        $starship->setCaptain('James T. Nacho');
        $manager->persist($starship);
        $part = new StarshipPart();
        $part->setName('spoiler');
        $part->setNotes('There\'s no air drag in space, but it looks cool.');
        $part->setPrice(500);
        $manager->persist($part);
        $manager->flush();
// ... lines 38 - 55
    }
}
```

👉 Bu kod, bir `StarshipPart` nesnesi oluşturur ve onu da kaydeder; ama henüz `Starship` ile ilişkilendirmez.

Normalde `Foundry`, `persist()` ve `flush()` işlemlerini bizim yerimize yapar. Ama bu sefer manuel çalıştığımız için kendimiz yapmalıyız.

Artık elimizde bir `Starship` ve bir `StarshipPart` var, ama hâlâ ilişkili değiller. Yine de fixture'ları yüklemeyi deneyin. Terminalden şunu çalıştırın:

```bash
symfony console doctrine:fixtures:load
```

👉 Bu komut, fixture verilerini veritabanına yükler.

Ama bir hata alırız:

```
starship_id cannot be null on the starship_part table.
```

Neden bu sütun zorunlu? Çünkü `StarshipPart` içinde `starship` özelliği `ManyToOne` ve `JoinColumn()` ile tanımlanmış:

```php
//src/Entity/StarshipPart.php

// ... lines 1 - 10
class StarshipPart
{
// ... lines 13 - 28
    #[ORM\ManyToOne(inversedBy: 'parts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Starship $starship = null;
// ... lines 32 - 84
}
```

👉 Bu kod, her `StarshipPart`'ın bir `Starship`'e ait olmasını zorunlu kılar (`nullable: false`).

## 🧩 Assigning the Part to the Starship / Parçayı Starship'e Atamak

Peki bu parçanın şu `Starship`'e ait olduğunu nasıl söyleriz? Cevap gayet basit. `flush()` çağrısından önce herhangi bir yerde şunu yazın: `\$part->setStarship(\$starship)`:

```php
src/DataFixtures/AppFixtures.php

// ... lines 1 - 12
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 17 - 36
        $part->setStarship($starship);
        $manager->flush();
// ... lines 39 - 56
    }
}
```

👉 Bu satır, `StarshipPart` nesnesini ilgili `Starship` nesnesine bağlar.

Hepsi bu kadar. Doctrine'de `starship_id` özelliğini ayarlamıyoruz, hatta bir ID bile göndermiyoruz, örneğin `\$starship->getId()` gibi. Hayır! Sadece nesneleri ayarlıyoruz. Doctrine bu sıkıcı detaylarla ilgileniyor: önce `Starship` kaydediliyor, sonra onun yeni ID'si ile `starship_part` tablosuna `starship_id` değeri atanıyor.

Akıllıca!

Fixture'ları tekrar çalıştırın:

```bash
symfony console doctrine:fixtures:load
```

👉 Bu komut, hatasız bir şekilde fixture'ları yükler.

Kontrol edelim:

```bash
symfony console doctrine:query:sql 'SELECT * FROM starship_part'
```

👉 Bu SQL sorgusu, tüm `starship_part` kayıtlarını getirir ve `starship_id` değerini gösterir.

Ve işte orada! Tek parçamız `starship_id = 75` ile mutlu bir şekilde bağlı. Şimdi bu ID'yi kontrol edin:

```bash
symfony console doctrine:query:sql 'SELECT * FROM starship WHERE id = 75'
```

👉 Bu komut, `id = 75` olan `Starship` kaydını getirir.

Görüldüğü üzere: `Starship id 75`, `StarshipPart id 1`'e sahip. Harikayız!

## 🎓 Doctrine: work with Objects, Not IDs / Doctrine: Kimliklerle Değil Nesnelerle Çalışın

Özetle: Doctrine ilişkileriyle çalışırken, nesneler dünyasındasınız. Kimlikleri unutun. Doctrine bu kısmı sizin yerinize halleder. Sadece nesneyi ayarlayın, gerisini Doctrine halleder.

Ama doğrusu, `AppFixtures` içinde tek bir `Starship` ve tek bir `StarshipPart` oluşturmak çok fazla iş. Bu yüzden bir sonraki adımda `Foundry`'yi geri getirip bir gemi filosu ve bir yığın parça oluşturacağız ve hepsini tek seferde ilişkilendireceğiz. İşte `Foundry` burada parlıyor!

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./3_Many To One The King of Relationships.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./5_ Setting Relations in Foundry.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
