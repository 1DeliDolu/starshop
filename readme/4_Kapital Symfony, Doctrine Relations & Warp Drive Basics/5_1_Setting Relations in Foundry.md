# 🔗 Setting Relations in Foundry / Foundry'de İlişkileri Ayarlama

Şu anda birkaç parçamız ve birkaç uzay gemimiz var, ancak test veri filomuzú doldurmak için çok daha fazlasına ihtiyacımız var. Bu iş tamamen dostumuz Foundry için uygun.

## 📦 Manuel Kodu Kaldırma ve Foundry ile Parça Oluşturma

Manuel kodu kaldırıp, bunun yerine herhangi bir yerde `StarshipPartFactory::createMany(100)` kullanın:

```php
// src/DataFixtures/AppFixtures.php
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ... önceki gemiler ...
        StarshipFactory::createMany(20);
        \App\Factory\StarshipPartFactory::createMany(100);
    }
}
```

Fixtures'ları deneyin:

```bash
symfony console doctrine:fixtures:load
```

Hata!

> starship_id cannot be null in starship_part.

## ⚙️ StarshipPartFactory'de İlişki Ayarlama

Bu, `StarshipPartFactory`'deki `defaults()` metoduna kadar uzanır. Bu, her yeni `StarshipPart` oluşturulduğunda geçirilen verilerdir. Altın kural, `defaults()`'ın nesnedeki her gerekli özellik için bir anahtar döndürmesidir. Şu anda açıkça `starship` özelliğini eksik bırakıyoruz, o yüzden bunu ekleyelim.

`starship`'i `starship_id` değil, `StarshipFactory::randomOrCreate()` adlı kullanışlı bir metoda ayarlayın ve bir dizi geçirin:

```php
// src/Factory/StarshipPartFactory.php
protected function defaults(): array|callable
{
    $randomPartKey = self::faker()->randomKey(self::$partIdeas);
    $randomPart = [$randomPartKey, self::$partIdeas[$randomPartKey]];
    return [
        'name' => $randomPart[0],
        'notes' => $randomPart[1],
        'price' => self::faker()->randomNumber(5),
        'starship' => \App\Factory\StarshipFactory::randomOrCreate([
            'status' => \App\Model\StarshipStatusEnum::IN_PROGRESS,
        ]),
    ];
}
```

## 🎯 Parça Statüsü Ayarlama

Ana sayfamızda yalnızca 'devam eden' veya 'bekleyen' statüsündeki uzay gemilerini listeliyoruz. Bu parçaların 'devam eden' statüsündeki bir gemiye ilişkin olduğundan emin olmak için diziye `status` anahtarını `StarshipStatusEnum::IN_PROGRESS` olarak ekleyin.

Bu `randomOrCreate()` etkileyici bir metoddur: önce veritabanında bu kriterlere uyan bir Starship arar (bir "devam eden" gemi). Bulursa onu kullanır. Bulamazsa bu statüyle bir tane oluşturur.

Fixtures'ları şimdi deneyin:

```bash
symfony console doctrine:fixtures:load
```

Hata yok! Veritabanını kontrol edin:

```bash
symfony console doctrine:query:sql "SELECT * FROM starship_part"
```

Her biri rastgele bir Starship'e bağlı 100 parçamız var, bu 'devam eden' statüsünde bir Starship olmalıdır.

## 🎮 Foundry'de Daha Fazla Kontrol Alma

Peki ya daha fazla kontrole ihtiyacımız varsa? Tüm 100 parçayı aynı gemiye atamak istesek ne olur?

Bir gemi değişkeni alarak başlayın: `$ship = StarshipFactory::createOne()`:

```php
// src/DataFixtures/AppFixtures.php
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ... önceki gemiler ...
        StarshipFactory::createMany(20);

        $ship = StarshipFactory::createOne([
            'status' => StarshipStatusEnum::IN_PROGRESS,
        ]);

        \App\Factory\StarshipPartFactory::createMany(100, [
            'starship' => $ship,
        ]);
    }
}
```

Fixtures'ları tekrar yükleyin:

```bash
symfony console doctrine:fixtures:load
```

Ve bitti! Tüm parçalar artık aynı gemiye bağlı.

## 🤔 Foundry Plot Twist

İşte işlerin ilginçleştiği yer. `StarshipPartFactory`'de `randomOrCreate()` yerine `createOne()` kullanarak değiştirin:

```php
// src/Factory/StarshipPartFactory.php
protected function defaults(): array|callable
{
    // ...
    return [
        // ...
        'starship' => \App\Factory\StarshipFactory::createOne([
            'status' => \App\Model\StarshipStatusEnum::IN_PROGRESS,
        ]),
    ];
}
```

Fixtures'ları tekrar yükleyin ve gemileri sorgulayın:

```bash
symfony console doctrine:fixtures:load
symfony console doctrine:query:sql "SELECT COUNT(*) FROM starship"
```

Vay! Aniden bir filomuz var! 124 gemi tam olarak.

Her parça için `defaults()` çağrılır. Yani tüm 100 parça için bu satırı tetikliyor, bu da bir Starship oluşturup kaydediyor, override edildiği için hiç kullanılmasa bile.

## ✅ Çözüm: new() Kullanma

Çözüm? Bunu `StarshipFactory::new()` olarak değiştirin:

```php
// src/Factory/StarshipPartFactory.php
protected function defaults(): array|callable
{
    // ...
    return [
        // ...
        'starship' => \App\Factory\StarshipFactory::new([
            'status' => \App\Model\StarshipStatusEnum::IN_PROGRESS,
        ]),
    ];
}
```

Bu gizli sos: veritabanında bir nesne değil, factory'nin yeni bir örneğini oluşturur. Deneyin:

```bash
symfony console doctrine:fixtures:load
symfony console doctrine:query:sql "SELECT COUNT(*) FROM starship"
```

Mükemmel! 24 gemiye geri döndük.

## 🏭 Factory'ler Nesne Tarifleridir

Eğlenceli gerçek! Bu factory örneklerini nesneler oluşturmak için tarifler gibi kullanabiliriz. `StarshipFactory::new(['status' => StarshipStatusEnum::STATUS_IN_PROGRESS])` veritabanında bir nesne oluşturmaz. Hayır: `new()` factory'nin yeni bir örneği anlamına gelir. Ve bir özellik için bir factory geçtiğinizde, Foundry o nesneyi oluşturmayı geciktirir ve gerekirse yapar. Yani, yalnızca Starship override edilmezse "devam eden" statüde yeni bir Starship oluşturup kaydeder. Bu aslında Foundry'de ilişkileri ayarlarken en iyi uygulamadır: bunları bir factory örneğine ayarlayın.

## 🧹 Final Temizlik

Fixtures'ımızı override'ı kaldırarak temizleyin:

```php
// src/DataFixtures/AppFixtures.php
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ... önceki gemiler ...
        StarshipFactory::createMany(20);
        \App\Factory\StarshipPartFactory::createMany(100);
    }
}
```

Ve `randomOrCreate()`'a geri dönün:

```php
// src/Factory/StarshipPartFactory.php
protected function defaults(): array|callable
{
    // ...
    return [
        // ...
        'starship' => \App\Factory\StarshipFactory::randomOrCreate([
            'status' => \App\Model\StarshipStatusEnum::IN_PROGRESS,
        ]),
    ];
}
```

Çünkü, dürüst olmak gerekirse, oldukça kullanışlı bir metod.

Son kez fixtures'ları yükleyerek hiçbir şeyi bozmadığımızdan emin olun:

```bash
symfony console doctrine:fixtures:load
```

Hayır! Bir dahaki sefere daha çok çalışacağız.

## 🎯 Özetlenen Öğrenmeler

1. **`randomOrCreate()`**: Veritabanında kriterilere uyan kayıt arar, bulamazsa oluşturur
2. **`createOne()`**: Her zaman yeni kayıt oluşturur (tehlikeli!)
3. **`new()`**: Factory instance oluşturur, sadece gerektiğinde kayıt yaratır
4. **Factory relationships**: En iyi pratik `new()` veya `randomOrCreate()` kullanmak
5. **Foundry'nin gücü**: 100 parça ile ilişkilerini kolayca yönetebiliyoruz

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./5_Bundle Config Configuring the Cache Service.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./6_ How autowiring works.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
