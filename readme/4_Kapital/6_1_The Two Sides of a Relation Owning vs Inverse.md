# 🔄 The Two Sides of a Relation: Owning vs Inverse / İlişkinin İki Tarafı: Sahip Olan vs Ters Taraf

Doctrine taco partiniz için eğlenceli bir gerçek: Her ilişki iki farklı taraftan görülebilir. Starship'i ele alalım: birden fazla parçası var, bu da Starship perspektifinden bir one-to-many ilişkisi yapıyor. Ama teleskopu çevirip StarshipPart ucundan bakarsanız, many-to-one ilişkisi bulursunuz. Bu perspektiflerden biri her zaman sahip olan (owning) taraf olarak bilinir, diğeri ise ters (inverse) taraf.

Şimdi şöyle düşünüyor olabilirsiniz:

> Tarafların nasıl adlandırıldığını neden umursayayım? Kedimi beslemeye gitmem gerek!

Mittens'a 3 dakika sakin olmasını söyleyin: bu sizi gelecekte büyük bir baş ağrısından... ve tamamen kaçırılmış bir öğünden kurtarabilir.

## 🏆 Sahip Olan Taraf Açığa Çıkarıldı

Öncelikle, hangi taraf sahip olan taraftır? Many-to-one için: her zaman `ManyToOne` özniteliğine sahip olan taraftır, bu da foreign key sütununa sahip olacak entity'dedir. Bizim durumumuzda bu `StarshipPart`'tır.

## ⚖️ Sahipliğin Önemi

Ama bu neden önemli? İki nedeni var. Birincisi, `JoinColumn` sadece sahip olan tarafta yaşayabilir. Ve bu mantıklı: foreign key sütununu kontrol eder. İkincisi, ilişkinin yalnızca sahip olan tarafını ayarlayabilirsiniz. Göstereyim:

## 🧪 Test Kodları ile Demonstrasyon

`src/DataFixtures/AppFixtures.php` dosyasını açın ve biraz oynayalım:

```php
// src/DataFixtures/AppFixtures.php
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Test for owning vs inverse side
        $starship = StarshipFactory::createOne();
        $part1 = new \App\Entity\StarshipPart();
        $part1->setName('Warp Core');
        $part1->setPrice(1000);
        $part2 = new \App\Entity\StarshipPart();
        $part2->setName('Phaser Array');
        $part2->setPrice(500);
        $manager->persist($part1);
        $manager->persist($part2);

        // ... diğer fixtures kodları
    }
}
```

Henüz hiçbir ilişki ayarlamadım, ama umursamadan fixtures'ları yükleyelim:

```bash
symfony console doctrine:fixtures:load
```

Favori hatamız ortaya çıkıyor:

> starship_id cannot be null

Tamamen beklendiği gibi.

## 🎭 Sahip Olan vs Ters Taraf Eylemde

Sahip olan vs ters taraf sorunu göstermek için, `$starship`'in sonuna `_real()` ekleyin:

```php
// src/DataFixtures/AppFixtures.php
public function load(ObjectManager $manager): void
{
    $starship = StarshipFactory::createOne()->_real();
    // ...
}
```

Foundry ile bir entity oluşturduğunuzda, aslında onu proxy object adı verilen küçük bir hediyeye sarar. Bu genellikle önemli değildir, ama bazen biraz karışıklık yaratabilir. `_real()` çağırarak, proxy'yi açar ve gerçek Starship nesnesini alırız.

## 🔄 Ters Tarafı Test Etme

Bu parçaları bu starship'e bağlama zamanı. Normalde `$part1->setStarship($starship);` deriz, bu sahip olan tarafı ayarlar. Bu sefer ters tarafı ayarlamayı deneyin. Bu `$starship->addPart($part1);` ve `$starship->addPart($part2);` olur:

```php
// src/DataFixtures/AppFixtures.php
public function load(ObjectManager $manager): void
{
    $starship = StarshipFactory::createOne()->_real();
    $part1 = new \App\Entity\StarshipPart();
    $part1->setName('Warp Core');
    $part1->setPrice(1000);
    $part2 = new \App\Entity\StarshipPart();
    $part2->setName('Phaser Array');
    $part2->setPrice(500);
    $manager->persist($part1);
    $manager->persist($part2);
    $starship->addPart($part1);
    $starship->addPart($part2);

    // ... diğer fixtures kodları
}
```

Az önce açıkladığım şeye göre, bu çalışmamalı çünkü sadece ters tarafı ayarlıyoruz. Ama zarları atalım ve yine de fixtures'ları yükleyelim:

```bash
symfony console doctrine:fixtures:load
```

Ama sürpriz, sürpriz! Hata yok. Aslında, veritabanını kontrol ederseniz:

```bash
symfony console doctrine:query:sql "SELECT * FROM starship_part WHERE name IN ('Warp Core', 'Phaser Array')"
```

Kesinlikle, her biri bir starship'e ilişkili iki yeni parçamız var.

Peki, ne oluyor? Az önce ilişkinin sadece ters tarafını ayarladık ve yine de veritabanına kaydedildi. Bu az önce size söylediğimin tam tersi!

## 🎪 Plot Twist: Ters Taraf Sahip Olan Tarafı Ayarlıyor

Starship entity'sini açın ve `addPart()` metodunu bulun:

```php
// src/Entity/Starship.php
class Starship
{
    // ...
    public function addPart(StarshipPart $part): static
    {
        if (!$this->parts->contains($part)) {
            $this->parts->add($part);
            $part->setStarship($this);  // 👈 İşte sihir burada!
        }
        return $this;
    }
    // ...
}
```

Aha! Bu metod `$part->setStarship($this);` çağırıyor. Sahip olan tarafı ayarlıyor. Ters tarafı ayarladığımızda, `make:entity` komutu tarafından oluşturulan kendi kodumuz da sahip olan tarafı ayarlıyor. Akıllı kız, değil mi?

## 🤷‍♂️ Sahip Olan vs Ters vs Umurumda Değil

İşte çıkarılacak ders: her ilişkinin sahip olan bir tarafı ve ters bir tarafı vardır. Ters taraf isteğe bağlıdır. `make:entity` ters tarafı oluşturmak isteyip istemediğimizi sordu ve biz evet dedik. Bu bize süper kullanışlı `$ship->getParts()` metodunu verdi.

Yani evet, teknik olarak ilişkiyi yalnızca sahip olan taraftan ayarlayabilirsiniz (yani `$starshipPart->setShip()`), ama pratikte her iki taraftan da ayarlayabilirsiniz, çünkü kendi kodumuz her iki tarafı da senkronize ediyor. O yüzden gidin yeni bilginizle arkadaşlarınızı şaşırtın, sonra derhal unutun: pratikte kritik değil.

## 🧹 Temizlik

Buradaki geçici kodumuz temizleyin ve fixtures'ları yeniden yükleyerek işleri tazelein:

```php
// src/DataFixtures/AppFixtures.php
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        StarshipFactory::createOne([
            'name' => 'USS LeafyCruiser (NCC-0001)',
            'class' => 'Garden',
            'captain' => 'Jean-Luc Pickles',
            'status' => StarshipStatusEnum::IN_PROGRESS,
            'arrivedAt' => new \DateTimeImmutable('-1 day'),
        ]);
        StarshipFactory::createOne([
            'name' => 'USS Espresso (NCC-1234-C)',
            'class' => 'Latte',
            'captain' => 'James T. Quick!',
            'status' => StarshipStatusEnum::COMPLETED,
            'arrivedAt' => new \DateTimeImmutable('-1 week'),
        ]);
        StarshipFactory::createOne([
            'name' => 'USS Wanderlust (NCC-2024-W)',
            'class' => 'Delta Tourist',
            'captain' => 'Kathryn Journeyway',
            'status' => StarshipStatusEnum::WAITING,
            'arrivedAt' => new \DateTimeImmutable('-1 month'),
        ]);
        StarshipFactory::createMany(20);
        \App\Factory\StarshipPartFactory::createMany(100);
    }
}
```

```bash
symfony console doctrine:fixtures:load
```

Tamam, sırada: `orphanRemoval`. Kulağa geldiği kadar kötü değil.

## 🎯 Öğrenilen Dersler

1. **Owning Side**: `ManyToOne` tarafı (StarshipPart) - foreign key'e sahip olan taraf
2. **Inverse Side**: `OneToMany` tarafı (Starship) - isteğe bağlı, kolaylık için
3. **JoinColumn**: Sadece owning side'da tanımlanabilir
4. **Relationship Setting**: Teknik olarak sadece owning side'dan set edilebilir
5. **make:entity Magic**: `addPart()` metodu otomatik olarak owning side'ı da set ediyor
6. **Praktik Sonuç**: Her iki taraftan da set edebiliriz, kod otomatik senkronize ediyor

### 🔍 **Önemli Detay:**

`$starship->addPart($part)` çağrıldığında, arkada `$part->setStarship($this)` de çalışıyor. Bu yüzden "sadece inverse side'ı set ettik" sanıyoruz ama aslında owning side da set ediliyor!

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./6_Fetching a Relation's Data.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./7_orphanRemoval.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
