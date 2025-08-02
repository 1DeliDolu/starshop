# 🔢 Ordering a Relation and "fetch" type / Bir İlişkinin Sıralanması ve "fetch" Türü

Bir `in progress` yıldız gemisine tıklayın. Sonra `templates/starship/show.html.twig` dosyasını açın. Parçaları listelemek için `for part in ship.parts` kullanıyorsunuz.

Bu sorunsuz çalışır. Ama ufak bir sorun var: Parçaların sırası garanti edilmez. Veritabanından hangi sırada çıkarlarsa o sırada görünürler!

Parçaları isme göre sıralamak isterim. Bunun için özel bir sorgu yazmak zorunda mıyım... artık o pratik `ship.parts`'ı kullanamayacak mıyım?

Korkmayın! Birkaç pratik yol var!

## 🪄 Rearranging the Parts / Parçaları Sıralamak

`Starship` entity'sine gidin ve `parts` özelliğini bulun. Üstüne yeni bir öznitelik ekleyin: `#[ORM\OrderBy(['name' => 'ASC'])]` — `position` değil, dikkat:

```php
// src/Entity/Starship.php
// ... lines 1 - 13
class Starship
{
// ... lines 16 - 45
    #[ORM\OrderBy(['name' => 'ASC'])]
    private Collection $parts;
// ... lines 48 - 181
}
```

👉 Bu kod, parçaların isimlerine göre artan şekilde sıralanmasını sağlar.

Sayfayı yenileyin, ve oldu!

Eğer büyük "T" harfinin küçük "c"den önce geldiğine şaşırıyorsanız, alfabenizi unutmadınız. Sadece Postgres büyük/küçük harfe duyarlı bir veritabanıdır. Bu yüzden büyük "T", küçük "c"den önce geliyor.

## 🧠 Smart Queries / Akıllı Sorgular

Bu sayfadaki sorguları inceleyin ve biçimlendirilmiş SQL'e bakın. Sorgu şu şekilde: `starship_part` tablosundan, `starship_id` bizim ID'ye eşit olanları ve isme göre artan şekilde sıralar. Tam da istediğimiz sorgu!

## 🏎️ The N+1 Problem / N+1 Problemi

Anasayfaya dönün ve şablonunu açın: `templates/main/homepage.html.twig`. "arrived" satırından sonra bir div ekleyin ve parça sayısını yazdırın: `ship.parts|length`:

```twig
// templates/main/homepage.html.twig
// ... lines 1 - 4
{% block body %}
    <main class="flex flex-col lg:flex-row">
// ... lines 7 - 8
        <div class="px-12 pt-10 w-full">
// ... lines 10 - 17
            <div class="space-y-5">
                {% for ship in ships %}
                    <div class="bg-[#16202A] rounded-2xl pl-5 py-5 pr-11 flex flex-col min-[1174px]:flex-row min-[1174px]:justify-between">
                        <div class="flex justify-center min-[1174px]:justify-start">
// ... line 22
                            <div class="ml-5">
// ... lines 24 - 33
                                <div>
                                    Arrived: {{ ship.arrivedAt|ago }}
                                </div>
                                <div>
                                    Parts: {{ ship.parts|length }}
                                </div>
                            </div>
                        </div>
// ... lines 42 - 52
                    </div>
                {% endfor %}
            </div>
// ... lines 56 - 71
        </div>
    </main>
{% endblock %}
```

👉 Bu kod, her geminin parça sayısını gösterir.

Anasayfada çalışıyor. Sorgulara bakın, ilginç şeyler oluyor. Pagination nedeniyle bazı sorgular karmaşık görünebilir ama temelde bir yıldız gemisi için bir sorgu ve her bir gemi için parçaları getiren 5 ek sorgu (her gemi için bir tane) var.

Ne oluyor? Önce yıldız gemilerini çekiyoruz, sonra `ship.parts` sayısı istendiğinde Doctrine elinde veri olmadığını anlıyor. Sonra her bir geminin parçalarını teker teker sorguluyor ve sayıyor. Bu, "N+1 problemi" olarak bilinir: 1 sorgu gemiler için, her biri için N tane de parça sorgusu.

## 🏋️‍♂️ Efficient Querying / Verimli Sorgulama

Ama burada daha büyük bir sorun var! Sadece parça sayısını öğrenmek için tüm `starship_part`'ları sorguluyoruz. Parça verisine ihtiyacımız yok, sadece kaç tane olduklarını bilmek istiyoruz. Bu küçük bir sorun... ta ki bir gemide çok fazla parça olana kadar.

Bunu düzeltmek için, `Starship` entity'sinde OneToMany ilişkisine `fetch` seçeneğini `EXTRA_LAZY` olarak ayarlayın:

```php
// src/Entity/Starship.php
// ... lines 1 - 13
class Starship
{
// ... lines 16 - 44
    #[ORM\OneToMany(targetEntity: StarshipPart::class, mappedBy: 'starship', fetch: 'EXTRA_LAZY', orphanRemoval: true)]
// ... line 46
    private Collection $parts;
// ... lines 48 - 181
}
```

👉 Bu kod, ilişkiyi "EXTRA_LAZY" olarak ayarlar.

Sonucu görmek için tekrar bakın!

## 🔢 Counting the Parts / Parça Saymak

Anasayfaya dönün. Önceden dokuz sorgumuz vardı... Şimdi? Hâlâ dokuz sorgu, fakat parça sorgusu değişti. Artık tüm veriyi çekmek yerine sadece kaç adet olduğunu sayıyor. Çok daha akıllıca, değil mi?

Belki merak ediyorsunuz – ben ettim – neden her zaman `fetch="EXTRA_LAZY"` kullanmıyoruz? Birincisi, bu küçük bir performans iyileştirmesidir; sadece çok parçası olan bir geminiz varsa ve sadece sayıyı istiyorsanız gerekir. Daha önemlisi, önce sayıyı mı yoksa önce döngüyle mi eriştiğinize göre fazladan bir sorguya neden olabilir.

## 🧩 The Criteria System / Criteria Sistemi

Sıradaki soruya geçiyoruz! Ya sadece belirli bir fiyattan pahalı olan ilgili parçaları getirmek istersek? Yine `ship.parts` kısayolunu kullanabilir miyiz yoksa özel bir sorgu mu yazmamız gerekir? Takipte kalın, sırada criteria sistemi var.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./8_Orphan Removal.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./10_The Clever Criteria System.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
