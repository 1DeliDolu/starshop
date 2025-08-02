## 🚀 Setup / Kurulum

Merhaba arkadaşlar! Tekrar hoş geldiniz! Ve izin verirseniz, ben de kendime hoş geldin diyeyim. 14 aylık beyin kanseri "tatilimden" dönüyorum. Ne yazık ki tamamen iyileşmiş değilim ve evet, bir elimle yazıyorum, bir nevi Symfony korsanı gibi. Ama sizi çok özledim ve Symfony’yi de öyle. Bugün güzel bir gün. Destek, sevgi ve sabrınız için teşekkür ederim. Şimdi işimize bakalım!

Önceki derste oldukça etkileyici işler yaptık. Bir `entity` oluşturduk, `migration`ları kurduk, `fixture`lar yarattık ve SQL uzmanı gibi sorgular yazdık. Ama kabul edelim, `veritabanı ilişkilerini` anlamadan arkadaşlarımızı veya büyükannemizi etkileyecek bir şey inşa edemeyiz. Örneğin: "bu pizza dilimi bana ait" ya da "çok fazla pizza dilimim var." Mmm, pizzayı severim.

`İlişkiler konusunu` tamamen kavramanız için, bu sayfadan kurs kodlarını indirmeniz gerekiyor. Arşivi açtığınızda, burada gördüğünüz kodların bulunduğu bir `start/` diziniyle karşılaşacaksınız. Tüm kurulum güzellikleri için `README.md` dosyasına göz atın. Son adım olarak bir `terminal` açıp proje dizinine girin ve şu komutu çalıştırın: `symfony serve`. Bazen bu komutu `-d` seçeneğiyle çalıştırırım, böylece arka planda çalışır. Ama bugün yüksek sesle ve gururla ön planda çalıştıracağım.

```shell
symfony serve
```

👉 Bu komut, Symfony geliştirme sunucusunu başlatır.

### Oh Merhaba Sunucu ve Tailwind Logları

Komutu ön planda çalıştırmanın faydalı bir yan etkisi, tüm `log`ları görebilmenizdir. Gerçi, bunları istediğiniz zaman `symfony server:log` komutuyla da görebilirsiniz. Bu proje `Tailwind CSS` kullanır ve arka planda `Tailwind`’i indirip derlediğini görebilirsiniz. Bu işlem tamamlandıktan sonra, yukarı kaydırıp bağlantıya tıklayarak uygulamamızı başlatacağım: `Starshop`!

### Starshop’a Giriş

`Starshop`, uzay gemilerini tamir etmekle ilgili bir uygulamadır – uzay gemisi sorunları için tek durak çözüm, çünkü hiç kimse bozuk bir duşla galaksiler arası boşlukta sürüklenmek istemez. İğrenç. Tüm bu `starship`ler doğrudan veritabanından gelmektedir. `src/Entity/` dizinine giderseniz, parlak `entity`mizle karşılaşırsınız: `Starship`.

```php
// src/Entity/Starship.php

// ... lines 1 - 10
#[ORM\Entity(repositoryClass: StarshipRepository::class)]
class Starship
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column]
    private ?string $name = null;
// ... lines 23 - 137
}
```

👉 Bu sınıf, bir uzay gemisini temsil eden `entity`dir ve veritabanında `starship` kayıtlarını saklar.

## 🛠️ Next Steps: Tracking Ship Parts / Sonraki Adımlar: Gemi Parçalarını Takip Etmek

Şimdi işleri biraz renklendirme zamanı: bir geminin parçalarını ve bunların maliyetlerini takip edeceğiz. Ardından her parçayı veritabanında bir gemiye atayacağız.
