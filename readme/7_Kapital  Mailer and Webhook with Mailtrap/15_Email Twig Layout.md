# ✉️ Email Twig Layout / Email Twig Yerleşim Şablonu

Yeni özellik zamanı! Müşterilere, rezervasyon yaptıkları geziden 1 hafta önce bir hatırlatma e-postası göndermek istiyorum. Kalkışa 1 hafta kaldı!

## 🛠️ Symfony CLI Worker Sorunu / Symfony CLI Worker Sorunu

Öncelikle, küçük bir Symfony CLI worker problemimiz var. `.symfony.local.yaml` dosyasını aç. `messenger worker` dizininde, değişiklikleri izlemek için `vendor` klasörü izleniyor. Bazı sistemlerde burada izlenecek çok fazla dosya olduğu için garip şeyler olabiliyor. Sorun değil: `vendor/` satırını kaldır:

````yaml
#.symfony.local.yaml
```yaml
workers:
// ... lines 2 - 5
    messenger:
// ... line 7
        watch: ['config', 'src', 'templates']
````

👉 Artık worker yalnızca `config`, `src` ve `templates` klasörlerini izleyecek.

Ve yapılandırmayı değiştirdiğimiz için terminale geçip web sunucusunu yeniden başlat:

```bash
symfony server:stop
```

👉 Symfony sunucusunu durdurur.

Ardından:

```bash
symfony serve -d
```

👉 Symfony sunucusunu arka planda başlatır.

## 🖼️ Email Layout / Email Şablonu

Yeni rezervasyon hatırlatma e-postamızın şablonu, rezervasyon onayı şablonuna çok benzer olacak. Yinelenmeyi azaltmak ve e-postalarımızı tutarlı yapmak için, `templates/email/` dizininde tüm e-postaların genişleteceği yeni bir `layout.html.twig` şablonu oluştur.

`booking_confirmation.html.twig` dosyasının içeriğini kopyala ve buraya yapıştır. Şimdi, sadece rezervasyon onayına özel içeriği kaldır ve boş bir `content` bloğu oluştur. İmzamızı burada tutmakta bir sakınca yok.

````twig
templates/email/layout.html.twig
```twig
{% apply inky_to_html|inline_css(source('@styles/foundation-emails.css'), source('@styles/email.css')) %}
    <container>
        {% block content %}{% endblock %}
        <row>
            <columns>
                <p>We can't wait to see you there,</p>
                <p>Your friends at Universal Travel</p>
            </columns>
        </row>
    </container>
{% endapply %}
````

👉 Bu şablon, tüm e-posta içeriklerinin yerleştirileceği temel yapıyı oluşturur.

`booking_confirmation.html.twig` dosyasında, en üstte bu yeni layout'u genişlet ve `content` bloğu ekle. Aşağıda, e-postaya özel içeriği bu bloğun içine taşı. Geri kalan her şeyi kaldır.

````twig
templates/email/booking_confirmation.html.twig
```twig
{% extends 'email/layout.html.twig' %}
{% block content %}
    <row>
        <columns>
            <spacer size="40"></spacer>
            <p class="accent-title">Get Ready for your trip to</p>
            <h1 class="trip-name">{{ trip.name }}</h1>
            <img
                    class="trip-image float-center"
                    src="{{ email.image('@images/%s.png'|format(trip.slug)) }}"
                    alt="{{ trip.name }}">
        </columns>
    </row>
    <row>
        <columns>
            <p class="accent-title">Departure: {{ booking.date|date('Y-m-d') }}</p>
        </columns>
    </row>
    <row>
        <columns>
            <button class="expanded rounded center" href="{{ url('booking_show', {uid: booking.uid}) }}">
                Manage Booking
            </button>
            <button class="expanded rounded center secondary" href="{{ url('bookings', {uid: customer.uid}) }}">
                My Account
            </button>
        </columns>
    </row>
{% endblock %}
````

👉 Artık rezervasyon onayı e-postası, genel bir şablonu genişletiyor ve içeriği `content` bloğu ile tanımlıyor.

Rezervasyon onayı e-postasının hala çalıştığından emin olalım — bunun için testlerimiz var! Terminalde tekrar çalıştır:

```shell
bin/phpunit
```

👉 PHPUnit ile testleri çalıştırır.

Her şey yeşil! Bu iyi bir işaret. İki kez emin olmak için Mailtrap'te de kontrol et. Uygulamada bir rezervasyon yap... ve Mailtrap'i kontrol et. Hala harika görünüyor!

Hatırlatma e-postasını yazma zamanı!

## 🚩 Booking Reminder Flag / Rezervasyon Hatırlatma Bayrağı

Hatırlatma e-postası gönderildikten sonra, müşteriye birden fazla hatırlatma göndermemek için rezervasyonu işaretlememiz gerekir. Bunun için `Booking` varlığına yeni bir bayrak ekleyelim.

Terminalde şunu çalıştır:

```shell
symfony make:entity Booking
```

👉 Symfony make komutu ile entity güncelleme başlatılır. (Eğer çalışmazsa alternatif komut aşağıda!)

Eğer hata alırsan:

```shell
symfony console make:entity Booking
```

👉 Komut satırından entity güncelleyebilirsin.

Yeni bir alan ekle: `reminderSentAt`, türü `datetime_immutable`, nullable? Evet. Bu tip bayrak alanları için kullandığım yaygın bir desendir. `null` değeri false, bir tarih ise true anlamına gelir. Aynı işi görür ama bize biraz daha fazla bilgi sağlar.

Komutu tamamladıktan sonra çık.

`Booking` entity içinde... işte yeni property ve getter ile setter'ı.

## 🔎 Finding Bookings to Remind / Hatırlatma Gönderilecek Rezervasyonları Bulmak

Şimdi, hatırlatma gönderilmesi gereken tüm rezervasyonları bulmanın bir yoluna ihtiyacımız var. Bu iş için mükemmel yer: `BookingRepository`! Yeni bir metod ekle: `findBookingsToRemind()`, dönüş tipi: dizi. Bir docblock ekleyerek bir dizi `Booking` nesnesi döndüğünü göster:

````php
//src/Repository/BookingRepository.php
```php
// ... lines 1 - 12
class BookingRepository extends ServiceEntityRepository
{
// ... lines 15 - 51
    /**
     * @return Booking[]
     */
    public function findBookingsToRemind(): array
    {
// ... lines 57 - 65
    }
}
````

👉 Bu metodun bir dizi `Booking` nesnesi döndürdüğü belirtiliyor.

İçeride, `$this->createQueryBuilder()` ile başla, takma ad olarak `b` kullan. Şu zinciri kur: `->andWhere('b.reminderSentAt IS NULL')`, `->andWhere('b.date <= :future')`, `->andWhere('b.date > :now')`, yer tutucular için `->setParameter('future', new \DateTimeImmutable('+7 days'))` ve `->setParameter('now', new \DateTimeImmutable('now'))`. Sonunda, `->getQuery()->getResult()` ile bitir:

````php
//src/Repository/BookingRepository.php
```php
// ... lines 1 - 12
class BookingRepository extends ServiceEntityRepository
{
// ... lines 15 - 54
    public function findBookingsToRemind(): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.reminderSentAt IS NULL')
            ->andWhere('b.date <= :future')
            ->andWhere('b.date > :now')
            ->setParameter('future', new \DateTimeImmutable('+7 days'))
            ->setParameter('now', new \DateTimeImmutable('now'))
            ->getQuery()
            ->getResult()
        ;
    }
}
````

👉 Bu metod, bir hafta içinde olan ve henüz hatırlatma gönderilmemiş rezervasyonları döndürür.

## 🧪 Pending Reminder Booking Fixture / Hatırlatma Bekleyen Rezervasyon Fixture'ı

`AppFixtures` dosyasında, burada bazı sahte rezervasyonlar oluşturuyoruz. Hatırlatma e-postasının kesinlikle tetikleneceği bir rezervasyon ekle: `BookingFactory::createOne()`, içinde, `'trip' => $arrakis, 'customer' => $clark` ve en önemlisi, `'date' => new \DateTimeImmutable('+6 days')`:

````php
// src/DataFixtures/AppFixtures.php
```php
// ... lines 1 - 10
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
// ... lines 15 - 87
        BookingFactory::createOne([
            'trip' => $arrakis,
            'customer' => $clark,
            'date' => new \DateTimeImmutable('+6 days'),
        ]);
    }
}
````

👉 Bu fixture, 6 gün sonraya bir rezervasyon ekler, böylece testlerde hatırlatma e-postası tetiklenir.

Şimdi ile 7 gün sonrası arasında net bir şekilde kalıyor.

## 🔄 "Migration" / "Migration" (Veritabanı Güncelleme)

Veritabanı yapısında değişiklik yaptık. Normalde migration oluşturmamız gerekir... fakat migration kullanmıyoruz. Bu yüzden şemayı zorla güncelleyeceğiz. Terminalde şunu çalıştır:

```bash
symfony console doctrine:schema:update --force
```

👉 Doctrine ile veritabanı şeması güncellenir.

Ardından, fixture'ları tekrar yükle:

```bash
symfony console doctrine:fixture:load
```

👉 Veritabanı test verileriyle doldurulur.

Hepsi çalıştı, harika!

Sırada, yeni bir hatırlatma e-postası ve bunları gönderecek bir CLI komutu oluşturacağız!
