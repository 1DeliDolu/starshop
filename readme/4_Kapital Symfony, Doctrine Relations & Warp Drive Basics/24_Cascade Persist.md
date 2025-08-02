# 🚦 Cascade Persist / Kademeli Persist

Bu hataya göz at: tam bir baş belası!

`Starship.droids` ilişkisi üzerinden bulunan bir varlık, `StarshipDroid` varlığı için `cascade persist` işlemleriyle yapılandırılmamış.

Bunu senin için çevireyim:

Hey, bu `Starship`'i kaydediyorsun ve ona bağlı bir `StarshipDroid` var. Harika, ama bana `StarshipDroid`'i de kaydetmemi söylemeyi unuttun. Ne yapmamı istersin?

Ama yine de, `Starship` içinden, entity yöneticisini çağırıp `$manager->persist($starshipDroid)` diyemeyiz.

cascade=\['persist'] Gücünden Yararlanmak
Çözüm, `cascade persist` denen bir şeyi kullanmaktır.

`$starshipDroids` özelliğine yukarı kaydır, ve `OneToMany`'yi bul. Yeni bir seçenek ekle: `cascade`. Bunu manuel olarak yazacağım. `persist` içeren bir dizi olarak ayarla:


```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 53
    #[ORM\OneToMany(targetEntity: StarshipDroid::class, mappedBy: 'starship', cascade: ['persist'])]
    private Collection $starshipDroids;
// ... lines 56 - 260
}
```

👉 Bu kodda, `cascade: ['persist']` ile ilişkiye eklenen tüm nesnelerin de otomatik olarak kaydedilmesi sağlanır.

Burada bir domino etkisi oluşturuyoruz. Eğer biri bu `starship`'i kaydederse, bu işlemi ilişkilere de kademeli olarak uygulayacağız.

Ama dikkatli ol: Bu gücü akıllıca kullan. Kodu daha otomatik yapar, bu harika, ama hata tespitini de zorlaştırabilir.

Ama bu durumda, tam ihtiyacımız olan çözüm bu.

Tekrar fixture’ları çalıştır:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, uygulamanın fixture verilerini yükler.

## 🛠️ Back to Adding Droids / Droid Eklemeye Geri Dönüş

Tekrar iş başındayız. Artık tekrar `ship->addDroid()` kullanabiliriz. Ama ben yine de droidleriyle birlikte bir filo `starship` oluşturmak istiyorum.

Tüm manuel kodu kaldır ve `StarshipFactory`'deki `droids` özelliğini geri getir:


```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 13
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 18 - 51
        DroidFactory::createMany(100);
        StarshipFactory::createMany(100, fn() => [
            'droids' => DroidFactory::randomRange(1, 5),
        ]);
        StarshipPartFactory::createMany(100);
    }
}
```

👉 Bu kod, her bir `Starship` için 1 ile 5 arası rastgele droid ilişkilendirir.

Yeniden fixture’ları çalıştır:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, yeni veri setini veritabanına tekrar yükler.

Tahmin et ne oldu? Çalıştı!

Arka planda Foundry, her bir droid için her bir `Starship` üzerinde `addDroid()` çağırıyor. Ve az önce `addDroid()` fonksiyonunun tekrar çalıştığını kanıtladık.

Artık `StarshipDroid` join entity'sinin oluşturulması tüm kod tabanımızdan gizlendi!

## 🎛️ Finer Control with assignedAt / assignedAt ile Daha Hassas Kontrol

Ama, bir droid'i bir starship'e ekleyip `assignedAt` özelliğini kontrol etmek istersen ne olacak? `Starship` içinde `addDroid()` metoduna bir `DateTimeImmutable` argümanı ekle. Esnek olsun diye bunu isteğe bağlı yap. Sonra, `StarshipDroid`'i oluşturduktan sonra `$assignedAt` gönderildiyse ayarla:


```php
// src/Entity/Starship.php
// ... lines 1 - 15
class Starship
{
// ... lines 18 - 207
    public function addDroid(Droid $droid, \DateTimeImmutable $assignedAt = null): static
    {
        if (!$this->getDroids()->contains($droid)) {
// ... lines 211 - 213
            if ($assignedAt) {
                $starshipDroid->setAssignedAt($assignedAt);
            }
// ... line 217
        }
// ... lines 219 - 220
    }
// ... lines 222 - 263
}
```

👉 Bu kod, droid atanırken bir tarih belirlemenize olanak tanır.

Güzel... ama ufak bir sorun var. Foundry, `assignedAt` alanını kontrol etmemize izin vermiyor. Yani bazı droidleri belirli bir zamanda atamak istersen, bunu manuel olarak yapmalısın.

## 👁️ Displaying assignedAt / assignedAt'ı Görüntüleme

Son olarak, `assignedAt`'ı sitemizde görünür yapalım. Bunun için `StarshipDroid` join entity nesnesine ihtiyacımız olacak. Biraz daha fazla iş, ama kesinlikle yapılabilir.

Döngüyü değiştir; `for starshipDroid in ship.starshipDroids` şeklinde yap. Sonra `starshipDroid.droid.name` ve `starshipDroid.assignedAt` ile, biraz süs için `ago` filtresiyle göster:


```twig
// templates/starship/show\.html.twig
// ... lines 1 - 4
{% block body %}
// ... lines 6 - 19
    <div class="md:flex justify-center space-x-3 mt-5 px-4 lg:px-8">
// ... lines 21 - 25
        <div class="space-y-5">
            <div class="mt-8 max-w-xl mx-auto">
                <div class="px-8 pt-8">
                // ... lines 29 - 61
                    <p class="text-[22px] font-semibold">
                        {% for starshipDroid in ship.starshipDroids %}
                                {{ starshipDroid.droid.name }} (assigned {{ starshipDroid.assignedAt|ago }} {% if not loop.last %}, {% endif %}
                    // ... lines 65 - 66
                        {% endfor %}
                    </p>
                // ... lines 69 - 84
                </div>
            </div>
        </div>
    </div>
{% endblock %}
```

👉 Bu Twig kodunda, her droid’in atanma zamanı ekranda gösterilir.

Sayfayı yenile ve... artık her bir droid’in ne zaman atandığını görebiliyoruz.

Hepsi bu kadar! Doctrine ilişkilerinin en derin köşelerini, hatta ek alanları olan çoktan-çoğa ilişkileri bile keşfettik. Her zaman olduğu gibi, sorularınız varsa yorumlara yazabilirsiniz. Hep birlikteyiz!
