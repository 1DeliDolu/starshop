# 🌐 The Prod Environment / Prod Ortamı

Projemizin kök dizinindeki `.env` dosyasını açın ve bu `dev` ortam değişkenini `prod` olarak değiştirin.

```
.env
// ... lines 1 - 17
APP_ENV=prod
// ... lines 19 - 20
```

👉 Bu, uygulamanın çalıştığı ortamı `prod` olarak ayarlar.

`dev` ortamında, bir dinleyici varlıklarımızı dinamik olarak sunar. Ancak `prod` ortamında, bunları elle derlemeniz gerekir. Bunu yapmak için şu komutu çalıştırın:

```
php bin/console asset-map:compile
```

👉 Bu komut, varlık haritasını derler ve prod ortamında gerekli varlıkları oluşturur.

Prod ortamında stilleri göremiyorsanız veya siteyi kullanıcıya sunuyorsanız, bu adım gereklidir.

Yeniden `dev` ortamına geçtiğinizde, varlıkların tekrar dinamik sunulabilmesi için `public/assets/` dizinini silmeniz gerekir. Daha fazla detay için Symfony belgelerine bakınız.

Tarayıcınızda ne değiştiğini görmek için sayfayı yenileyin. Ve... bakın! Web debug toolbarı kayboldu. Şimdi, şablonlarımızdan birinde bir şeyi değiştirmeyi deneyelim. `templates/main/homepage.html.twig` dosyasını açın ve en alt kısımda `Time` ifadesini daha açıklayıcı olması için `Updated at` olarak değiştirin.

```
templates/main/homepage.html.twig
// ... lines 1 - 4
{% block body %}
    <main class="flex flex-col lg:flex-row">
// ... lines 7 - 54
            <div>
// ... lines 56 - 57
                <p>Updated At: {{ issData.timestamp|date }}</p>
// ... lines 59 - 62
            </div>
// ... line 64
    </main>
{% endblock %}
```

👉 Bu değişiklik, zaman bilgisinin yanında `Updated At` (Güncellenme Zamanı) ifadesini gösterir.

## 🧹 Clearing the prod Cache / Prod Önbelleğini Temizleme

Sayfayı yenilediğimizde... hiçbir şey değişmedi. Neden? Performans sebeplerinden dolayı, şablonlar önbelleğe alınır. Şablonu değiştirdikten sonra, tarayıcınız bunu göremez çünkü önbelleğe alınmıştır. Bunu düzeltmek için önbelleği elle temizlememiz gerekir. Terminalde şu komutu çalıştırın:

```
php bin/console cache:clear
```

👉 Bu komut, varsayılan ortamın (burada prod) önbelleğini temizler.

Temizlemek istediğimiz ortam önbelleğini belirtmek için, komutun sonuna örneğin `--env=prod` gibi bir seçenek ekleyebiliriz:

```
php bin/console cache:clear --env=prod
```

👉 Bu komut, sadece `prod` ortamının önbelleğini temizler.

Bu, şu anda çalıştığınız ortamdan farklı bir ortamda bir komut çalıştırmanız gerektiğinde faydalı olabilir. Şu anda zaten prod ortamında geliştirme yaptığımız için bu bölüm gerekli değildir. Eğer bunu çalıştırırsak... güzel! Prod ortamı önbelleği başarıyla temizlendi.

Şimdi tekrar sayfayı yenilersek... ta da! "Updated at" ifadesini görürüz. Harika. Prod ortamında çalışırken ve yaptığınız değişikliklerin (şablonlar, konfigürasyon dosyaları, vb.) tarayıcıda yansımadığını görürseniz, önbelleği manuel olarak temizlemeniz gerekebilir.

## 🛠️ Changing the Cache Adapter for prod Only / Sadece prod İçin Cache Adapter'ını Değiştirme

Şu anda, `cache.adapter.array` kullanıyoruz, bu da aslında sahte bir önbellektir. Bunu `config/packages/cache.yaml` dosyasında görebiliriz. Sahte önbellek geliştirme için iyidir, ancak prod ortamında gerçekten `cache.adapter.filesystem` kullanmak isteriz. Artık `when@` söz dizimini bildiğimize göre bunu kullanalım. Aşağıda, `when@` deyimini bu sefer prod ortamı için `when@prod:` olarak yazıyoruz. Altına, yukarıda gördüğümüz yapıyı tekrar ediyoruz: `framework`, `cache` ve `app` — ardından da `cache.adapter.filesystem`.

```
config/packages/cache.yaml
// ... lines 1 - 23
when@prod:
    framework:
        cache:
            app: cache.adapter.filesystem
```

👉 Bu ayar, prod ortamında dosya tabanlı önbellek kullanılmasını sağlar.

Bunu çalışır halde görmek için (hala prod ortamındayken) önbelleği tekrar temizlememiz gerekir:

```
php bin/console cache:clear
```

👉 Bu komut, değişikliklerin etkili olması için prod önbelleğini tekrar temizler.

Tarayıcıda dikkatlice bakarsanız, verinin yaklaşık beş saniye boyunca önbelleklendiğini ve sonra... yeni verinin geldiğini göreceksiniz! Çalışıyor. `.env` dosyamızda `APP_ENV=prod`'u tekrar `dev` olarak değiştirin. Sayfayı tekrar yenilediğinizde... her yenilemede bir HTTP isteği görürsünüz.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./7_Symfony Environments.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./9_More about Services.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>

Sıradaki konu: Servisler hakkında daha fazla bilgi edinelim.
