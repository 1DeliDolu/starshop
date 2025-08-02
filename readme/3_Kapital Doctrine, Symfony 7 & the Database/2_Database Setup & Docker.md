# 🐘 Database Setup & Docker / Veritabanı Kurulumu ve Docker

Doctrine yüklü! Ama artık bir veritabanı sunucusu çalıştırmamız gerekiyor.

## `DATABASE_URL` Environment Variable / `DATABASE_URL` Ortam Değişkeni

`.env` dosyamıza bir göz atın. Doctrine'i yüklediğimizde, Flex tarifi bu `doctrine-bundle` bölümünü ekledi. `DATABASE_URL` ortam değişkeni, Doctrine'e veritabanına nasıl bağlanacağını söylediğimiz yerdir. Bu, özel bir URL biçiminde bir dizgedir ve teknik terimle buna DSN denir.

Bu dizge, bağlanmak istediğimiz veritabanı türünü (`mysql`, `postgres`, `sqlite`, `borgsql`, vb.), kullanıcı adını, parolayı, sunucuyu, portu ve veritabanı adını içerir. Herhangi bir sorgu parametresi, ekstra yapılandırmadır.

Varsayılan olarak, `DATABASE_URL`, bir Postgres veritabanına bağlanacak şekilde ayarlanmıştır ve biz de bunu kullanacağız. Docker ile bunu çok kolay bir şekilde çalıştıracağız.

Docker kullanmak istemiyor musunuz? Sorun değil! Bu satırı yorum satırı yapın ve `sqlite` satırının yorumunu kaldırın. SQLite bir sunucu gerektirmez: bu sadece dosya sisteminizde bir dosyadır. Doctrine, veritabanı katmanını soyutladığı için, yazdığımız kodun çoğu her veritabanı türüyle çalışacaktır. Güzel!

Unutmayın, bu dosyada hassas bilgiler saklamayın: bu dosya git deposuna gönderilir. Yerel makinenizde kendi veritabanı sunucunuz varsa, bir `.env.local` dosyası oluşturun (Git tarafından yok sayılır) ve `DATABASE_URL` değişkeninizi orada ayarlayın.

## 🐳 Starting a Postgres Container with Docker / Docker ile Postgres Konteyneri Başlatma

Peki bir Postgres veritabanı sunucusunu nasıl çalıştıracağız?

`compose.yaml` dosyasına bir göz atın. Bu dosya, bir Flex tarifi tarafından eklendi ve Docker yapılandırmasını içerir. Bu yapılandırma içinde, bir Postgres konteynerini çalıştıracak `database` hizmeti yer alır. Harika! İstediğinizi yapabilirsiniz, ancak biz yalnızca veritabanı sunucusunu yerel olarak çalıştırmak için Docker'ı kullanacağız. PHP kendim makinemde normal şekilde kurulu.

Terminali açın ve şu komutu çalıştırın:

```bash
docker compose up -d
```

👉 Bu komut, Docker konteynerlerini başlatır. `-d` seçeneği, işlemin arka planda çalışmasını sağlar.

Ama veritabanı sunucusu nerede çalışıyor? Hangi portta? `DATABASE_URL`'i buna göre güncellememiz gerekmiyor mu?

## 🧙‍♂️ The Symfony CLI is Awesome / Symfony CLI Harika

Hayır! Web sunucusunu çalıştıran `symfony` CLI binary’si bazı Docker sihirlerine sahiptir! Uygulamayı yenileyin. Aşağıda "Server" üzerine gelin. Bu kısım, Symfony CLI sunucusu hakkında ayrıntılar içerir. Bu, Docker konteynerlerini otomatik olarak algıladığı ve ortam değişkenlerini bizim için ayarladığı anlamına gelir!

> 💡 **İpucu** Otomatik algılamanın çalışması için yerel projenizin bir Git deposu olması gerekir. İndirilen kodla ilerliyorsanız, `git init` komutu ile bir depo başlatmanız gerekir.

Gösterelim. Terminale geçin ve şunu çalıştırın:

```bash
symfony var:export --multiline
```

👉 Bu komut, `.env` dosyasındaki değişkenlere ek olarak Symfony CLI tarafından ayarlanan ekstra ortam değişkenlerini gösterir.

Biraz yukarı kaydırın... Ah! İşte burada! `DATABASE_URL`! Bu değer `.env` dosyasındakini geçersiz kılar ve Docker'da çalışan Postgres veritabanına işaret eder. Port numarası rastgele değişir, ancak Symfony CLI her zaman doğru olanı kullanır.

## 🛠️ `symfony console` vs `bin/console`

Symfony komutlarını çalıştırmak için genelde `bin/console` kullanırız. Ancak Docker veritabanı ile Symfony CLI kullanırken, veritabanına özgü komutları `symfony console` ile çalıştırmalıyız. Bu komut, `bin/console` ile aynıdır, fakat Symfony CLI'nin ortam değişkenlerini eklemesine olanak tanır.

## 🗃️ Creating the Database / Veritabanı Oluşturma

Tamam! Docker konteynerinde çalışan bir veritabanı sunucumuz var ve `DATABASE_URL` buna işaret ediyor. Veritabanını oluşturmak için şu komutu çalıştırın:

```bash
symfony console doctrine:database:create
```

👉 Bu komut, veritabanını oluşturur. Eğer hata alırsanız, bu büyük ihtimalle veritabanının zaten var olduğunu belirtir. Bu iyi bir şeydir; veritabanı sunucusuna başarıyla bağlandığınız anlamına gelir!

Artık elimizde Doctrine ve bir veritabanı var. Sırada bir tablo oluşturmak var! Bunu bir sonraki adımda, varlıklar (entities) ve geçişler (migrations) dünyasına atlayarak yapacağız.

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./1_Installing Doctrine.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./2_1_DSN.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
