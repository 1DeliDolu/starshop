# 🦄 Finding & Eliminating Deprecations / Uyarıları (Deprecation) Bulmak ve Ortadan Kaldırmak

Symfony’nun uyarı sistemi (deprecation system) internette benzersizdir: bildiğim kadarıyla başka hiçbir şeye benzemez. Symfony’yi özel kılan şeylerden biri de budur ve bu konuda çok fazla emek harcanır!

## 🔄 How Symfony Changes / Deprecates Features / Symfony Özellikleri Nasıl Değiştirir veya Kullanımdan Kaldırır

Diyelim ki Symfony’de bir şeyi değiştirmek istiyoruz: örneğin bir metodun adını. Metodu doğrudan yeniden adlandıramayız, çünkü bu kodunuzu bozar. Bunun yerine, yeni metot adını ekleriz, eskiyi koruruz, ancak eski metoda küçük bir deprecation kod fonksiyonu ekleriz. Bunu küçük bir sürümde, örneğin `6.3` veya `6.4` olarak yayımlarız. Sonra siz o sürüme yükseltirsiniz ve kodunuz eski metodu çağırdığı için uyarı tetiklenir. Bu uyarılar toplanır ve bunları çeşitli şekillerde görebiliriz – örneğin Web Debug Toolbar’da.

Göreviniz bunları okumak ve kodunuzu yeni metot adını kullanacak şekilde güncellemek. Tüm uyarılar ortadan kalktığında, Symfony 7.0’a güvenle yükseltebilirsiniz. Çünkü unutmayın, Symfony 6.4 ile Symfony 7.0 arasındaki tek fark, eski kod yollarının kaldırılmış olmasıdır. Yani örneğimizde, eski metot adı sonunda 7.0’da tamamen kaldırılır.

Bu süreci çok seviyorum. Symfony’nin kendini sürekli yenilemesine ve uygulamalarımızı güvenli ve öngörülebilir şekilde güncellememize olanak tanıyor. Harika bir sistem.

## 🕵️‍♂️ Hunting Down Deprecations / Uyarıların Peşine Düşmek

Bugün uyarı dedektifleriyiz: bunları bulup ortadan kaldırmaya çalışacağız. Başlamak için öncelikle önbellek klasörümü manuel olarak temizliyorum:

```shell
rm -rf var/cache/*
```

👉 Bu komut, `var/cache` klasörünü temizler ve önbelleğin sıfırdan oluşturulmasını sağlar.

Ana sayfayı yenileyince, önbellek oluşturulacak. Bazı uyarılar sadece önbellek oluşturulurken ortaya çıkar. Tarifleri güncelledikten sonra 3 uyarı kaldı! Güzel!

## ❌ Removing symfony/templating / symfony/templating Paketini Kaldırmak

Uyarılara bakalım. İlk uyarı, bazı templating helper sınıfının kullanımdan kaldırılmasıyla ilgili. Kodumda bunu kullandığımı hatırlamıyorum. İzleyiciye bakınca da çok açıklayıcı değil. Burada bir helper sınıfı var... bir sınıf yükleyici tarafından çağrılmış.

Bu, bir şeyin bu sınıfı kullanmaya çalıştığını ve sınıfın tamamen kullanımdan kaldırıldığını gösteriyor. Aslında tüm `symfony/templating` bileşeni kullanımdan kaldırıldı: Symfony 7.0’da artık hiç yok! Muhtemelen siz de kullanmadınız... ben de uygulamamda kullanmıyorum. O zaman kim kullanıyor?

Bunu öğrenmek için komut satırında şunu çalıştırın:

```shell
composer why symfony/templating
```

👉 Bu komut, `symfony/templating` paketinin neden kurulu olduğunu gösterir.

Ah! Bunu `knplabs/knp-time-bundle` gerektiriyor. Kurulu sürüme bakınca: `1.20.0`. Ancak bu en son sürüm değil: şu anda `2.2` sürümü var. Çok gerideyiz! 2.0 ana sürümü kodu modernize ediyor... ve muhtemelen templating bağımlılığını kaldırıyor. Aşağıda bunu görebilirsiniz.

Bu, paketin yeni bir ana sürümü olduğu için, changelog veya sürüm notlarını incelemek ve geriye dönük uyumsuzluk olup olmadığını kontrol etmek gerekir.

Buradaki ilginç nokta şu: bu ilk uyarı doğrudan bizim çağırdığımız bir şeyden gelmiyor. Bu dolaylı bir uyarı: kullandığımız bir kütüphaneden kaynaklanıyor. Bu oldukça yaygın. Symfony 7’ye hazır olmak için bu bundle’ı yükseltmemiz gerekiyor.

`composer.json` dosyasında "time" kelimesini arayın... sonra en yeni `^2.2` sürümüne güncelleyin. Sonra şunu çalıştırın:

```shell
composer up
```

👉 Bu komut, paketi yükseltir ve `symfony/templating` paketini kaldırır.

## 🚨 DoctrineFixturesBundle False Deprecation / DoctrineFixturesBundle Yanlış Uyarısı

Tekrar önbelleği temizleyin, bazı sekmeleri kapatın ve "browse mixes" sayfasına gidin çünkü orası veritabanına bağlanıyor. Bu sefer iki uyarı görünüyor. Açın ve inceleyin. İlki, data fixtures ile ilgili bir şey. İzleyiciye bakınca çok açık değil ama DoctrineFixturesBundle’dan geliyor. Bu biraz karmaşık: DoctrineFixturesBundle GitHub deposunda bir tartışma bulmam gerekti. Buradaki uyarı yanlış bir uyarı! Bundleda eklenen uyarı katmanı tam doğru eklenmemiş. Maintainer bunun sorun olmadığını doğruluyor... yani Symfony 7’ye geçtiğimizde bir şey bozulmayacak.

Bu biraz garip bir durum ama uyarıların peşine düşmenin zor olabileceğini gösteriyor!

## 📦 Deprecations from Doctrine / Doctrine Kaynaklı Uyarılar

Son uyarı daha uzun ve farklı bir formatta. Şimdiye kadar her mesajda uyarının hangi pakette ve hangi sürümde eklendiği belirtiliyordu. Ama burada göremiyoruz. Ve sonunda doctrine/orm deposundaki bir issue’ya referans var.

Yani! Bu uyarı Symfony’den değil: doctrine/orm’dan geliyor! Bu, paketin bir sonraki ana sürümüne geçmeden önce kodumuzu değiştirmemiz gerektiğini söylüyor. Bugün sadece Symfony’yi yükseltmeye odaklandığımız için, bu uyarıyı görmezden gelebiliriz.

Yani... evet, bence tamamız. Uygulama küçük, sayfalar arasında dolaşınca sadece bu uyarıları görüyorum.

## 📋 Deprecation Log on Production / Canlıda Uyarı Kayıtları

Ama... ya gözden kaçan bir sayfa ya da form gönderimi ile uyarı tetiklenirse? Cevap: loglama.

`config/packages/monolog.yaml` dosyasında, en altta üretim (production) log yapılandırmamız var. Ana handler `nested` handler’dır: bu, canlıda hataları loglar. Hataları `stderr`'e loglar, ya da bunu bir dosyaya da değiştirebilirsiniz.

```yaml
config/packages/monolog.yaml
// ... lines 1 - 39
when@prod:
    monolog:
        handlers:
// ... lines 43 - 48
            nested:
                type: stream
                path: php://stderr
                level: debug
                formatter: monolog.formatter.json
// ... lines 54 - 63
```

👉 Bu yapılandırmada, `nested` handler’ı tüm hataları JSON formatında stderr’e yazar.

Önemli olan: umarım canlıda hatalarınızı bir yerde topluyorsunuzdur. En altta `deprecation` adlı başka bir handler var. Bu, tüm deprecation uyarılarını aynı yere loglar. Yani canlıda hata loglarınızda uyarı mesajlarını da görmelisiniz.

```yaml
config/packages/monolog.yaml
// ... lines 1 - 39
when@prod:
    monolog:
        handlers:
// ... lines 43 - 48
            nested:
                type: stream
                path: php://stderr
                level: debug
                formatter: monolog.formatter.json
// ... lines 54 - 57
            deprecation:
                type: stream
                channels: [deprecation]
                path: php://stderr
                formatter: monolog.formatter.json
```

👉 Bu yapılandırmada, `deprecation` handler’ı deprecation uyarılarını stderr’e JSON olarak kaydeder.

Yani: bulabildiğiniz tüm uyarıları düzeltin, canlıya aktarın, bir iki gün bekleyin, sonra loglarınıza bakıp hâlâ uyarı olup olmadığını kontrol edin. Artık yoksa, Symfony 7.0’a geçmek için hazırsınız. Şimdi bu yükseltmeyi yapalım!
