# ⚙️ Installing Doctrine / Doctrine Kurulumu

Selam arkadaşlar! Symfony 7 serimizin 3. bölümüne hoş geldiniz. Bu bölüm heyecan verici, çünkü uygulamamıza bir veritabanı kazandırıyoruz. Bunu yapmak için yeni bir kütüphaneye ihtiyacımız yok, ama harika olduğu ve kurabiye gibi koktuğu için, Doctrine adında bir kütüphane kullanacağız. Doctrine ve Symfony ayrı projeler olsa da, kuantum dolanık parçacıklar gibi mükemmel bir şekilde birbirine uyarlar. Uzaktan etki — işte bu!

Ben Kevin, bu yolculukta yıldız gemisi kaptanınızım. Başlat!

Benimle veritabanı dünyasında maceraya atılmak için kurs kodunu indirin ve README.md’deki kurulum rehberini takip edin. Son adım olarak — ben bunu zaten yaptım — şunu çalıştırın:

```bash
symfony serve -d
```

👉 Bu komut, [https://127.0.0.1](https://127.0.0.1) adresinde yerel bir web sunucusu başlatır.

Önceki bölümlerdeki Star Shop’a merhaba deyin. Burada, şu anda onarımda olan yıldız gemilerini listelediğimiz bir “Ship Repair Queue” var. Görünüşe göre veriler bir veritabanından geliyor, ama aslında veriler sabit kodlanmış. Sıkıcı!

Şimdi bu uygulamayı veritabanı dünyasına taşımanın zamanı!

## 📦 Requiring Doctrine / Doctrine Bağımlılığını Yükleme

Öncelikle Doctrine’ı yüklememiz gerekiyor. Terminale geçin ve şunu çalıştırın:

```
composer require doctrine
```

👉 Bu komut birçok şey yükler. Ayrıca bazı Flex tarifleri de yapılandırılır. Tariflerden Docker yapılandırması dahil edilip edilmeyeceği sorulursa, kalıcı olarak etkinleştirmek için `p` seçeneğini seçin. Docker bir sonraki bölümde anlatılacak, bu ders için gerekli değil.

Biraz yukarı kaydırarak neler olduğuna bakalım. Yüklediğimiz `doctrine` paketi aslında `symfony/orm-pack` adında bir Flex paketi için bir takma ad. Flex paketleri, birlikte iyi çalışan kütüphane koleksiyonlarıdır. Sonuç: son derece sağlam bir Doctrine kurulumu.

İlk ilginç paket `doctrine/dbal`. `DBAL`, Veritabanı Soyutlama Katmanı (Database Abstraction Layer) anlamına gelir. Yani farklı veritabanı platformlarıyla çalışmak için tutarlı bir yol sunar. MySQL, PostgreSQL, SQLite, vb. Oldukça önemlidir, ama genellikle arka planda çalışır.

İkincisi ise `doctrine/orm`. `ORM`, Nesne İlişkisel Eşleyici (Object Relational Mapper) demektir. PHP nesnelerini veritabanı tablolarına eşlemeye yarayan bir kütüphanedir. Bunun detaylarına ineceğiz.

Bunların dışında, Doctrine’ı Symfony’ye entegre eden bazı ek kütüphaneler ve yeni tablolar eklemek gibi işlemler için kullanacağımız bir göç kütüphanesi de var.

Geri kalanlar, Doctrine için arka plan destek paketleridir ve onları görmezden gelebilirsiniz.

## 🥧 Doctrine Flex Recipes / Doctrine Flex Tarifleri

Ama asıl ilginç olan, bu paketler için Flex tariflerinin neler yaptığıdır. Şunu çalıştırın:

```
git status
```

👉 Bu komutla değişen dosyaları görebilirsiniz.

Değiştirilen dosyalar, standart Flex tarifi dosyalarıdır. `.env` dosyası Doctrine’a özel bazı ortam değişkenleriyle güncellendi — bunları yakında göreceğiz — ve `config/bundles.php` dosyası, yüklenen iki paketi etkinleştirecek şekilde güncellendi.

İzlenmeyen dosyalar, Flex tarifleri tarafından eklenen yeni dosyalardır. Bu `compose*.yaml` dosyaları, bir sonraki bölümde bir veritabanı konteyneri başlatmamıza yardımcı olacak.

`config/packages/` klasöründe iki yeni dosya var — `doctrine.yaml` ve `doctrine_migrations.yaml`. Bunlar iyi varsayılan ayarlara sahiptir; ihtiyaç halinde göz atabilirsiniz.

Tarifler ayrıca boş bir `migrations/` klasörü, boş bir `src/Entity/` klasörü ve boş bir `src/Repository/` klasörü ekledi. Bunların hepsine tek tek değineceğiz.

Tamam! Artık Doctrine kurulu, yani veritabanlarıyla konuşabiliriz... ama aslında henüz bir veritabanı sunucumuz çalışmıyor. Şimdi bir tane başlatalım!

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./2_Database Setup & Docker.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
