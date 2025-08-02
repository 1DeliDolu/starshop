# <h1 id="setup">🚀 Setup / Kurulum </h1>

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

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./2_ Part Entity.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>

<!-- Scroll to Top Button -->
<div id="scrollToTop" style="position: fixed; bottom: 30px; right: 30px; display: none; z-index: 1000;">
    <a href="#setup" title="Yukarı Çık" style="background-color: #007bff; color: white; padding: 12px 16px; text-decoration: none; border-radius: 50%; font-size: 1.2em; display: inline-block; box-shadow: 0 4px 8px rgba(0,0,0,0.3); transition: all 0.3s ease;">⬆️</a>
</div>

<script>
// Scroll to top button functionality
window.onscroll = function() {
    var scrollToTopBtn = document.getElementById("scrollToTop");
    if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        scrollToTopBtn.style.display = "block";
        scrollToTopBtn.style.opacity = "1";
    } else {
        scrollToTopBtn.style.display = "none";
        scrollToTopBtn.style.opacity = "0";
    }
};

// Smooth scroll effect
document.getElementById("scrollToTop").addEventListener("click", function(e) {
    e.preventDefault();
    document.getElementById("setup").scrollIntoView({
        behavior: 'smooth'
    });
});
</script>
