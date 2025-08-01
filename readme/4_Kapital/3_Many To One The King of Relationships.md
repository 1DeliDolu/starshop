## 🧩 Many To One: The King of Relationships / Çoktan Bire: İlişkilerin Kralı

Pekâlâ dostlar, `Starship` ve `StarshipPart` varlıklarını başarıyla oluşturduk ve... veritabanında gayet güzel duruyorlar. Ama işte bilmece: Bu parçaları ilgili yıldız gemisine nasıl bağlayacağız? Her `StarshipPart`'a hakkı olan bir `Starship` yuvası nasıl vereceğiz? İşte burada sihirli `make:entity` komutu yeniden devreye giriyor. Ne gösteriş ama. Terminalinizi açın ve şunu çalıştırın:

```bash
symfony console make:entity
```

👉 Bu komut, yeni bir varlık oluşturmak veya mevcut bir varlığı güncellemek için kullanılır.

## 🧠 Building Relationships: Think Objects, Not IDs / İlişkileri Kurmak: Kimlikleri Değil Nesneleri Düşünün

Eğer geleneksel veritabanı mantığıyla düşünüyorsanız, `starship_part` tablosunda bir `starship_id` sütunu oluşacağını hayal edersiniz. Ve evet, oluşacak ama Doctrine'de böyle düşünmüyoruz. Bunun yerine nesneleri ilişkilendirmeye odaklanıyoruz. Yani `StarshipPart` varlığını güncelleyerek bir `Starship` alanı ekliyoruz.

Alanı adlandırırken, buna `starshipId` demeyin. Doctrine sınıflar ve nesneler üzerinden düşünmemizi ister. Ve çünkü bir `StarshipPart` bir `Starship`'e ait olacak, `StarshipPart` varlığına bir `starship` özelliği ekleyin.

Alan tipi için, sahte bir tür olan `relation` kullanın. Bu, bir sihirbazı başlatır! Hangi sınıfla ilişkilendiriyoruz? Hep birlikte söyleyelim: `Starship`.

## 🎯 Choosing the Right Relationship Type / Doğru İlişki Türünü Seçmek

Sihirbaz bizi dört farklı ilişki türü üzerinden yönlendirir. Açıklamaları kontrol edin: Bizim istediğimiz `ManyToOne`, yani her parça bir `Starship`'e ait olacak ve her `Starship` birçok parçaya sahip olabilecek.

`starship` özelliğinin `null` olup olamayacağı sorulduğunda, "hayır" diyeceğiz. Her parçanın bir `Starship`'e ait olmasını istiyoruz: Rastgele dolaşan parçalar olmaz.

## 🛠️ Adding Convenience with a New Property / Kolaylık Sağlamak için Yeni Özellik Eklemek

Sihirbaz ilginç bir soru sorar:

`$starship->getParts()` diyebilmek için `Starship` varlığına yeni bir özellik eklemek ister misiniz?

Bu tamamen isteğe bağlı, ama bir geminin tüm parçalarını bu kadar kolayca almak hoş olur. Ayrıca hiçbir olumsuz yanı yok. Yani bu benim için "evet". Özelliği `parts` olarak adlandırın: kısa ve öz. `orphan removal` için "hayır" deyin. Bunu sonra ele alacağız.

Enter'a basarak işlemi tamamlayın. Ben kaydetmeden önce commit ettim, bu yüzden değişiklikleri kontrol etmek için şunu çalıştıracağım:

```bash
git status
```

👉 Bu komut, hangi dosyaların değiştiğini ve izlenip izlenmediğini gösterir.

## 🆕 New Properties in StarshipPart and Starship / StarshipPart ve Starship İçindeki Yeni Özellikler

Görünüşe göre her iki varlık da güncellendi! `StarshipPart` içinde yeni bir `starship` özelliği var. Ama bu kez `ORM\Column` yerine `ORM\ManyToOne` var ve değeri bir `Starship` nesnesi olacak. Ayrıca yepyeni `getStarship()` ve `setStarship()` metodlarımız var:

```php
// src/Entity/StarshipPart.php

// ... lines 1 - 10
class StarshipPart
{
// ... lines 13 - 28
    #[ORM\ManyToOne(inversedBy: 'parts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Starship $starship = null;
// ... lines 32 - 73
    public function getStarship(): ?Starship
    {
        return $this->starship;
    }
    public function setStarship(?Starship $starship): static
    {
        $this->starship = $starship;
        return $this;
    }
}
```
👉 Bu kod, her `StarshipPart` örneğinin bir `Starship` nesnesine ait olmasını sağlar.

`Starship` tarafında ise `ORM\OneToMany` ile yeni bir `parts` özelliği var. Aşağıya doğru kaydırınca işe yarar bir `getParts()` metodu görüyoruz. Ama `setParts()` yerine bize `addPart()` ve `removePart()` metodları verilmiş:

```php

//src/Entity/Starship.php


// ... lines 1 - 6
use Doctrine\Common\Collections\Collection;
// ... lines 8 - 13
class Starship
{
// ... lines 16 - 41
    /**
     * @var Collection<int, StarshipPart>
     */
    #[ORM\OneToMany(targetEntity: StarshipPart::class, mappedBy: 'starship')]
    private Collection $parts;
// ... lines 47 - 151
    /**
     * @return Collection<int, StarshipPart>
     */
    public function getParts(): Collection
    {
        return $this->parts;
    }
    public function addPart(StarshipPart $part): static
    {
        if (!$this->parts->contains($part)) {
            $this->parts->add($part);
            $part->setStarship($this);
        }
        return $this;
    }
    public function removePart(StarshipPart $part): static
    {
        if ($this->parts->removeElement($part)) {
            // set the owning side to null (unless already changed)
            if ($part->getStarship() === $this) {
                $part->setStarship(null);
            }
        }
        return $this;
    }
}
```

👉 Bu kod, `Starship` nesnesinden ilişkili tüm `StarshipPart` öğelerine erişmenizi ve onları yönetmenizi sağlar.

Yapıcı metodda `parts` özelliğini `new ArrayCollection()` ile başlatmışız:

```php
//src/Entity/Starship.php

// ... lines 1 - 5
use Doctrine\Common\Collections\ArrayCollection;
// ... lines 7 - 13
class Starship
{
// ... lines 16 - 47
    public function __construct()
    {
        $this->parts = new ArrayCollection();
    }
// ... lines 52 - 180
}
```
👉 Bu kod, `parts` koleksiyonunun boş ama kullanılabilir bir `ArrayCollection` örneği ile başlamasını sağlar.

Bir düşünün: `OneToMany` ve `ManyToOne` aslında aynı ilişkinin iki farklı görünümüdür. Bir parça bir yıldız gemisine aitse, o yıldız gemisi birçok parçaya sahiptir. Biz tek bir ilişki ekledik ama onu iki farklı bakış açısından görebiliyoruz.

## ⚙️ Updating the Database / Veritabanını Güncelleme

Ama henüz işimiz bitmedi. Çünkü `make:entity` yeni özellikler ekledi, muhtemelen veritabanımızı güncellememiz gerekecek. Bir migration (göç) oluşturun:

```bash
symfony console make:migration
```

👉 Bu komut, veritabanı şemasındaki değişiklikleri algılayıp bir migration dosyası oluşturur.

## 🔍 Checking Out the Migration / Migration Dosyasını İncelemek

Bu benim favori migration'larımdan biri. `starship_part` tablosunu değiştirerek `starship_id` sütunu ekliyor; bu da `starship` tablosuna bir yabancı anahtar. Bu, Doctrine'in zekasından kaynaklanıyor. `StarshipPart`'a bir `starship` özelliği ekledik ama Doctrine bunun `starship_id` adlı bir sütun olması gerektiğini biliyordu. Bunu nasıl ayarladığını bir sonraki bölümde göreceğiz. Migration'ı çalıştıralım:

```bash
symfony console doctrine:migrations:migrate
```

👉 Bu komut, oluşturulan migration dosyasını çalıştırarak veritabanı şemasını günceller.

## 💥 Preparing for the Migration / Migration'a Hazırlık

Ve patladı!

```
Column "starship_id" in table "starship_part" cannot be null.
```

`starship_part` tablosunu hatırlıyor musunuz? Zaten içinde 50 satır var! Migration yeni bir `starship_id` sütunu eklemeye çalışıyor ve onu `null` yapıyor. Ama bu `nullable: false` yüzünden izin verilmiyor. Bu 50 satırı şu komutla temizleyin:

```bash
symfony console doctrine:query:sql "DELETE FROM starship_part"
```

👉 Bu SQL komutu, `starship_part` tablosundaki tüm satırları siler.

Sonra migration'ı tekrar çalıştırın:

```bash
symfony console doctrine:migrations:migrate
```

👉 Bu komut, migration işlemini başarıyla tamamlar.

## 🔗 Next Up: Connecting the Dots / Sıradaki Adım: Noktaları Birleştirmek

Peki bir `StarshipPart` nesnesini bir `Starship` ile nasıl ilişkilendiririz? Kemerlerinizi bağlayın, çünkü sıradaki konu bu!
