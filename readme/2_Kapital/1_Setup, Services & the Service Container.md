## 🚀  Setup, Services & the Service Container / Kurulum, Servisler ve Servis Konteyneri

Symfony ile ne yaparsanız yapın, kullanacağınız en önemli şey servislerdir — uygulamanızda işi yapan küçük sarı minyonlar gibi. Bu bölümde, bu servislerin yapılandırmasından ve ortamlarından bahsedeceğiz.

Bir servis tam olarak nedir? Bu kolay! Bir iş yapan düz (plain) bir PHP sınıfıdır. Örneğin, mesajları kaydetmenize yardımcı olan bir `Logger` bir servistir. Ya da müşterilerinize e-posta gönderen bir `Mailer`. Veya veritabanına sorgu göndermek için kullandığınız bir veritabanı bağlantı nesnesi. Bunların hepsi birer servistir. Hatta istekleri yöneten denetleyici (controller) bile bir servistir — ama onun süper güçleri vardır. Ona daha sonra geleceğiz.

---

### Setup the Course Project / Ders Projesini Kurmak

Bu kursun adı "Temeller" çünkü her şeyin temeli budur. Bu dersten sonra gelen her şey bu temaların birer varyasyonudur. Benimle birlikte kod yazmak istiyorsanız, bu sayfadaki kurs kodunu indirin, zip dosyasını çıkarın ve içinde `start/` klasörünü bulun — burası burada gördüğünüz kodla aynıdır. `README.md` dosyasında bu uygulamayı çalıştırmak için ihtiyacınız olan her şey var. Ben bu adımların çoğunu zaten tamamladım, bu yüzden son adıma geçip Symfony’nin dahili web sunucusunu çalıştıracağım. Bunun için terminali açın ve şunu yazın:

```bash
symfony serve -d
```

`-d` parametresi Symfony’ye bu işlemi arka planda başlatmasını söyler. Uygulama artık [https://localhost:8000](https://localhost:8000) adresinde çalışıyor. Bunu tarayıcıya yapıştırabilirim ama küçük bir kısayol kullanacağım: Mac'te "cmd" veya diğer sistemlerde "control" tuşuna basılı tutarak bu bağlantıya tıklayın ve... Episode 1’de oluşturduğumuz Starshop sitesine tekrar hoş geldiniz.

---

### Services are everywhere / Servisler Her Yerde

Servisler, işi yapan nesnelerdir: Logger, Mailer, veritabanı bağlantısı, hatta denetleyiciler bile. Peki uygulamamızdaki her nesne bir servis midir? Aslında hayır! Verileri tutan nesnelerimiz de var. Örneğin, `Starship` sınıfı bir servis değildir. O sadece düz bir veri nesnesidir. Bu tür nesnelere ihtiyaç duyduğumuzda, onları klasik şekilde örnekliyoruz.

Ama servisler — yani iş yapan nesneler — farklıdır. Elbette bunları elle de örnekleyebiliriz, ama pratikte bunu başka bir şey yapar: **servis konteyneri**. Servis konteyneri, servislerimizin büyük bir hayranıdır. Onlar hakkında her şeyi bilir: sınıf adını, kurucu (constructor) parametrelerini... Bir servisi istediğinizde, onu sizin için oluşturur ve kullanılmaya hazır bir PHP nesnesi olarak döner. Dahası, zekidir: Bir servisi birden fazla kez isteseniz bile, onu yalnızca bir kez oluşturur. Örneğin, uygulamamız yalnızca bir tane logger'a ihtiyaç duyar. Logger'ı beş kez isteseniz bile, aynı nesne size her seferinde döner!

Peki elimizde hangi servisler olduğunu nasıl göreceğiz? Tüm mevcut servislerin listesini görmek için özel bir komut çalıştıracağız. Terminalde şu komutu yazın:

```bash
bin/console debug:container
```

---

### Hello Bundles / Merhaba Paketler

Burada uygulamamızda kullanabileceğimiz uzun bir servis listesi göreceksiniz. Peki bunlar nereden geliyor? Konteynere kim diyor ki “logger” adında bir servis olmalı, sınıfı Logger olmalı ve şu argümanlarla örneklenmeli?

Bazı servisler doğrudan bizim kodumuzdan gelir — bunların nasıl kaydedildiğini birazdan konuşacağız. Ama büyük çoğunluğu **bundle**’lardan gelir. Bundle’lar, Symfony uygulamalarına ekleyebileceğiniz eklentilerdir. Birkaç şey sağlarlar ama en önemlisi servislerdir. Her bundle’ın bir yapılandırma dosyası vardır ve bu dosya şöyle der:

> Hey! “Logger” adında bir servis istiyorum. Bu, “Logger” sınıfının bir örneği olmalı ve şu argümanlarla oluşturulmalı.

Yani servisler birer araçtır ve bundle’lar bize bu araçları sağlar. Kendi kodumuzda `config/bundles.php` dosyasını açalım. Bu dosya, uygulamamızda hangi bundle’ların kayıtlı olduğunu belirler. Bakın! Zaten on tane bundle’ımız var! Bazıları — örneğin `WebProfilerBundle` — sadece belirli bir ortamda (environment) kullanılabilir. `MonologBundle`, `StartshipRepository` içinde log mesajı yazarken kullandığımız Logger servisini sağlar. Veya `TwigBundle` satırını tamamen silersek, denetleyicilerimizde kullandığımız `render()` metodu artık çalışmaz. Çünkü arka planda bu metod, şablonları işlemek için `twig` servisini kullanır. Buna daha sonra değineceğiz.

Sıradaki konu: Uygulamanıza yeni servisler kazandırmak için yeni bundle’lar nasıl yüklenir?
