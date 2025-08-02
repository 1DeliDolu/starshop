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
