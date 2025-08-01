# Debugging with the Amazing Profiler / Harika Profiler ile Hata Ayıklama

Symfony, internetteki en etkileyici hata ayıklama araçlarından bazılarına sahiptir. Ancak Symfony uygulamaları başlangıçta çok küçük kurulduğu için, bu araçlar başlangıçta yüklü değildir. Şimdi bunu düzeltme zamanı. Terminalinize gidin ve daha önce olduğu gibi tüm değişikliklerinizi commit edin, böylece tariflerin neler yaptığını görebiliriz. (Bu adımı zaten yaptık.)

---

### Installing the Debugging Tools / Hata Ayıklama Araçlarını Kurmak

Sonra şu komutu çalıştırın:

```bash
composer require debug
```

Evet! Bu da bir Flex takma adıdır ve bir "pack" kurar. Bu işlem, projemize farklı hata ayıklama özellikleri ekleyen dört farklı paketi yükler. Ardından `composer.json` dosyasını açın.

```json
"require": {
    "symfony/monolog-bundle": "^3.0"
}
```

Pack, `require` anahtarına yalnızca bir satır ekler: `monolog-bundle`. Monolog bir log (kayıt) kütüphanesidir.

Dosyanın en altına, `require-dev` bölümüne üç paket daha eklenmiştir:

```json
"require-dev": {
    "symfony/debug-bundle": "7.0.*",
    "symfony/stopwatch": "7.0.*",
    "symfony/web-profiler-bundle": "7.0.*"
}
```

Bunlara "geliştirme bağımlılıkları" denir, yani prod ortama dağıtım sırasında indirilmezler. Ama yerel geliştirme sırasında normal paketler gibi çalışırlar. Bu üç paket, "profiler" adlı bileşeni çalıştırmak için kullanılır.

Terminale dönüp şu komutu çalıştırın:

```bash
git status
```

Tarifler neler yaptı görelim: bazı temel dosyaları güncelledi, birkaç yeni bundle etkinleştirildi ve bu bundle’lara ait üç yeni yapılandırma dosyası oluşturuldu.

---

### Hello Web Debug Toolbar & Profiler / Merhaba Web Debug Toolbar ve Profiler

Tüm bu kurulumların sonucunu görmek için sayfayı yenileyin. Vay canına! Sayfanın altında siyah bir bar: **web debug toolbar**!

Bu araç çubuğu bilgiyle doludur. Sayfanın route’u ve controller’ı, yüklenme süresi, bellek kullanımı, render edilen Twig şablonu ve render süresi gibi detaylar içerir.

Ama asıl sihirli kısım, herhangi bir bağlantıya tıklayınca açılan **profiler**’da gizlidir. Burada çok daha fazla bilgi vardır: istek ve yanıt ayrıntıları, o sayfa yüklenirken oluşan log'lar, routing detayları ve render edilen Twig şablonları. Görünüşe göre altı şablon render edilmiş: bizim ana şablonumuz, temel layout ve debug toolbar'ı çalıştıran birkaç başka şablon. (Bu şablonlar, prod ortama dağıtıldığında render edilmez.)

Belki de en sevdiğim bölüm: **Performance**. Sayfa yükleme süresini parçalara ayırarak gösterir. Symfony’yi öğrendikçe bu parçaları daha iyi anlayacaksınız. Bu bölüm, sayfanızı yavaşlatan kod parçalarını tespit etmede ve Symfony’nin nasıl çalıştığını daha iyi kavramada çok faydalıdır.

---

### Hello bin/console! / Merhaba bin/console!

Komut satırına geçin ve şu komutu çalıştırın:

```bash
php bin/console
```

Veya çoğu sistemde sadece:

```bash
./bin/console
```

Bu Symfony’nin konsoludur ve sayısız komut içerir. Bunları zamanla öğreneceğiz. Ayrıca kendi komutlarınızı da yazabilirsiniz — bu eğitimin sonunda bunu yapacağız.

Pek çok komut `debug:` ile başlar. Örneğin:

```bash
php bin/console debug:router
```

Bu, uygulamadaki tüm route’ları gösterir: en altta bizim `homepage` route’u ve debug toolbar/profiler’ı destekleyen Symfony’nin geliştirme ortamı route’ları.

Bir başka faydalı komut:

```bash
php bin/console debug:twig
```

Bu komut, uygulamada mevcut olan tüm Twig fonksiyonlarını, filtreleri ve diğer Twig yapılarını gösterir. Bu, Twig dokümantasyonuna benzer, ancak kurulu paketlerin Twig’e eklediği özel filtre ve fonksiyonları da içerir.

---

Bu debug komutları son derece faydalıdır. Eğitimin ilerleyen bölümlerinde daha fazlasını deneyeceğiz.

Sırada: ilk API endpoint’imizi oluşturalım ve Symfony’nin güçlü **serializer** bileşenini keşfedelim.

--- 

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./5_Twig & Templates.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./8_Creating JSON API Endpoints.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
