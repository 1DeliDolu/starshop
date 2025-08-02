# 🛸 We Have a Database... but no Tables! / Bir Veritabanımız Var... Ama Hiç Tablo Yok!

Doctrine ORM, veritabanındaki tabloları temsil etmek için PHP sınıflarını kullanır; örneğin ürünler için bir tabloya ihtiyacınız varsa, bir `Product` sınıfı oluşturursunuz. Doctrine bu sınıflara "entity" (varlık) der, ancak aslında bunlar standart, sıradan PHP sınıflarıdır. Sıradanlık iyidir!

StarShop uygulamamızda, yıldız gemilerini izlememiz gerekiyor... yani bir `Starship` tablosuna ihtiyacımız var... yani bir `Starship` entity sınıfına ihtiyacımız var. Bir yıldız gemisi nasıl görünür? Son eğitimde, `src/Model` dizininde bir `Starship` model sınıfı oluşturduk. Açıp bakalım. Her `Starship` nesnesinin bir `id`, `name`, `class`, `captain`, `status` ve `arrivedAt` özelliğine sahip olduğuna karar vermiştik.

Bu sınıf neredeyse bir Doctrine entity'si: sadece, Doctrine'in bu sınıfı veritabanındaki bir tabloyla nasıl eşleyeceğini anlamasına yardımcı olacak bazı yapılandırmalar eksik. Bunu elle kolayca ekleyebiliriz. Ama... bunu bizim için yapabilecek bir aracımız var: `MakerBundle`!

## 🛠️ make\:entity / make\:entity Komutu

Şunu çalıştırın:

```bash
symfony console make:entity
```

👉 Bu komut, bir entity (varlık) sınıfı oluşturur.

İsim olarak `Starship` yazın. Symfony UX Turbo kullanmıyoruz, bu nedenle o soruya hayır yanıtını verin. Bu işlem, `Entity/` dizininde bir `Starship` sınıfı ve bir `StarshipRepository` sınıfı oluşturdu. Ona sonra değineceğiz.

Ama işimiz bitmedi! Bu komut harika: entity'nin hangi özelliklere (veya sütunlara) sahip olması gerektiğini etkileşimli olarak soruyor. `Starship` modeline geri dönün ve neler gerektiğini kontrol edin. `MakerBundle`, `id` özelliğini otomatik olarak ekler, bu yüzden doğrudan `name` ile başlayın. Alan tipi? Varsayılan olan `string`'i kullanın. Alan uzunluğu? `255` uygundur. Bu alan veritabanında `null` olabilir mi? Hayır, her `Starship`'in bir `name` alanı olmalı.

Sıradaki `class`, `name` gibi olacak... sonra `captain`, bu da basit bir `string`. Sırada `status` var. Doctrine varsayılan olarak `string` kullanır ama... `Starship` modelimize bakarsanız, `status` bir `enum`. Bunu bir sütunla nasıl eşleştirebiliriz? Terminalde `?` tuşuna basarak ekleyebileceğimiz tüm farklı türleri görebilirsiniz. En altta... `enum`! Onu kullanın. `Enum` sınıfı? `App\Model\StarshipStatusEnum` sınıf adının tamamını yazın.

Bu alan birden fazla değer saklayabilir mi? Hayır, bir `Starship` aynı anda yalnızca bir `status`'e sahip olabilir. Bu alan `null` olabilir mi? Hayır!

Son olarak `arrivedAt` alanını ekleyin. Harika! `Maker`, varsayılan olarak `datetime_immutable` türünü seçer, çünkü alan adını `At` ile bitirdik. Akıllıca! Bu alan `null` olabilir mi? Hayır.

## 🧱 \[ORM\Entity] / \[ORM\Entity] Özniteliği

Yeni oluşturulan `Starship` entity'sine göz atalım: `src/Entity/` içinde.

Görünüşe göre bu, bazı özel PHP öznitelikleriyle birlikte gelen standart bir PHP sınıfı:

Sınıfın üzerindeki `#[ORM\Entity]` özniteliği, Doctrine'e bu sınıfın sadece sıradan bir PHP sınıfı değil, aynı zamanda veritabanındaki bir tabloyla eşleştirilecek bir `entity` olduğunu söyler. Tablo adı özelleştirilebilir ama biz varsayılan olan, sınıf adının yılan biçimli hali olan `starship` adını kullanacağız.

## 📋 \[ORM\Column] / \[ORM\Column] Özniteliği

Özelliklere göz atın: her biri `#[ORM\Column]` özniteliğine sahip. Bu, Doctrine'e bu özelliklerin tabloda birer sütun olduğunu bildirir. Tür konusunda, Doctrine akıllıdır ve tür ipucundan tahmin eder. Örneğin, `id` bir `integer`, `name` bir `string`, `arrivedAt` bir `timestamp` türü olacaktır. Güzel!

---

```php
//src/Entity/Starship.php
// ... lines 1 - 8
#[ORM\Entity(repositoryClass: StarshipRepository::class)]
class Starship
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(length: 255)]
    private ?string $name = null;
    #[ORM\Column(length: 255)]
    private ?string $class = null;
    #[ORM\Column(length: 255)]
    private ?string $captain = null;
    #[ORM\Column(enumType: StarshipStatusEnum::class)]
    private ?StarshipStatusEnum $status = null;
    #[ORM\Column]
    private ?\DateTimeImmutable $arrivedAt = null;
// ... lines 31 - 95
}
```

👉 Bu sınıf, bir `Starship` varlığını temsil eder ve `ORM\Entity` özniteliği ile bir veritabanı tablosuna karşılık gelir.

---

`id` alanı, onun bir birincil anahtar olduğunu ve veritabanının bunu otomatik artan bir tamsayı olarak oluşturması gerektiğini belirten bazı ek özniteliklere sahiptir.

Ayrıca, `string` sütunlardaki `length` argümanını kaldırabiliriz: bu zaten varsayılandır.

---

```php
//src/Entity/Starship.php
// ... lines 1 - 9
class Starship
{
// ... lines 12 - 16
    #[ORM\Column]
    private ?string $name = null;
    #[ORM\Column]
    private ?string $class = null;
    #[ORM\Column]
    private ?string $captain = null;
// ... lines 25 - 109
}
```

👉 Bu özelliklerin her biri veritabanında birer `string` sütundur ve `length` parametresine gerek yoktur.

---

`status` özelliği bir `StarshipStatusEnum` türündedir ama Doctrine bunu veritabanında bir `string` olarak saklayacaktır. Harika! `enumType` argümanını bile kaldırabiliriz: Doctrine bunu özellik türünden de anlayabilir!

---

```php
//src/Entity/Starship.php
// ... lines 1 - 9
class Starship
{
// ... lines 12 - 25
    #[ORM\Column]
    private ?StarshipStatusEnum $status = null;
// ... lines 28 - 109
}
```

👉 `status` alanı bir `enum` türünde olsa da Doctrine bunu `string` olarak işler, `enumType` belirtmeye gerek kalmaz.

---

Aşağıda, maker aracı tüm özellikler için getter ve setter metodlarını üretti. Eski `Starship` modelimizde iki ekstra metot vardı: `getStatusString()` ve `getStatusImageFilename()`. Bunları model sınıfından kopyalayın... ve entity sınıfının en altına yapıştırın!

---

```php
//src/Entity/Starship.php
// ... lines 1 - 9
class Starship
{
// ... lines 12 - 96
    public function getStatusString(): string
    {
        return $this->status->value;
    }
    public function getStatusImageFilename(): string
    {
        return match ($this->status) {
            StarshipStatusEnum::WAITING => 'images/status-waiting.png',
            StarshipStatusEnum::IN_PROGRESS => 'images/status-in-progress.png',
            StarshipStatusEnum::COMPLETED => 'images/status-complete.png',
        };
    }
}
```

👉 Bu metotlar, `status` değerini metin ve resim olarak döndürmek için kullanılır.

---

## 🧪 Schema Validation / Şema Doğrulama

Çalışmamızı iki kez kontrol etmek için terminalde şunu çalıştırın:

```bash
symfony console doctrine:schema:validate
```

👉 Bu komut, Doctrine’in entity sınıfımızı doğru şekilde tanıyıp tanımadığını kontrol eder.

Bu, Doctrine’in özniteliklerimizi görebildiği ve okuyabildiği anlamına gelir. Peki ya... veritabanımız hala eşitlenmemiş mi?

Bir `entity` sınıfımız var... ama veritabanında henüz bir `starship` tablomuz yok.

Bu tabloyu veritabanına eklemenin birkaç yolu var ama en iyi yol: `migrations`. Sıradaki adım bu olacak!

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./2_1_DSN.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./4_Migrations.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
