# 🌱 Environment Variables / Ortam Değişkenleri

Ortam değişkenleri, geliştirdiğimiz ortama bağlı olarak değişen değerler içindir; örneğin, yerel ortam ile üretim ortamı arasında. Bunun en yaygın örneği, veritabanı bağlantı detaylarıdır. Gerçek ortam değişkenlerini işletim sistemimizde ayarlayabiliriz ve çoğu bulut barındırma platformu bu değişkenleri ayarlamayı oldukça kolaylaştırır. Ancak, yerelde bu işi yapmak her zaman en kolay yol değildir. Symfony ayrıca geliştirme sırasında hayatı kolaylaştıran bir `.env` dosyasına sahiptir.

Planımız şu: `iss_location_cache_ttl` değerimizin yerelde ve üretimde farklı olmasını istiyoruz. Üretim ortamında, önbelleğimizin şu anki 5 saniyeden daha uzun sürmesini istiyoruz.

```yaml
# config/services.yaml
parameters:
    iss_location_cache_ttl: 5
// ... lines 8 - 26
 ```

En kolay yol, özel bir ortam değişkeni oluşturup bunu her ortam için (ör. `dev` ve `prod`) farklı bir değere ayarlamaktır.

## 🏗️ Creating and Reading Environment Variables / Ortam Değişkeni Oluşturmak ve Okumak

`.env` dosyamızda, aşağıya büyük harflerle `ISS_LOCATION_CACHE_TTL` yazıyoruz; ortam değişkenlerinde bu standarttır. Varsayılan olarak bunu 5 olarak ayarlayalım.

```dotenv

ISS_LOCATION_CACHE_TTL=5
```

👉 Bu satır, varsayılan olarak ortam değişkeni `ISS_LOCATION_CACHE_TTL`'yi 5 olarak tanımlar.

Şimdi, `services.yaml` dosyasında `iss_location_cache_ttl` parametresini koruyacağız; ancak 5 yerine, az önce oluşturduğumuz ortam değişkenini kullanacağız. Bunu yapmak için özel bir sözdizimi kullanmamız gerekiyor: `%env()%` yazıp yeni `ISS_LOCATION_CACHE_TTL` ortam değişkenimizi seçiyoruz.

```yaml
#config/services.yaml
parameters:
    iss_location_cache_ttl: '%env(ISS_LOCATION_CACHE_TTL)%'
```

👉 Bu satır, `iss_location_cache_ttl` parametresini ortam değişkeninden alır.

## 🧑‍💻 Debugging in Controller / Controller'da Hata Ayıklama

Bunu test etmek için `/src/Controller/MainController.php` dosyasında, `homepage()` fonksiyonunu bulun. Bunun içine, `Response`'dan hemen sonra aşağıdaki satırı ekleyin:

```php
// src/Controller/MainController.php
// ... lines 1 - 17
class MainController extends AbstractController
{
// ... line 20
    public function homepage(
// ... lines 22 - 24
    ): Response {
        dd($this->getParameter('iss_location_cache_ttl'));
// ... lines 27 - 40
    }
}
```

👉 Bu kod, `iss_location_cache_ttl` parametresinin değerini ekrana basar.

## 🧪 Environment Variable Processors / Ortam Değişkeni İşleyicileri

Tarayıcıda sayfayı yenileyin. Şimdi 5'i göreceksiniz. Dikkat ederseniz, bu değer şu an bir string. Tüm ortam değişkeni değerleri varsayılan olarak string olarak gelir, ancak Symfony bu değerleri farklı bir tipe dönüştürebilmemiz için "ortam değişkeni işleyicileri" sağlar. Bunlardan biri, bu değeri bir tamsayıya dönüştürmemize yardımcı olabilir.

Tekrar `services.yaml` dosyasına dönün. Ortam değişkeninin başına `int:` ekleyin:

```yaml
# config/services.yaml
// ... lines 1 - 5
parameters:
    iss_location_cache_ttl: '%env(int:ISS_LOCATION_CACHE_TTL)%'
// ... lines 8 - 26
```

👉 Bu satır, ortam değişkenini tamsayıya dönüştürerek kullanır.

Yenilediğinizde artık gerçek bir tamsayı 5 elde edersiniz. Bu projeyi üretime aktaracak olsaydık, muhtemelen `ISS_LOCATION_CACHE_TTL` değişkenini daha uzun bir süre, mesela 60 olarak ayarlardık; böylece veri 1 dakika boyunca önbellekte kalırdı. Kısa süreli ayar sadece test aşamasında daha kullanışlıdır.

## 📄 The .env.local File / .env.local Dosyası

Buradayken, diğer `.env` dosyalarından da bahsetmek istiyorum. `.env` dosyası Git deposuna eklenir ve burada yaptığınız değişiklikler sahneye alınmamış olarak görünür. Eğer Git deposuna eklemek istemediğiniz bazı sırlarınız varsa (ör. hassas tokenler, şifreler vb.), `.env.local` adında farklı bir dosya oluşturabilirsiniz. Bu dosya, `.gitignore` dosyasında zaten yer aldığı için Git tarafından yok sayılır. Buraya hassas bilgilerinizi yazabilirsiniz ve bu bilgiler depoya eklenmez. Örneğin, `APP_SECRET` ortam değişkenini `.env.local` dosyasına taşıyabiliriz. `.env` dosyasında ise bunu boş bırakabilir veya sahte bir değer atayabiliriz. Diğer geliştiricilerin de bu değişkenleri görüp, kendi `.env.local` dosyalarına gerçek değerleri yazabilmesi için değişkenlerin adını `.env` dosyasında tutmak iyi bir uygulamadır. Bu sadece bir örnekti, isterseniz bunu geri alabilirsiniz.

## 🔬 Debugging Environment Variables / Ortam Değişkenlerini Hata Ayıklama

Bunlara ek olarak, daha az kullanılan `.env.test` ve `.env.prod` dosyaları da vardır. Bunlar sırasıyla yalnızca test ve prod ortamlarında yüklenir. Ayrıca ortam değişkenlerini hata ayıklamak için kullanışlı bir komutumuz var. Terminalde aşağıdakini çalıştırın:

```
php bin/console debug:dotenv
```

👉 Bu komut, ortam değişkenlerinin hangi sırayla yükleneceğini gösterir ve bonus olarak her dosyada hangi ortam değişkenlerinin bulunduğunu listeler.

Şu ana kadar sadece üç tane var ve bunların değerlerini ve hangi dosyada tanımlandıklarını görebiliyoruz.

Eğer hassas bilgilerinizi gerçekten güvenceye almak istiyorsanız, Symfony'nin bunun için özel bir aracı vardır: "Secrets Vault". Google'da "Symfony secrets" aratırsanız, üst sıralarda "How to Keep Sensitive Information Secret" (Hassas Bilgileri Gizli Tutmak) adlı dökümantasyona ulaşırsınız. "Secrets Vault" ile ortam değişkenlerinizi şifreleyerek Git deposuna güvenle ekleyebilirsiniz; şifre çözülmeden okunamazlar. Eğer bu seviyede veri korumasına ihtiyacınız varsa, dökümantasyonu okumanızı veya SymfonyCasts'teki ilgili videoları izlemenizi öneririm. Son olarak, homepage fonksiyonunda yaptığımız değişiklikleri geri alıp, `dd()` satırını kaldırıyorum; artık buna ihtiyacımız yok.

Sonraki: Otomatik yapılandırma hakkında daha fazla konuşacağız.
