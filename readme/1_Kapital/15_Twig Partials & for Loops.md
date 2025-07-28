## 🎨 Tasarım Güncellemesi ve Twig Parçaları

### Twig Partials & for Loops / Twig Parçaları ve for Döngüleri

Sitemize yeni bir tasarım uyguladık... yani şablonlarımızı birçok Tailwind sınıfı içeren HTML öğeleriyle güncelledik. Sonuç? Göze hitap eden bir site.

Şablonların bazı kısımları hâlâ dinamik: kaptan ve sınıf bilgilerini yazdırmak için Twig kodları kullanıyoruz. Ancak bazı bölümler tamamen sabit kodlu. Bu oldukça yaygındır: bir frontend geliştirici siteyi HTML & Tailwind ile oluşturur... ancak onu dinamik hale getirmek ve gerçekten çalışır kılmak sana kalır.

---

## 🧩 Şablon Parçası Olarak Düzenleme

### Organizing into a Template Partial / Şablon Parçası Olarak Düzenleme

`homepage.html.twig` dosyasının en üstündeki uzun `<aside>` öğesi yan menüyü oluşturur. Bu kodun `homepage.html.twig` içinde yer alması sorun değil... ancak oldukça yer kaplıyor! Peki ya bu yan menüyü başka bir sayfada da kullanmak istersek?

Twig’in harika özelliklerinden biri, HTML “parçalarını” alıp kendi şablon dosyalarına taşıyabilmen. Bu tür şablonlara **template partial** denir... çünkü sayfanın yalnızca bir kısmını içerirler.

Bu kodu kopyala ve `main/` klasöründe – aslında istediğin yere koyabilirsin – `_shipStatusAside.html.twig` adında yeni bir dosya oluştur. İçeriği bu dosyaya yapıştır:

```twig
<aside
    class="pb-8 lg:pb-0 lg:w-[411px] shrink-0 lg:block lg:min-h-screen text-white transition-all overflow-hidden px-8 border-b lg:border-b-0 lg:border-r border-white/20"
>
    <div class="flex justify-between mt-11 mb-7">
        <h2 class="text-[32px] font-semibold">My Ship Status</h2>
        <button>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 448 512"><path fill="#fff" d="M384 96c0-17.7 14.3-32 32-32s32 14.3 32 32V416c0 17.7-14.3 32-32 32s-32-14.3-32-32V96zM9.4 278.6c-12.5-12.5-12.5-32.8 0-45.3l128-128c12.5-12.5 32.8-12.5 45.3 0s12.5 32.8 0 45.3L109.3 224 288 224c17.7 0 32 14.3 32 32s-14.3 32-32 32l-178.7 0 73.4 73.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0l-128-128z"/></svg>
        </button>
    </div>
    <div>
        <div class="flex flex-col space-y-1.5">
            <div class="rounded-2xl py-1 px-3 flex justify-center w-32 items-center" style="background: rgba(255, 184, 0, .1);">
                <div class="rounded-full h-2 w-2 bg-amber-400 blur-[1px] mr-2"></div>
                <p class="uppercase text-xs">in progress</p>
            </div>
            <h3 class="tracking-tight text-[22px] font-semibold">
                <a class="hover:underline" href="{{ path('app_starship_show', {
                    id: myShip.id
                }) }}">{{ myShip.name }}</a>
            </h3>
        </div>
        <div class="flex mt-4">
            <div class="border-r border-white/20 pr-8">
                <p class="text-slate-400 text-xs">Captain</p>
                <p class="text-xl">{{ myShip.captain }}</p>
            </div>
            <div class="pl-8">
                <p class="text-slate-400 text-xs">Class</p>
                <p class="text-xl">{{ myShip.class }}</p>
            </div>
        </div>
    </div>
</aside>
```

Sonra `homepage.html.twig` dosyasına dön ve bu kısmı silerek yerine şu satırı ekle:

```twig
{{ include('main/_shipStatusAside.html.twig') }}
```

Denediğinde görünürde hiçbir şey değişmeyecek! Çünkü `include()` ifadesi oldukça basittir:

> Bu şablonu çalıştır ve elimde hangi değişkenler varsa onlarla render et.

Dosya adına neden alt çizgi (`_`) eklediğimi mi soruyorsun? Gerçek bir nedeni yok! Bu sadece bu dosyanın bir sayfa parçası içerdiğini gösteren bir alışkanlık.

---

## 🔁 Twig ile Gemiler Üzerinde Döngü Kurma

### Looping over the Ships in Twig / Twig ile Gemiler Üzerinde Döngü Kurma

Ana sayfa şablonunda şimdi gemi listesinin bulunduğu alt bölüme odaklanabiliriz. Şu anda yalnızca bir gemi var... ve sabit kodlanmış. Hedefimiz, şu anda onarımı yapılan tüm gemileri listelemek. Neyse ki en altta kullandığımız `ships` adında bir değişkenimiz var: bu, Starship nesnelerinden oluşan bir dizi.

Twig içinde ilk defa bir dizi üzerinde döngü kurmamız gerekiyor! Bunun için yorumu kaldırıyoruz ve şöyle yazıyoruz:

```twig
{% for ship in ships %}
```

Burada `ships`, elimizdeki dizi; `ship` ise döngü içinde her bir Starship nesnesini temsil eden yeni değişkendir. Döngünün sonunda da şunu ekliyoruz:

```twig
{% endfor %}
```

Sayfayı yeniden denediğimizde... artık üç adet sabit gemi yerine gerçekten veriden gelen gemileri listeliyoruz! Bu harika bir gelişme!

---

🎯 Sıradaki adım: Bizi bir PHP `enum` oluşturmaya götürecek sürpriz bir dönemeç!
