# 🟦 The Two Sides of a Relation: Owning vs Inverse / Bir İlişkinin İki Yönü: Sahip Olan (Owning) ve Ters (Inverse) Taraf

Doctrine taco partisi için eğlenceli bir bilgi: Her ilişki iki farklı açıdan görülebilir. `Starship` örneğini ele alalım: birçok parçası var, yani `Starship` açısından bire-çoğ (one-to-many) bir ilişki. Ancak dürbünü ters çevirip `StarshipPart` tarafından bakarsak, bu defa çoğ-bire (many-to-one) bir ilişki görürüz. Bu bakış açılarından biri daima sahip olan (owning) taraf, diğeri ise ters (inverse) taraf olarak adlandırılır.

Şimdi diyebilirsiniz ki:

Tarafların nasıl adlandırıldığı neden umurumda olsun? Kedimi beslemem lazım!

Mittins’e üç dakika daha sabretmesini söyleyin: Bu bilgiler ilerde büyük bir baş ağrısını önleyebilir... ve tamamen kaçırılmış bir yemeği de!

## 🏷️ The Owning Side Unveiled / Sahip Olan (Owning) Tarafın Açığa Çıkışı

Öncelikle, hangi taraf sahip olan taraftır? Çoğ-bire (many-to-one) ilişkide: daima `ManyToOne` özniteliğini (attribute) barındıran taraftır, yani yabancı anahtar sütununun olacağı varlık (entity). Bizim örneğimizde bu, `StarshipPart`tır.

## 🎯 The Importance of Ownership / Sahipliğin Önemi

Peki bu neden önemli? İki sebebi var. Birincisi, `JoinColumn` yalnızca sahip olan tarafta yer alabilir. Bu da mantıklı: çünkü yabancı anahtar sütununu o kontrol eder. İkincisi, ilişkiyi yalnızca sahip olan tarafta ayarlayabilirsiniz. Şimdi göstereyim:

src/DataFixtures/AppFixtures.php dosyasını açın ve biraz oynayalım: `$starship = StarshipFactory::createOne();` satırının altına, iki adet `StarshipPart` nesnesi oluşturup bunları kaydedelim:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 11
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $starship = StarshipFactory::createOne();
        $part1 = new StarshipPart();
        $part1->setName('Warp Core');
        $part1->setPrice(1000);
        $part2 = new StarshipPart();
        $part2->setName('Phaser Array');
        $part2->setPrice(500);
        $manager->persist($part1);
        $manager->persist($part2);
// ... lines 25 - 52
    }
}
```

👉 Bu kod, iki parça oluşturur ve kaydeder; henüz ilişki ayarlanmadı.

Henüz herhangi bir ilişki belirtmedik, ama fixture'ları yükleyelim:

```bash
symfony console doctrine:fixtures:load
```

👉 Bu komut, fixture'ları yüklemeye çalışır.

Favori hatamız hemen çıkar:
`starship_id cannot be null`

Beklenildiği gibi.

## ↔️ The Owning vs Inverse Side in Action / Sahip Olan ve Ters Tarafın Uygulamada Gösterimi

Owning vs Inverse farkını göstermek için, `$starship`'ın sonuna `_real()` ekleyin:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 11
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $starship = StarshipFactory::createOne()->_real();
// ... lines 17 - 52
    }
}
```

👉 Bu kod, gerçek `Starship` nesnesini proxy'den çıkarır.

Foundry ile bir entity oluşturduğunuzda, aslında onu proxy nesnesi olarak sarar. Genellikle önemli değildir, ama bazen kafa karışıklığı yaratabilir. `_real()` çağırarak gerçek `Starship` nesnesini elde ederiz.

Şimdi bu parçaları bu yıldıza bağlama zamanı. Normalde şöyle deriz: `$part1->setStarship($starship);` — bu sahip olan tarafı ayarlamak olur. Ama bu sefer ters tarafı ayarlayalım:
`$starship->addPart($part1);` ve `$starship->addPart($part2);`:

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 11
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $starship = StarshipFactory::createOne()->_real();
// ... lines 17 - 22
        $manager->persist($part1);
        $manager->persist($part2);
        $starship->addPart($part1);
        $starship->addPart($part2);
// ... lines 28 - 55
    }
}
```

👉 Bu kod, parçaları gemiye ekler.

Az önce açıkladığım şeye göre, bu işe yaramamalı çünkü yalnızca ters tarafı ayarlıyoruz. Ama yine de fixture'ları yükleyelim:

```bash
symfony console doctrine:fixtures:load
```

Ve sürpriz! Hiç hata yok. Veritabanını kontrol edin:

```bash
symfony console doctrine:query:sql "SELECT * FROM starship_part"
```

Beklediğiniz gibi, her biri bir yıldıza bağlı iki yeni parça var.

Yani burada ne oldu? Yalnızca ilişkinin ters tarafını ayarladık ve yine de veritabanına kaydedildi. Az önce söylediğimin tam tersi!

## 🌀 The Plot Twist: Inverse Side Setting the Owning Side / Ters Tarafın Sahip Olan Tarafı Ayarlaması

`Starship` entity'sini açın ve `addPart()` metodunu bulun:

```php
// src/Entity/Starship.php
// ... lines 1 - 13
class Starship
{
// ... lines 16 - 159
    public function addPart(StarshipPart $part): static
    {
        if (!$this->parts->contains($part)) {
            $this->parts->add($part);
            $part->setStarship($this);
        }
        return $this;
    }
// ... lines 169 - 180
}
```

👉 Bu metot, ters tarafta olsak bile aslında sahip olan tarafı (`$part->setStarship($this);`) da ayarlıyor.

Aha! Bu metot, `$part->setStarship($this);` çağırıyor. Sahip olan tarafı ayarlıyor. Yani ters tarafı ayarladığımızda, `make:entity` komutunun ürettiği kendi kodumuz sahip olan tarafı da senkronize ediyor. Akıllıca, değil mi?

## ⚖️ Owning vs Inverse vs I don't Care / Sahip Olan vs Ters Taraf vs Beni İlgilendirmez

Sonuç şu: Her ilişkinin bir sahip olan (owning) ve bir ters (inverse) tarafı vardır. Ters taraf isteğe bağlıdır. `make:entity` komutu, ters tarafı oluşturmak isteyip istemediğimizi sormuştu ve evet dedik. Böylece bize çok pratik olan `$ship->getParts()` metodunu verdi.

Evet, teknik olarak ilişkiyi yalnızca sahip olan taraftan (`$starshipPart->setShip()`) ayarlayabilirsiniz, ama pratikte iki taraftan da ayarlayabilirsiniz çünkü kendi kodunuz iki tarafı da senkronize ediyor. Artık yeni bilginizle arkadaşlarınızı etkileyebilirsiniz ve sonra hemen unutabilirsiniz: pratikte kritik değil.

Geçici kodlarımızı temizleyin ve fixture'ları tazeleyin:

```bash
symfony console doctrine:fixtures:load
```

👉 Kod temizlendikten sonra fixture'lar tekrar yüklenir.

Sırada: `orphanRemoval`. İsmi kadar korkutucu değildir.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./6_Fetching a Relation's Data.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./8_Orphan Removal.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
