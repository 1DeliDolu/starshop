# 🚀 Turbo: Your Single Page App

## Turbo: Your Single Page App / Turbo: Tek Sayfa Uygulamanız

Bir kullanıcı arayüzü oluştururken onun güzel, etkileşimli ve akıcı olmasını isterim. Kişisel tercihim olarak, React, Vue ya da Next gibi ön yüz çatılarından kaçınıyorum. Ama siz kullanabilirsiniz… ve bunda hiçbir sorun yok: bunlar harika araçlardır. Ayrıca Symfony ile bir API geliştirmek gerçekten harika!

Ancak HTML’inizi Twig ile oluşturmak isterseniz – ki ben bunu yapmayı çok seviyorum – yine de son derece zengin, duyarlı ve etkileşimli bir kullanıcı arayüzü oluşturabiliriz!

Şık bir arayüzün en önemli parçalarından biri, tam sayfa yenilemelerini ortadan kaldırmaktır. Şu an tıklama yaptığımda bakın: hızlı ama bunlar tam sayfa yenilemeleri. React veya Vue gibi araçları kullanırsanız bu olmaz.

Bunları ortadan kaldırmak için Stimulus'u geliştiren ekipten gelen başka bir kütüphane olan Turbo'yu kullanacağız. Turbo birçok şey yapabilir, ama esas görevi tam sayfa yenilemeleri ortadan kaldırmaktır. Stimulus gibi bir JavaScript kütüphanesidir. Ve yine Stimulus gibi Symfony onu entegre etmek için bir bundle sağlar.

---

🛠️ Turbo'nun Kurulumu

## Installing Turbo / Turbo’nun Kurulumu

Terminalinizi açın ve şu komutu çalıştırın:

```
composer require symfony/ux-turbo
```

Bu kez, tarif iki ilginç değişiklik yaptı. Gösterelim. İlk değişiklik `importmap.php` dosyasında: `@hotwired/turbo` JavaScript paketi eklendi.

```
importmap.php
// ... lines 1 - 15
return [
// ... lines 17 - 26
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
];
```

İkinci değişiklik `assets/controllers.json` dosyasında. Bu dosyadan daha önce bahsetmedik, ama StimulusBundle tarifince eklenmişti: üçüncü taraf paketlerin içindeki Stimulus controller'larını etkinleştirmek için kullanılır.

```
assets/controllers.json
{
    "controllers": {
        "@symfony/ux-turbo": {
            "turbo-core": {
                "enabled": true,
                "fetch": "eager"
            },
            "mercure-turbo-stream": {
                "enabled": false,
                "fetch": "eager"
            }
        }
    },
    "entrypoints": []
}
```

Yeni kurduğumuz `symfony/ux-turbo` PHP paketi içerisinde `turbo-core` adında bir JavaScript controller’ı bulunuyor. Burada `enabled: true` olarak ayarlanmış olması, bu controller’ın artık kayıtlı ve kullanılabilir olduğu anlamına gelir: sanki `assets/controllers/` dizinimizde yaşıyormuş gibi.

Bu controller’ı doğrudan kullanmayacağız – yani onu bir öğeye bağlamayacağız. Ancak Stimulus ile yükleniyor ve kayıt ediliyor olması, Turbo’nun sitemizde aktif olması için yeterlidir.

---

⚡ Tam Sayfa Yenilemeleri Artık Yok!

## Full Page Refreshes Gone / Tam Sayfa Yenilemeleri Artık Yok

Bu ne anlama geliyor? Sihir gibi! Sayfayı bir kez yenileyin ve bam! Tam sayfa yenilemeler ortadan kalkar! Yukarıya dikkat edin: geri tıkladığımda yeniden yüklenmiyor. Bumm! Süper hızlı ve her şey Ajax üzerinden gerçekleşiyor.

Nasıl çalışıyor? Bu bağlantıya tıkladığımızda, Turbo bu tıklamayı yakalar ve tam sayfa yenileme yerine, Ajax ile ilgili sayfaya istek gönderir. Ajax isteği sayfanın tüm HTML içeriğini döner ve Turbo bunu mevcut sayfanın içine yerleştirir.

Bu küçük şey, projemizi bir tek sayfa uygulamasına dönüştürür ve sitemizin ne kadar hızlı hissettirdiği konusunda büyük fark yaratır.

---

🛠️ AJAX İstekleri ve Web Hata Ayıklama Araç Çubuğu

## AJAX Calls & the Web Debug Toolbar / AJAX İstekleri ve Web Hata Ayıklama Araç Çubuğu

Ama bir şey daha var. Sayfayı yenileyelim. Symfony uygulamanızda bir Ajax isteği gönderdiğinizde – ister Turbo ile ister başka bir yöntemle – Web Hata Ayıklama Araç Çubuğu bunu algılar. Tıkladığımda şuraya dikkat edin. Bakın! Bu sayfada yapılan tüm Ajax çağrılarının çalışan bir listesi var. Ve bu isteklerden herhangi birine ait profiler’ı görmek isterseniz bağlantıya tıklayabilirsiniz.

Ve evet… işte oradayız. Anasayfa için yapılan Ajax isteği burada. Ancak Turbo ile bu hileye bile ihtiyaç kalmaz çünkü tıklarken bu çubuk tamamen yeni sayfanın Web Hata Ayıklama Araç Çubuğu ile değiştirilir.

Ve bir de şunu duydunuz mu: şu anda mevcut olan Turbo 8 ile siteniz daha da hızlı hissedilir. Bunun nedeni "Instant Click" adlı yeni bir özellik. Bu özellik ile, bir bağlantının üzerine geldiğinizde Turbo o sayfaya Ajax isteği yapar. Ardından gerçekten tıkladığınızda, içerik anında yüklenir… ya da en azından önden yüklenmiş olur.

---

✨ Turbo'nun Diğer Özellikleri

## Turbo Has More Features / Turbo’nun Diğer Özellikleri

Turbo’nun birçok başka özelliği de vardır ve bunları popover’lar, modallar, toast bildirimleri ve daha fazlası ile frontend oluşturduğumuz LAST Stack eğitiminde kullanıyoruz.

---

⚠️ Turbo İçin Sağlam JavaScript Gerekir

## Turbo Requires Good JavaScript / Turbo Sağlam JavaScript Gerektirir

Ancak Turbo hakkında bir not. Artık tam sayfa yenilemeler olmadığı için JavaScript kodunuzun buna uygun yazılmış olması gerekir. Pek çok JavaScript kodu, tam sayfa yenilemelerini varsayar… ve HTML aniden sayfaya eklendiğinde bozulabilir. İyi haber şu ki, JavaScript’inizi Stimulus ile yazarsanız sorun olmaz.

İzleyin. Anasayfaya nasıl gelirsek gelelim, yan menüyü kapatma JavaScript kodumuz hep çalışıyor.

---

🏁 Son Bölüm

## Final Chapter / Son Bölüm

Tam gaz sona yaklaşıyoruz! Bitirmeden önce, Symfony’nin harika kod üretim aracı olan MakerBundle ile eğlenceli bir bonus bölümü daha yapacağız.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./18. Stimulus Writing Pro JavaScript.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./20_Maker Bundle Let's Generate Some Code.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
