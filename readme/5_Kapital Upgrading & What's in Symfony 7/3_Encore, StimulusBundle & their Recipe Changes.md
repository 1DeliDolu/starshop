# 🚀 Encore, StimulusBundle & their Recipe Changes / Encore, StimulusBundle ve Tarif Değişiklikleri

Tarifleri güncellemeye devam edelim.

## 🧩 symfony/twig-bundle Recipe Update / symfony/twig-bundle Tarif Güncellemesi

Sırada `TwigBundle` var. Bu, güncellediği tek bir dosyada bir çakışmaya sahip: `templates/base.html.twig`.

Ve... bu biraz garip. Burada kendi özel içeriğimizi görebilirsiniz... ardından aşağıda varsayılan başlık ve varsayılan favicon var. Kendi özel şeylerimizi tutalım ve bu yorumu silelim. Buna ihtiyacımız yok.

Şunu çalıştırın:

```shell
git add templates
```

👉 Bu komut, `templates` dizinindeki değişiklikleri git'e ekler.

Sonra:

```shell
git diff --cached
```

👉 Bu komut, sahnelenmiş değişiklikleri gösterir.

Bu, elbette `symfony.lock` dosyasını gösterir, ancak `base.html.twig` dosyasında bir değişiklik var: `encore_entry_link_tags()` ve `encore_entry_script_tags()` kaldırıldı. Neden?

## 🔀 The Rearranging of Recipes / Tariflerin Yeniden Düzenlenmesi

Symfony frontend dünyasında yakın zamanda yapılan büyük eklemelerden biri `StimulusBundle` idi. Kendi başına, bu çok büyük bir olay değil. Ama, tanıtıldığında, çeşitli tarifler yeniden düzenlendi. Bir paketin tarifinde bulunan bazı değişiklikler, başka bir pakete taşındı.

Örneğin, bu satırlar eskiden `TwigBundle`'ın tarifinin bir parçasıydı, ancak şimdi `WebpackEncoreBundle`'ın tarifine taşındı. Yani `TwigBundle` tarifini güncellediğimizde, bu satırların kaldırılması gerekiyormuş gibi görünüyor.

Tabii ki, bunlara hala ihtiyacımız var, ancak bu değişikliği geçici olarak kabul edin. Bunların daha sonra `WebpackEncoreBundle` tarifini güncellediğimizde tekrar eklendiğini göreceğiz.

## 🧩 symfony/webpack-encore-bundle Recipe Update / symfony/webpack-encore-bundle Tarif Güncellemesi

Tamam, bunu commit edin ve... son tarif güncellememizi yapalım: `WebpackEncoreBundle`!

Ve... daha fazla çakışma. Sorunlar bitmiyor. Şunu çalıştırın:

```shell
git status
```

👉 Bu komut, mevcut git durumunu gösterir.

`package.json` dosyasında bir dizi değişiklik var. Tarif bizi Encore'un 3. sürümünden 4. sürümüne yükseltmeye çalışıyor. 3 ve 4 arasındaki en büyük fark, artık `package.json` dosyanızda bazı paketlere sahip olmanızın sizin sorumluluğunuzda olması; örneğin `webpack` ya da `babel` paketleri gibi.

`version 4`'ü tutalım... ve diğer her şeyi de koruyalım. Bu, eklediğimiz özel paketler ile Encore 4 için gereken yeni paketlerin bir karışımı.

Şunu çalıştırın:

```shell
git add package.json
```

👉 Bu komut, `package.json` dosyasındaki değişiklikleri sahneler.

Sonra `git diff` ile başka ne değiştiğine bakın. Bazı önemsiz ayarlar, `package.json` ve `symfony.lock`. `webpack.config.js` dosyasında bazı düşük seviye değişiklikler var: daha yeni bir `core.js` sürümü kullanılıyor ve `plugin-proposal-class-properties` artık gerekmiyor.

Yani, sıkıcı ama iyi şeyler! Bu tarifi commit edin. Ayrıca az önce `package.json` dosyasını güncellediğimiz için, diğer sekmede `Control+C` ile `yarn`'ı durdurun. Ardından şunu çalıştırın:

```shell
yarn install
```

👉 Bu komut, en son node bağımlılıklarını yükler.

ve

```shell
yarn watch
```

👉 Bu komut, geliştirme modunda izlemeyi başlatır.

Süper, artık Encore 4 ile derleme yapıyoruz! Takım harika gidiyor!

## ⬆️ Upgrading WebpackEncoreBundle to v2 / WebpackEncoreBundle'ı v2'ye Yükseltmek

Encore dünyasındaki en büyük değişiklik gerçekten `StimulusBundle`'ın tanıtılmasıydı. Bununla ilgili olarak, `composer.json` dosyasında `symfony/webpack-encore-bundle` yeni bir ana sürüme sahip. Bunu `^2.0` olarak değiştirin.

Sonra ana terminal sekmesinde şunu çalıştırın:

```shell
composer up
```

👉 Bu komut, PHP bağımlılıklarını günceller.

Bu arada, bu komutun en altta `SensioFrameworkExtraBundle` ile ilgili bir hata ile başarısız olacağını unutmayın. Bir önceki bölümde `framework bundle recipe`'ı güncellerken uygulamamızı biraz bozduk. Bunu sonraki bölümde düzelteceğiz, ama şu an için bir zararı yok.

Peki `WebpackEncoreBundle`'ın 1. ve 2. sürümleri arasında ne değişti? Sadece bir şey: Twig `stimulus_` yardımcı fonksiyonları - örneğin `stimulus_controller()` - kaldırıldı ve yeni `StimulusBundle`'a taşındı. Sorun yok.

Asıl zor olan kısım ise daha önce bahsettiğim gibi: yeni bundle'ın sonucu olarak, birçok tarif parçası paketler arasında yeniden düzenlendi. `encore_entry()` Twig fonksiyonlarının `WebpackEncoreBundle`'ın tarifine taşınmasının yanında, `assets/controllers.json` gibi bazı dosyalar da `WebpackEncoreBundle`'ın tarifinden `StimulusBundle`'ın tarifine taşındı.

Bu gayet iyi: yeni durumda Stimulus ile ilgili dosyalar ilgili bundle'ın tarifinde yaşıyor. Ama... tarifleri yükseltirken biraz karışıklık yaratıyor.

Şimdi adım adım ilerleyelim. Şunu çalıştırın:

```shell
git status
```

👉 Bu komut, mevcut git durumunu gösterir.

Bu değişiklikleri commit edin... sonra tekrar şunu çalıştırın:

```shell
composer recipes
```

👉 Bu komut, mevcut ve güncellenebilir tarifleri gösterir.

Sürpriz! İki yeni güncelleme var! Bunlar nereden çıktı? Az önce `StimulusBundle` ve `WebpackEncoreBundle`'ı yükselttik ve bu yeni sürümlerin yeni tarifleri var.

## 🧩 symfony/stimulus-bundle Recipe Update / symfony/stimulus-bundle Tarif Güncellemesi

`StimulusBundle`'ı güncelleyin. İşte tuhaflık burada başlıyor. Şunu çalıştırın:

```shell
git status
```

👉 Bu komut, mevcut git durumunu gösterir.

`assets/controllers.json` dosyasında bir çakışma var. Bu dosya zaten mevcuttu ve tarif bunu eklemeye çalıştı. Bunun nedeni, artık bu dosyayı eklemekten `StimulusBundle`'ın sorumlu olması... ve zaten burada olduğu için kafası karışmış. Bunu şu şekilde düzeltin: kendi `controllers.json` dosyamızı olduğu gibi tutalım.

Bunu ekleyin, sonra diğer değişikliklere bakmak için `git diff` çalıştırın. Tamam, `app.js` dosyasına bir `import` satırı ekledi. Buna da ihtiyacımız yok çünkü... zaten aşağıda var! Bu da tarifin zaten yapılmış bir şeyi tekrar yapmaya çalışmasının başka bir örneği. Bunu üst kısımdan kaldırın... sonra bu dosyayı git'e ekleyin.

Ve... diğer her şey iyi. Yeni bir `hello_controller.js` verdi, bunu tutabilir veya silebilirsiniz, ayrıca `symfony.lock`. Her şey yolunda.

## 🧩 symfony/webpack-encore-bundle V2 Recipe Update / symfony/webpack-encore-bundle V2 Tarif Güncellemesi

Bunu commit edin... sonra WebpackEncoreBundle için son güncellemeye geçelim. Bu özellikle garip. Şunu çalıştırın:

```shell
git status
```

👉 Bu komut, mevcut git durumunu gösterir.

İki çakışma. Buradaki dosyaların birçoğu eskiden WebpackEncoreBundle'ın tarifinde yer alıyordu, ancak artık çıkarıldı. Yani tarifi yükselttiğimizde bir sürü şeyin silinmesi gerekiyormuş gibi görünüyor. `assets/app.js` dosyasında, bu dosya silinmedi ama içeriğini silmeye çalışıyor. Eski halini olduğu gibi koruyun. Sonra bunu git'e ekleyin.

Sıradaki dosya `package.json`. O da aynı şekilde: bir şeyleri silmeye çalışıyor. Buna izin vermeyin! Kodunuzu koruyun... sonra bu dosyayı da git'e ekleyin.

Şimdi nasıl görünüyor bakalım. `assets/bootstrap.js` dosyasını silmek istiyor - bunu istemiyoruz - ve ayrıca `controllers.json` dosyasını da silmek istiyor. Bunu da istemiyoruz. Hiçbirini istemiyoruz... özellikle de az önce yanlışlıkla yazdığım "G" harfini `package.json` dosyasına! Burada tek ilgilendiğimiz değişiklik şu: `base.html.twig` dosyasında. Ta da! `encore_entry_link_tags()` ve `encore_entry_script_tags()` fonksiyonlarını tekrar ekliyor.

Bu iyi bir değişiklik. Son dosya olan `webpack.config.js` için ise `enableStimulusBridge()` fonksiyonunu kaldırmak istiyor. Ama Stimulus kullandığımız için buna hala ihtiyacımız var. Şunu çalıştırın:

```shell
git reset HEAD
```

👉 Bu komut, tüm dosyaları git'in sahneleme alanından çıkarır.

Sonra şunu çalıştırın:

```shell
git checkout assets webpack.config.js
```

👉 Bu komut, `assets` ve `webpack.config.js` dosyalarındaki değişiklikleri geri alır.

Mükemmel. Geriye sadece `symfony.lock` ve `base.html.twig` dosyaları kaldı. Bunları commit edin.

Ve tamamız! Artık WebpackEncoreBundle'ın en son sürümünü, WebpackEncore'un en yeni sürümüyle birlikte kullanıyoruz ve bu garip, bir defaya mahsus tarif güncellemesini de tamamladık.

Ne yazık ki, daha önce uygulamamızı bozmuştuk. O yüzden sırada: SensioFrameworkExtraBundle'ı kaldırmak var.
