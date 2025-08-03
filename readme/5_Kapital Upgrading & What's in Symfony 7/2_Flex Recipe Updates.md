## 🔄 Flex Recipe Updates / Flex Tarif Güncellemeleri

Paketleri kurduğumuzda, çoğunun `Flex` tarifleri vardır. Bu tarifler yeni dosyalar ekler ve bazen mevcut dosyaları değiştirir. Paketin hemen çalışması için gereken her şeyi yaparlar. Bu harika!

Zamanla, bu tarifler genellikle değişir. Belki bir yapılandırma dosyasına yeni bir satır eklemeye veya bir varsayılan değeri değiştirmeye karar verirler.

Neyse ki, `Flex`'in gelişmiş bir tarif güncelleme sistemi vardır. Tariflerinizi güncellemeniz gerekmese de, bu uygulamanızı modern tutmak için harika bir yoldur. Ayrıca, bu güncellemeler önceki bölümün sonunda gördüğümüz bazı `deprecation` uyarılarını da düzeltmeye yardımcı olur.

Başlamadan önce, herhangi bir değişikliği `git`'e kaydettiğinizden emin olun — ben zaten yaptım — çünkü tarif güncelleme sistemi `Git` üzerinden çalışır.

Tarifleri görmek için şu komutu çalıştırın:

```shell
composer recipes
```

👉 Bu komut, mevcut tarifleri listeler.

Güzel! Görünüşe göre yaklaşık 8 güncellememiz var. Şimdi işe koyulalım:

```shell
composer recipes:update
```

👉 Bu komut, tarifleri günceller.

Tarifleri güncellemek mi? Evet, en sevdiğim şeylerden biri: bu paketlerde nelerin değiştiğini görmemize olanak tanır... biz esas işimizi yaparken, yani. Listede tek tek ilerlemek için enter'a basıyorum.

## 🧩 doctrine/doctrine-bundle Recipe Update / doctrine/doctrine-bundle Tarif Güncellemesi

İlk olarak Doctrine Bundle var: ve bu karmaşık bir güncelleme. Hatta bir çatışmaya bile neden oldu!

Bazen bir tarif güncellemesinin bir şeyi değiştirdiğini — örneğin bir yapılandırma dosyasında bir satırı güncellediğini — görebiliriz ama nedenini tam olarak anlamayabiliriz. Bunu anlamaya yardımcı olmak için, komut bu değişikliklerin arkasındaki her bir `pull request`'i listeler. Örneğin, bu lazy ghosts olayı... Bağlantıya tıklayarak PR ve arkasındaki açıklamayı görebiliriz.

Editörde, vay canına! Çatışma `doctrine.yaml` dosyasındaymış! Özellikle, `server_version` değişmiş. Orijinal tarif bize `Postgres 13` ile çalışmak için yapılandırma vermişti. Şimdi ise `Postgres 16` için kod geliyor.

Yeni değişiklikleri tutmanız gerekmiyor. Üretim veritabanınız `Postgres 13` kullanıyorsa, onu tutun! Ama ben 16'ya güncelliyorum.

Terminalde şu komutu çalıştırın:

```shell
git status
```

👉 Bu komut, dosya durumunu gösterir.

Bu dosyayı ekleyerek çözün. Ardından tüm değişiklikleri görmek için şunu çalıştırın:

```shell
git diff --cached
```

👉 Bu komut, aşamaya alınmış değişiklikleri gösterir.

Çoğu değişiklik sürüm güncellemesi: `MySQL` 5.7'den 8'e ve `Postgres` 13'ten 16'ya. `doctrine.yaml` yapılandırmasında birkaç yeni satır var. Bunlar, sistemde bazı düşük seviyeli değişikliklere opt-in olduğumuz yerler. Bu yapılandırmaya sahip olmamak büyük olasılıkla bir `deprecation` tetikleyecektir. Bunların derinlemesine incelemesini size bırakıyorum ama muhtemelen hiçbir şeyi etkilemeyecekler.

`docker-compose.yaml` dosyasında daha fazla değişiklik var; burada da `Postgres 13`'ten 16'ya geçiş yapılmış. Yine, bunları tutabilir veya kaldırabilirsiniz.

Ve en altta, `symfony.lock` dosyası hangi tarif sürümünün kurulu olduğunu takip eder. Yani, tamamız! Bu değişiklikleri `commit` edin... ve benimkinden daha iyi bir `commit message` kullanın.

`docker-compose.yaml` dosyasından yeni `Postgres` sürümünü kullanmak için şunu çalıştırın:

```shell
docker compose down
```

👉 Bu komut, tüm konteynerleri durdurur.

Sonra şunu çalıştırın:

```shell
docker compose up -d
```

👉 Bu komut, konteynerleri arka planda başlatır.

Artık `Postgres 16` çalışıyor. Bakın: ana sayfa hâlâ çalışıyor çünkü veritabanına erişmiyor. Ama "browse mixes"e tıkladığınızda, hata! Tanımsız tablo çünkü yepyeni bir veritabanı kullanıyoruz. Bunu düzeltmek için şunu çalıştırın:

```shell
symfony console doctrine:migrations:migrate
```

👉 Bu komut, veritabanı şeması değişikliklerini uygular.

Güzel. Ve:

```shell
symfony console doctrine:fixtures:load
```

👉 Bu komut, örnek verileri yükler.

Şimdi... tamamız!

## 🔁 doctrine/doctrine-migrations-bundle Recipe Update / doctrine/doctrine-migrations-bundle Tarif Güncellemesi

Tekrar terminale dönelim... ve işe devam:

```shell
composer recipes:update
```

👉 Bu komut, tarifleri bir kez daha günceller.

Sırada `doctrine-migrations-bundle` var. Bu küçük bir güncelleme. Paket, profiler entegrasyonu ile birlikte gelir: bu, web debug araç çubuğunda küçük bir simge. Çok kullanışlı değil... bu yüzden varsayılan olarak devre dışı bırakıldı. Bunu `commit` edelim... ve sıradakine geçelim.

```shell
composer recipes:update
```

👉 Bu komut, tarif güncellemelerine devam eder.

## 🏗️ symfony/framework-bundle Recipe Update / symfony/framework-bundle Tarif Güncellemesi

Framework bundle! Symfony'nin kalbi! Değişiklikleri görmek için şunu çalıştırın:

```shell
git diff --cached
```

👉 Bu komut, aşamaya alınmış değişiklikleri gösterir.

Doctrine gibi, bunların çoğu düşük seviyeli, yeni bir davranışa opt-in olduğumuz değişiklikler. Örneğin, `annotations` artık kullanımdan kalktığı için devre dışı bırakıyoruz. `handle_all_throwables` sayesinde Symfony, istisnaları hata sayfalarına dönüştürür ama aynı zamanda başka hata türlerini de işler. Ve `storage_factory_id` kaldırıldı çünkü bu artık varsayılan değer.

Kolay! Bunu commit edin... ve devam edin:

```shell
composer recipes:update
```

👉 Bu komut, diğer tarifleri günceller.

## 📋 symfony/monolog-bundle Recipe Update / symfony/monolog-bundle Tarif Güncellemesi

Sırada `monolog-bundle` var. Tek değişiklik, `monolog.yaml` dosyasının sonunda yeni bir `formatter` anahtarı. Bu bir tutarlılık değişikliği. Prod yapılandırmasındaki ana log handler zaten bu `formatter` anahtarına sahipti. Her şeyin aynı formatta olması için deprecatıons altında eklendi. Küçük ama güzel! Bu `deprecation` logunu yakında daha fazla konuşacağız.

Yani, commit edin! Ve...

```shell
composer recipes:update
```

👉 Bu komut, güncellemeye devam eder.

## 🗺️ symfony/routing Recipe Update / symfony/routing Tarif Güncellemesi

Routing. Çok basit. `#[Route]` attributelerini içe aktaran kodun görünüşe göre bir `namespace` anahtarına ihtiyacı var. Neyse.

## 🌐 symfony/translation Recipe Update / symfony/translation Tarif Güncellemesi

Commit edin... ve sıradaki

```shell
composer recipes:update
```

👉 Bu komut, tarif güncellemelerini tamamlar.

Sırada `symfony/translation` var. Bu da kolay: `translation.yaml` eskiden örnek olarak bazı yorum satırı halindeki sağlayıcılar içeriyordu... şimdi ise çıkarılmışlar. Ama bu sağlayıcı paketlerinden birini kurarsanız, tarifi o satırı yeniden ekler.

Commit edin... ve son 2 tarife geldik! Bunlar her ikisi de `Webpack Encore`'da ve yeni bir `StimulusBundle` ile ilgili değişikliklerle alakalı. Bu da bir sonraki bölümün konusu olacak!

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./1_Upgrading & What's in Symfony 7.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./3_Encore, StimulusBundle & their Recipe Changes.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
