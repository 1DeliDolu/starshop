# ➕ Re-adding addDroid(): Hide that Join Entity! / addDroid()’u Yeniden Eklemek: Birleştirme Varlığını Gizle!

Kendinize şu soruyu soruyor olabilirsiniz:

Her seferinde bir droid’i bir yıldız gemisine eklemek için gerçekten bir `StarshipDroid` nesnesi mi oluşturmam gerekiyor?

Bence bu fazla zahmetli. Eski güzel günlerdeki gibi sadece `$ship->addDroid($droid)` çağırmak mümkün olmaz mı?

Evet! Henüz çalışmayacak, ama bu bizi hiç durdurmadı! Şimdi fixture’ları yükleyin:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, veritabanını örnek verilerle doldurur.

Hata!

```
Undefined property: App\Entity\Starship::$droids
```

Çok da şaşırtıcı değil, çünkü `addDroid()` metodunda artık olmayan `droids` özelliğini çağırıyoruz.

## Remaking addDroid() / addDroid()’u Yeniden Oluşturmak

Yapılacak ilk iş, `Starship`’in ilgili `Droid`’i zaten içerip içermediğini kontrol etmek. Bunun için özelliği `getDroids()` metodunu kullanacak şekilde değiştirin. Ama bekleyin, `$this->getDroids()->add()` burada işimize yaramaz. Bunun yerine kolları sıvayıp birleştirme varlığını doğrudan burada oluşturacağız:
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

İlişkinin sahibi tarafını ayarladık, ama şimdi diğer tarafı da senkronize edelim. Bunu `$droid->starshipDroids->add($starshipDroid)` çağırarak yapabiliriz. Fixture’ları tekrar çalıştırın:

```shell
symfony console doctrine:fixtures:load
```

👉 Fixture’lar tekrar yüklenir.

## Cascading the Persist / Persist İşlemini Zincirleme 

Şimdi yeni bir hata geldi. Doctrine’de bu hata oldukça yaygındır, fakat her zaman kolay anlaşılmaz:

```
A new entity was found through the relationship Starship#starshipDroids that was not configured to cascade persist for the entity StarshipDroid.
```

Bu, aslında şu anlama gelir: Yeni bir `StarshipDroid` nesnesi oluşturduk ve Doctrine’e ilişkili `Starship`’i kaydetmesini söyledik. Ama Doctrine’e, `StarshipDroid` nesnesinin kendisini de kaydetmesini söylemedik.

Sorun şu ki: Entity manager’a erişimimiz yok. Yani doğrudan `$entityManager->persist($starshipDroid)` diyemeyiz. Bunun yerine, `cascade=['persist']` denilen bir özelliğe güveneceğiz. Şimdi buna geçiyoruz.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./22_Hiding the Join Entity.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./24_Cascade Persist.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
