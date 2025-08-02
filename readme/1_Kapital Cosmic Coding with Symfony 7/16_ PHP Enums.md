### 🚀 PHP Enum’ları / PHP Enum'ları

### \[Creating an Enum] / \[Bir Enum Oluşturmak]

Projede biri tam ortada gereksinimleri değiştirdi! Yeni plana göre her geminin durumu şu üç seçenekten biri olmalı: `in progress`, `waiting` veya `completed`. `src/Repository/StarshipRepository.php` içinde gemiler zaten bir statüye sahipler – ancak bu şu anda serbest bir string ve her şey olabilir.

Üç geçerli statü olduğuna göre, bu bir PHP enum’u için mükemmel bir kullanım durumu.

`Model/` dizininde – bu enum başka bir yerde de olabilir ama biz düzenli kalmak için buraya koyuyoruz – yeni bir enum sınıfı oluşturun ve adını `StarshipStatusEnum` yapın.

```php
namespace App\Model;

enum StarshipStatusEnum: string
{
    case WAITING = 'waiting';
    case IN_PROGRESS = 'in progress';
    case COMPLETED = 'completed';
}
```

Bu kadar! Bir enum tam olarak budur: belirli "durumların" tek bir yerde merkezi olarak toplandığı bir yapı.

---

### 🛠️ Refactoring the Starship Class / Starship Sınıfını Yeniden Düzenlemek

`Starship` sınıfında, son parametre şu anda bir string durum. Bunu `StarshipStatusEnum` olarak değiştirin. Ayrıca `getStatus()` metodu da artık bir `StarshipStatusEnum` döndürecek.

---

### 🧪 Updating the Repository / Repository Güncellemesi

`StarshipRepository` içinde, artık string yerine enum kullanmanız gerekiyor. IDE'niz uyarı veriyor: “Bu argüman `StarshipStatusEnum` bekliyor ama sen string veriyorsun!”

Sakin olun. Şöyle değiştirin:

```php
use App\Model\StarshipStatusEnum;

return [
    new Starship(
        // ...
        StarshipStatusEnum::IN_PROGRESS
    ),
    new Starship(
        // ...
        StarshipStatusEnum::COMPLETED
    ),
    new Starship(
        // ...
        StarshipStatusEnum::WAITING
    ),
];
```

---

### 💥 Enum to String Error / Enum'u String'e Dönüştürme Hatası

Sayfayı yenilediğinizde şu hatayı alırsınız:

> object of class StarshipStatusEnum could not be converted to string

Bu mantıklı: `ship.status` artık bir enum ve direkt string’e çevrilemez.

---

### 🧼 Displaying Enum in Twig / Enum Değerini Twig’de Gösterme

En basit çözüm: `.value` kullanmak.

```twig
<p class="uppercase text-xs text-nowrap">{{ ship.status.value }}</p>
```

Çünkü string-backed bir enum kullandık, `value` özelliği tanımladığımız string değer olacaktır. Şimdi deneyin. Harika görünüyor! `in progress`, `completed`, `waiting`.

---

### ⏭️ What’s Next / Sırada Ne Var?

Bir sonraki adımda, bu son değişikliği daha zarif hâle getirmek için `Starship` sınıfında daha akıllı metotlar tanımlamayı öğreneceğiz. Ardından tasarımın son rötuşlarını yapacağız.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./15_Twig Partials & for Loops.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./17_Smart Model Methods & Making the Design Dynamic.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
