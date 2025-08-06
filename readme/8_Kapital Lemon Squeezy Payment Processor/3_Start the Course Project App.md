# 🚀 Start the Course Project App / Kurs Proje Uygulamasını Başlat

Sizi çok iyi anlıyorum. "`ödeme sistemi entegrasyonu`" ifadesi kulağa oldukça korkutucu gelebilir, ama bu kursun sonunda şöyle diyeceksiniz:

LemonSqueezy ile her şey çocuk oyuncağı!

Haydi projemizi inceleyelim! Önceki bölümde kurs kodlarını indirmiştik, ancak henüz indirmediyseniz şimdi bu sayfadan indirebilirsiniz. Zip dosyasını açın, `start/` dizinine gidin ve kurulum talimatları için mutlaka `README.md` dosyasını kontrol edin. Ben bu adımları zaten tamamladım, bu yüzden web sunucusunu başlatıyorum:

```bash
symfony serve -d
```

👉 Bu komut, Symfony web sunucusunu arka planda başlatır.

Ve bu URL’ye tıklıyorum.

Hoş geldiniz: "Squeeze the Day" — dijital limonata tezgâhımız! Burada bazı ürünlerimiz var, sepete ekleyebiliyoruz ve miktar da belirleyebiliyoruz. `Sepet` sayfası — yani ödeme yapılacak kısım — LemonSqueezy entegrasyonuna ihtiyaç duyduğumuz yer olacak.

Ayrıca abone olanlar için haftalık limonata teslimatı da sunuyoruz! Ne kadar pratik! `Abonelikler` konusuna sonraki kursta değineceğiz. Kayıt olabilir, giriş yapabilir ve bazı temel hesap bilgilerini görüntüleyebiliriz. Hadi bunu yapalım!

`AppFixtures.php` dosyasında bazı varsayılan kullanıcı bilgileri var. Bu e-posta adresini kopyalayın, giriş sayfasına dönün ve e-posta alanına yapıştırın.

```php
// src/DataFixtures/AppFixtures.php
// ... lines 1 - 9
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->create([
            'email' => 'lemon@example.com',
            'plainPassword' => 'lemonpass',
            'firstName' => 'Lemon',
        ]);
// ... lines 19 - 57
    }
}
```

👉 Bu kod, varsayılan bir kullanıcı oluşturur: `lemon@example.com` / `lemonpass`.

Parola: `lemonpass`. "Sign In" butonuna tıklayın... ve "Account" sayfasını kontrol edin. Şu an için oldukça temel bilgiler yer alıyor.

## 📡 Install HTTP Client / HTTP İstemcisini Kur

Tamam, artık `API` ile çalışmaya başlayabiliriz. Yardım almak için belgeleri kontrol edelim. `lemonsqueezy.com` sitesine gidin... "Resources", "Developer Docs", ardından "Guides" kısmına tıklayın. Sonra "`Getting Started with the API`" başlıklı bölümü bulun. Görünüşe göre önce bir `API anahtarı` oluşturmamız gerekiyor (bunu birazdan yapacağız), ardından tüm API isteklerini belirli bir `alan adı` ve `header` bilgileriyle yapmamız gerekiyor. LemonSqueezy ayrıca kimlik doğrulama istiyor, bu yüzden bir `authorization token` (yetkilendirme belirteci) de iletmemiz gerekiyor. Symfony uygulamamızdan `API istekleri` göndermek için Symfony’nin `HttpClient` bileşenini kullanabiliriz, bu iş için oldukça uygundur.

Terminalde şu komutu çalıştırın:

```bash
composer require symfony/http-client
```

👉 Bu komut, Symfony HTTP Client bileşenini kurar.

Görünüşe göre bu bileşen zaten dolaylı bir bağımlılık olarak kuruluymuş, Composer sadece `composer.json` dosyasına doğrudan eklemiş oldu. Harika. Şimdi yapılandıralım! `config/packages/` içinde `http_client.yaml` adında yeni bir dosya oluşturun.

## 🧩 Create a Scoped HTTP Client / Scoped (Sınırlandırılmış) Bir HTTP İstemcisi Oluşturun

Yeni dosyamızda, LemonSqueezy API’sine istek göndermemize yardımcı olacak `scoped client` tanımlayacağız: `framework:`, `http_client:`, `scoped_clients:`. Adı `lemon_squeezy.client` olacak. Ardından `base_uri:` satırına belgelerde "Making requests" kısmında verilen URL’yi kopyalayıp yapıştırın: `'https://api.lemonsqueezy.com/v1/'`. Sonra `headers:` altında `Accept:` ve `Content-Type:` için `'application/vnd.api+json'` olarak ayarlayın.

```yaml
config/packages/http_client.yaml
framework:
    http_client:
        scoped_clients:
            lemon_squeezy.client:
                base_uri: 'https://api.lemonsqueezy.com/v1/'
                headers:
                    Accept: 'application/vnd.api+json'
                    Content-Type: 'application/vnd.api+json'
```

👉 Bu yapılandırma, LemonSqueezy API’sine JSON formatında istek göndermek için özel bir HTTP istemcisi oluşturur.

## 🔑 API Anahtarını Oluştur ve Sakla

`Authorization` için bir `Bearer token` (taşıyıcı jeton) eklememiz gerekiyor, ancak önce bir `API anahtarı` oluşturalım. LemonSqueezy panosunu açın ve "Settings", ardından "API" kısmına gidin. `"Add API key"` butonuna tıklayın. Adını `"API"` olarak belirleyin, `"Create API key"` butonuna tıklayın ve oluşturulan anahtarı kopyalayın. Bu gizli bilgi, yani... bunu görmemiş gibi yapın. Ben sonra daha gizli bir anahtar oluşturacağım.

Bu bilgiyi gizli tutmak için `.env` dosyasına kaydetmek istemiyoruz çünkü bu dosya `versiyon kontrol sistemine` dahil edilir. Güvende tutmak için `.env.local` dosyası oluşturup buraya kaydediyoruz. Bu dosya versiyon kontrolüne dahil edilmez ve bu yüzden güvenlidir. Şöyle yazın:

```
# .env.local
LEMON_SQUEEZY_API_KEY={your_api_key}
```

👉 Bu satır, API anahtarını yerel ortamda güvenli şekilde saklar.

Daha sonra `.env` dosyasına da aşağıdaki satırı ekleyin:

```env
LEMON_SQUEEZY_API_KEY=
```

👉 Bu satır, uygulamanın çalışması için bu ortam değişkenine ihtiyaç duyduğunu belirtir.

Üretim ortamında (production), bu değeri `bulut barındırma hizmetinizde ortam değişkeni` olarak ayarlayabilirsiniz. Daha da güvenli hale getirmek isterseniz, Symfony'nin `secrets` yönetim sistemine göz atabilirsiniz. Daha fazla bilgi için `symfony.com` sitesindeki belgelere bakın.

Son olarak `http_client.yaml` dosyasına dönün ve `base_uri` altına şu satırı ekleyin:

```yaml
auth_bearer: '%env(LEMON_SQUEEZY_API_KEY)%'
```

```yaml
config/packages/http_client.yaml
framework:
    http_client:
        scoped_clients:
            lemon_squeezy.client:
                base_uri: 'https://api.lemonsqueezy.com/v1/'
                auth_bearer: '%env(LEMON_SQUEEZY_API_KEY)%'
                headers:
                    Accept: 'application/vnd.api+json'
                    Content-Type: 'application/vnd.api+json'
```

👉 Bu, API isteklerine otomatik olarak `Authorization` başlığı ekler.

Şimdi LemonSqueezy API’sine HTTP isteği göndermeye hazırız! Bir sonraki adımda bunu yapacağız.
