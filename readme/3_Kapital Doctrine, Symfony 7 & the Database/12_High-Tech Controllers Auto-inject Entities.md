# 🚀 High-Tech Controllers: Auto-inject Entities / Yüksek Teknoloji Denetleyicileri: Varlıkları Otomatik Enjekte Etme

Ana sayfadan bir gemiye tıkladığımızda gösterim sayfasına gideriz, ancak bu URL çok hoş veya akılda kalıcı değildir. Sadece geminin kimliğini (`id`) içerir. Jean-Luc Picard’ın, Enterprise yerine USS 43’ün kaptanı olduğunu söylediğini düşünün. Sönük olurdu!

Bunu, yeni `slug` alanımızı kullanacak şekilde değiştirelim. `id` gibi, bu da benzersizdir, bu nedenle veritabanında tek bir gemiyi bulmak için kullanabiliriz.

Ama önce size çok havalı bir şeyi göstermek istiyorum. `StarshipController::show()` metodunu açın. Bu metoda şu anda rota parametresinden `$id` enjekte ediliyor ve `StarshipRepository` servisi ile bu ID'den gemi bulunuyor. Eğer gemi bulunmazsa 404 fırlatan bir mantığımız da var.

## Inject Starship Directly / Varlığı Doğrudan Enjekte Et

Tüm parametreleri kaldırın ve sadece `Starship $ship` olarak değiştirin, ardından gemiyi bulma ve bulunamama mantığını tamamen silin:

```php 
// src/Controller/StarshipController.php

// ... lines 1 - 9
class StarshipController extends AbstractController
{
    #[Route('/starships/{id<\d+>}', name: 'app_starship_show')]
    public function show(Starship $ship): Response
    {
        return $this->render('starship/show.html.twig', [
            'ship' => $ship,
        ]);
    }
}
```

👉 Bu kod, `id` parametresiyle gelen bir `Starship` varlığını otomatik olarak enjekte eder ve görünüm şablonuna aktarır.

Bu, şimdi çok sade bir controller oldu - bayıldım. Eğer "ama `Starship` bir servis değil ki" diyorsanız, haklısınız. Ama biraz sabredin.

Uygulamaya geri dönelim, `Starship` gösterim sayfasındayız. Sayfayı yenileyin... ve... hala çalışıyor! Şimdi var olmayan bir gemiyi deneyelim: mesela ID'si 999 olan. 404 hatası alıyoruz. Yani önceki mantık hala çalışıyor... Nasıl?!

Varlıklar servis değildir... bu hâlâ ve her zaman geçerlidir. `MainController::homepage()` metoduna bakın. `Request` nesnesini enjekte ediyoruz. O da bir servis değildir. Eğer bunu bir servisin yapıcısına enjekte etmeye çalışırsanız hata alırsınız.

## Controller Value Resolvers / Denetleyici Değer Çözücüler

Controller'lar özeldir. Symfony bir controller metodunu çağırırken, önce tüm parametrelere bakar ve bunları "denetleyici değer çözücüleri" üzerinden geçirir. Birkaç tane vardır ve aslında bazılarını fark etmeden kullandık bile. Örneğin, `RequestValueResolver`, `Request` nesnesini enjekte eder ve `ServiceValueResolver`, bir parametre servis tipiyle tanımlanmışsa onu çözer.

Symfony’nun Doctrine entegrasyonu bir de `EntityValueResolver` sağlar. İşte bu sayede `Starship` varlığını enjekte edebiliyoruz. Çünkü `Starship` türünde tanımladık, bu geçerli bir Doctrine varlığı ve elimizde bir `id` rota parametresi var. Her varlığın bir `id`'si olduğu için, çözücü otomatik olarak varlığı sorgular ve bize iletir. Eğer varlık bulunamazsa 404 fırlatır. Harika!

## 🧭 Using slug in the URL / URL'de slug Kullanımı

Görevimize geri dönelim: `Starship` `slug`'ını `id` yerine URL'de kullanalım. Önce, `#[Route]` özniteliğini `/starship/{slug}` olarak güncelleyin:

```php
src/Controller/StarshipController.php

// ... lines 1 - 9
class StarshipController extends AbstractController
{
    #[Route('/starships/{slug}', name: 'app_starship_show')]
    public function show(Starship $ship): Response
// ... lines 14 - 18
}
```

👉 Bu rota, URL'deki `slug` değerine göre `Starship` varlığını bulmak için yapılandırılmıştır.

Sonra, bu rotaya ait URL'yi üreten tüm yerleri güncellememiz gerekiyor. Endişelenmeyin, sadece 2 yer var.

İlki: `templates/main/homepage.html.twig`. "show" kelimesini arayın - işte burada. `path` fonksiyonu içinde `id: ship.id` ifadesini `slug: ship.slug` ile değiştirin:

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
                            // ... lines 24 - 27
                                <h4 class="text-[22px] pt-1 font-semibold">
                                    <a
                                        class="hover:text-slate-200"
                                        href="{{ path('app_starship_show', { slug: ship.slug }) }}"
                                    >{{ ship.name }}</a>
                                </h4>
                            // ... lines 34 - 36
                            </div>
                        </div>
                    // ... lines 39 - 49
                    </div>
                {% endfor %}
            </div>
        // ... lines 53 - 68
        </div>
    </main>
{% endblock %}
```

👉 Bu şablon, `slug` parametresiyle gemi bağlantılarını oluşturur.

Sonra `templates/main/_shipStatusAside.html.twig` dosyasını açın, "show" kelimesini bulun ve `id: myShip.id` ifadesini `slug: myShip.slug` ile değiştirin:

```twig
// templates/main/_shipStatusAside.html.twig

<aside
// ... lines 2 - 3
>
    // ... lines 5 - 11
    <div>
        <div class="flex flex-col space-y-1.5">
        // ... lines 14 - 17
            <h3 class="tracking-tight text-[22px] font-semibold">
                <a class="hover:underline" href="{{ path('app_starship_show', {
                    slug: myShip.slug
                }) }}">{{ myShip.name }}</a>
            </h3>
        </div>
    // ... lines 24 - 34
    </div>
</aside>
```

👉 Bu şablon da gemi bağlantısını `slug` ile üretir.

Uygulamaya geri dönün ve "Geri" diyerek ana sayfaya gidin. Bir gemi bağlantısının üzerine gelin ve URL’ye bakın. Çok daha hoş! Tıklayın.

> Kırmızı alarm!

"Cannot autowire argument \$ship..." hatası.

Sorun şu: eğer rota joker karakterinin adı `id` değilse, Symfony `Starship` varlığını otomatik çözümleyemez ve onu bir servis olarak enjekte etmeye çalışır. Joker karakterin adı `id` olmadığında, Symfony’ye biraz yardımcı olmamız gerekir.

## 🧩 #\[MapEntity] Attribute / #\[MapEntity] Özniteliği

`StarshipController::show()` metoduna geri dönün, `Starship $ship` parametresini kendi satırına taşıyın. Üstüne bir öznitelik ekleyin: `#[MapEntity]`. İçine bir dizi verin: `slug` anahtarı, rota parametresi adıdır, değeri yine `slug`, yani varlıkta arama yapılacak özelliktir:

```php 
// src/Controller/StarshipController.php

// ... lines 1 - 10
class StarshipController extends AbstractController
{
    // ... line 13
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Starship $ship,
    ): Response {
    // ... lines 18 - 20
    }
}
```

👉 Bu öznitelik, `slug` parametresine göre `Starship` varlığını bulup enjekte eder.

Uygulamaya dönüp sayfayı yenileyin. Her şey tekrar çalışıyor, kırmızı alarm iptal!

Slug yerine rastgele bir metin girin... ve 404! Harika!

Artık gemi URL’lerimiz daha şık, insan okunabilir ve SEO dostu!

Uzayda uçmak tehlikeli iştir. Bazen yıldız gemileri "hızlı, planlanmamış ayrışmalara" uğrar... yani patlarlar. Artık var olmayan gemileri veritabanından silmenin bir yoluna ihtiyacımız var. Sonraki bölümde Doctrine ile varlıkları nasıl sileceğimizi göreceğiz.

---
<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./11_Auto Slug and Timestamps with Doctrine Extensions.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./13_Black Hole Deleting Entities.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
