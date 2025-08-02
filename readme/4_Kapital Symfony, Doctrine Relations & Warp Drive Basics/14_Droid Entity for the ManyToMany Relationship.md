# 🤖 Droid Entity for the ManyToMany Relationship / ManyToMany İlişkisi için Droid Varlığı

Şimdiye kadar ilişki türlerinin tadına baktık. `ManyToOne` ve `OneToMany` gördük, ki bunlar aslında aynı ilişki türüdür, sadece farklı taraflardan bakılmıştır. Yani, aslında şu ana kadar sadece bir ilişki türünü keşfettik: `ManyToOne`.

Peki ya belki de duyduğunuz `OneToOne` ilişkisi? Sürpriz! O da aslında gizlenmiş bir `ManyToOne`: veritabanında bir `ManyToOne` gibi görünür fakat yabancı anahtar üzerinde `unique` kısıtlaması vardır, böylece ilişkinin her iki tarafı da sadece bir öğeye bağlanabilir.

Kısacası: `ManyToOne`, `OneToMany` ve `OneToOne` aslında aynı ilişki türünün farklı görünümleridir.

Droidler Sahneye Çıkıyor
Şimdi, uzayda tamir işlerinden bahsedelim. Biz insanlar için bu tehlikeli bir iş! Uzayın vakumu, soğuk, oksijen yokluğu, ara sıra meteor yağmurları ve sonsuz boşluk var. Bir de Bob'un kemerini bağlamayı unutup sonsuz boşluğa süzülüp kaybolması var. Onu bulmak saatler sürdü. O günden sonra asla eskisi gibi olmadı.

Peki bu işi kim daha iyi yapar? Tabii ki güvenilir `droid`lerimiz!

Bir droid ordusuna komuta ediyorsunuz, her biri birden fazla `starship`e atanmış ve her `starship`te de birden fazla `droid` var. İşte burada ikinci ve son ilişki türümüz devreye giriyor: `ManyToMany`.

Hazırlık için bir `Droid` varlığına ihtiyacımız var. Artık ne yapılacağını biliyorsunuz:

```shell
symfony console make:entity Droid
```

👉 Bu komut, `Droid` varlığı oluşturur.

Ve böylece işimiz başlıyor. Bunun için birkaç özellik yeterli: `name` ve `primaryFunction`. Varsayılanlar gayet uygun. Hepsi bu kadar, çok kolay.

Ama bir geliştiricinin işi asla bitmez. Çünkü biz tembel değil, verimli geliştiricileriz; göç komutunu kopyalayıp çalıştırın:

```shell
symfony console make:migration
```

👉 Bu komut, yeni varlık için migration (göç) dosyası oluşturur.

Bir bakın, burada sürpriz yok. Şimdi çalıştırın:

```shell
symfony console doctrine:migrations:migrate
```

👉 Bu komut, migration dosyasını çalıştırıp veritabanında yeni tabloyu oluşturur.

Ve… veritabanında yepyeni bir `droid` tablomuz oldu. Henüz `ship` ile ilişkisi yok, ama sonuçta her ilişki bir yerden başlar.

Evreni Droidlerle Doldurmak
Bunu kurmadan önce, birkaç droid üretelim! Şunu çalıştırın:

```shell
symfony console make:factory Droid
```

👉 Bu komut, `Droid` varlığı için bir factory (üretim) sınıfı oluşturur.

Şimdi `src/Factory/DroidFactory.php` dosyasını açın. Hazır durumda, ama biraz kişiliğe ihtiyaçları var. Diziyi daha ilginç verilerle değiştiriyorum:

```php
// src/Factory/DroidFactory.php
// ... lines 1 - 10
final class DroidFactory extends PersistentProxyObjectFactory
{
// ... lines 13 - 26
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->randomElement([
                'R2-D2', 'C-3PO', 'BB-8', 'ZZZ-123',
            ]),
            'primaryFunction' => self::faker()->randomElement([
                'astromech',
                'protocol',
                'astromech',
                'assassin',
                'sleeper',
            ]),
        ];
    }
// ... lines 47 - 56
}
```

👉 Bu sınıfta, `Droid`'ler için rastgele isim ve işlevler atanıyor.

> Not

Bunun çalışması için ayrıca `AppFixtures` dosyasında şunu ekleyin:

```shell
DroidFactory::createMany(100)
```

Bunu birazdan yapacağız. Fixture'ları tekrar yükleyin:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, veritabanına yeni droid verileri yükler.

Ve işte bu kadar! Uzayda kaybolmadan önce yardım etmeye hazır, droidlerle dolu bir tabloya sahip olduk. Ama henüz bir droid bir gemiye atanamıyor. Bunu, son ilişki türümüz olan `ManyToMany` ile değiştireceğiz.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./13_Adding a Search + the Request Object.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./15_Many-To-Many Relationship.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
