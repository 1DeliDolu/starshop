# ➕ Re-adding addDroid(): Hide that Join Entity! / addDroid()'u Yeniden Eklemek: Birleştirme Varlığını Gizle!

Kendinize şu soruyu soruyor olabilirsiniz:

Her seferinde bir droid'i bir yıldız gemisine eklemek için gerçekten bir `StarshipDroid` nesnesi mi oluşturmam gerekiyor?

Bence bu fazla zahmetli. Eski güzel günlerdeki gibi sadece `$ship->addDroid($droid)` çağırmak mümkün olmaz mı?

Evet! Henüz çalışmayacak, ama bu bizi hiç durdurmadı! Şimdi fixture'ları yükleyin:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, veritabanını örnek verilerle doldurur.

Hata!

```
Undefined property: App\Entity\Starship::$droids
```

Çok da şaşırtıcı değil, çünkü `addDroid()` metodunda artık olmayan `droids` özelliğini çağırıyoruz.

## Remaking addDroid() / addDroid()'u Yeniden Oluşturmak

Yapılacak ilk iş, `Starship`'in ilgili `Droid`'i zaten içerip içermediğini kontrol etmek. Bunun için özelliği `getDroids()` metodunu kullanacak şekilde değiştirin. Ama bekleyin, `$this->getDroids()->add()` burada işimize yaramaz. Bunun yerine kolları sıvayıp birleştirme varlığını doğrudan burada oluşturacağız:
`$starshipDroid = new StarshipDroid()`, ardından
`$starshipDroid->setDroid($droid)` ve
`$starshipDroid->setStarship($this)` ile ilişkileri ayarlayacağız:

```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 207
    public function addDroid(Droid $droid): static
    {
        if (!$this->getDroids()->contains($droid)) {
            $starshipDroid = new StarshipDroid();
            $starshipDroid->setDroid($droid);
            $starshipDroid->setStarship($this);
            $this->starshipDroids->add($starshipDroid);
        }
        return $this;
    }
// ... lines 219 - 260
}
```

👉 Bu yöntem, eğer droid daha önce eklenmemişse bir `StarshipDroid` nesnesi oluşturur ve ilişkilendirir.

İlişkinin sahibi tarafını ayarladık, ama şimdi diğer tarafı da senkronize edelim. Bunu `$droid->starshipDroids->add($starshipDroid)` çağırarak yapabiliriz. Fixture'ları tekrar çalıştırın:

```shell
symfony console doctrine:fixtures:load
```

👉 Fixture'lar tekrar yüklenir.

## Cascading the Persist / Persist İşlemini Zincirleme

Şimdi yeni bir hata geldi. Doctrine'de bu hata oldukça yaygındır, fakat her zaman kolay anlaşılmaz:

```
A new entity was found through the relationship Starship#starshipDroids that was not configured to cascade persist for the entity StarshipDroid.
```

Bu, aslında şu anlama gelir: Yeni bir `StarshipDroid` nesnesi oluşturduk ve Doctrine'e ilişkili `Starship`'i kaydetmesini söyledik. Ama Doctrine'e, `StarshipDroid` nesnesinin kendisini de kaydetmesini söylemedik.

Sorun şu ki: Entity manager'a erişimimiz yok. Yani doğrudan `$entityManager->persist($starshipDroid)` diyemeyiz. Bunun yerine, `cascade=['persist']` denilen bir özelliğe güveneceğiz. Şimdi buna geçiyoruz.

## Adding cascade=['persist'] / cascade=['persist'] Eklemek

`Starship` entity'sinde `starshipDroids` ilişkisini bulun ve `cascade=['persist']` seçeneğini ekleyin:

```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 52
    /**
     * @var Collection<int, StarshipDroid>
     */
    #[ORM\OneToMany(targetEntity: StarshipDroid::class, mappedBy: 'starship', orphanRemoval: true, cascade: ['persist'])]
    private Collection $starshipDroids;
// ... lines 58 - 260
}
```

👉 `cascade=['persist']` seçeneği, Starship kaydedildiğinde ilişkili StarshipDroid nesnelerinin de otomatik olarak kaydedilmesini sağlar.

Şimdi fixture'ları tekrar çalıştırın:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu sefer hata olmadan yüklenecek!

## Synchronizing Both Sides / Her İki Tarafı Senkronize Etmek

İlişkinin her iki tarafını da senkronize etmek için `addDroid()` metodunu güncelleyin:

```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 232
    public function addDroid(Droid $droid): static
    {
        if (!$this->getDroids()->contains($droid)) {
            $starshipDroid = new StarshipDroid();
            $starshipDroid->setDroid($droid);
            $starshipDroid->setStarship($this);
            $this->starshipDroids->add($starshipDroid);
            $droid->getStarshipDroids()->add($starshipDroid);
        }
        return $this;
    }
// ... lines 245 - 260
}
```

👉 Bu sayede hem Starship hem de Droid tarafındaki koleksiyonlar güncel kalır.

Artık `$ship->addDroid($droid)` çağrısı yaparak kolayca droid ekleyebilirsiniz ve join entity tamamen gizlenmiş olur!
