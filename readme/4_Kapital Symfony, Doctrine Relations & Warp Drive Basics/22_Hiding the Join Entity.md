# 🫥 Hiding the Join Entity / Birleştirme Varlığını Gizlemek

Anasayfayı yenileyin ve... hata! Anasayfa şablonunda `ship.droidNames` kullanıyoruz. Bunun aslında `$starship->getDroidNames()` çağırdığını biliyoruz. Ama bu, az önce sildiğimiz `droids` özelliğini hâlâ kullanmaya çalışıyor. Önce bunu düzeltelim.

## Isn't It Still a Relationship between Starship and Droid? / Bu Hâlâ Starship ve Droid Arasında Bir İlişki Değil mi?

Bunu, `$ship->starshipDroids` üzerinde döngü kurup her birinden ismi alarak yamalayabiliriz. Ama bir dakika durun! Bu yöntemi bir kenara bırakın. Daha geniş düşünürseniz, bu hâlâ `Starship` ve `Droid` arasında bir ilişkidir. O halde, `$ship->getDroids()` çağırdığınızda size yine bir `Droid` nesneleri koleksiyonu döndürse güzel olmaz mıydı? Bu yapılabilir mi? Kesinlikle, dostum, kesinlikle.

## Fixing the getDroids() Method / getDroids() Yöntemini Düzeltmek

Her bir `StarshipDroid` koleksiyonunu bir `Droid` nesnesine dönüştürmek için `$this->starshipDroids->map()` kullanın:

```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 202
    public function getDroids(): Collection
    {
        return $this->starshipDroids->map(fn (StarshipDroid $starshipDroid) => $starshipDroid->getDroid());
    }
// ... lines 207 - 257
}
```

👉 Bu yöntem, artık tekrar bir `Droid` nesneleri koleksiyonu döndürüyor.

Artık bu metoda sahip olduğumuza göre, aşağıda `getDroidNames()` içinde `droids` özelliğini kullanmak yerine `getDroids()` metoduna geçiş yapın:

```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 223
    public function getDroidNames(): string
    {
        return implode(', ', $this->getDroids()->map(fn(Droid $droid) => $droid->getName())->toArray());
    }
// ... lines 228 - 257
}
```

👉 Bu yöntem, tüm droid isimlerini virgül ile ayırarak birleştirir.

Anasayfa şablonuna dönüp sayfayı yenileyin. Başarılı! Bir geminin droidlerini almak hâlâ kolay. Ve geri kalan kodlarımızı değiştirmemize gerek kalmadı.

## 🛡️ Act 5: Future-Proofing Droids / Droid’leri Geleceğe Hazırlamak

`Droid` varlığını açıp `getStarships()` metodunu bulun. Bu yöntemi henüz kullanmadık ama bunu da düzeltelim. Bu da bir `Starship` nesneleri koleksiyonu döndürmeli. Aynı `map()` yöntemini kullanarak `StarshipDroid` koleksiyonunu bir `Starship` nesneleri koleksiyonuna dönüştürün:

```php
// src/Entity/Droid.ph
// ... lines 1 - 10
class Droid
{
// ... lines 13 - 66
    public function getStarships(): Collection
    {
        return $this->starshipDroids->map(fn (StarshipDroid $starshipDroid) => $starshipDroid->getStarship());
    }
// ... lines 71 - 119
}
```

👉 Bu yöntem, ilgili tüm `Starship` nesnelerini döndürür.

## 🪄 Hiding the Join Entity When We Create the Relationship / İlişki Oluştururken Birleştirme Varlığını Gizlemek

Ele almamız gereken son bir şey kaldı. İlişkiyi oluştururken, bu birleştirme varlığını (join entity) oluşturmak için hâlâ biraz ek iş yapmamız gerekiyor. Bu, basitçe `$ship->addDroid($droid)` kadar kolay değil. Bunu bir sonraki bölümde ele alacağız.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./21_Persisting the More Complex Many-to-Many Relationship.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./23_Re-adding addDroid() Hide that Join Entity.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
