## 🔗 Generating URLs in the CLI Environment / CLI Ortamında URL Oluşturma

Asenkron e-posta göndermeye geçtiğimizde, e-posta bağlantılarımızı bozduk! Alan adı olarak `localhost` kullanılıyor; bu garip ve yanlış.

Uygulamaya geri dönelim ve e-postayı gönderen isteğin profiler'ında neler olup bittiğine bakalım. Hatırlayın, artık e-posta "`queued`" (kuyrukta) olarak işaretleniyor. "Messages" sekmesine gidin ve mesajı bulun: `SendEmailMessage`. İçinde bir `TemplatedEmail` nesnesi var. Bunu açın. İlginç! `htmlTemplate` bizim Twig şablonumuz ama `html` null! Bu, o şablondan oluşturulan HTML’in ayarlanmış olması gerekmiyor mu? Bu küçük detay önemli: E-posta şablonu, denetleyicimiz mesajı kuyruğa gönderdiğinde render edilmez. Hayır! Şablon, daha sonra `messenger:consume` komutunu çalıştırdığımızda render edilir.

## 🖇️ Link Generation in the CLI / CLI'da Bağlantı Oluşturma

Peki bu neden önemli? Çünkü `messenger:consume` bir CLI komutudur ve CLI'da mutlak URL oluştururken Symfony, hangi alan adının kullanılacağını (veya http mi https mi olacağını) bilmez. Peki denetleyicide nasıl biliyor? Denetleyicide Symfony, bunu mevcut isteğe göre çözer. CLI komutunda ise istek olmadığı için vazgeçer ve `http://localhost` kullanır.

## 🌐 Configure the Default URL / Varsayılan URL’yi Yapılandırma

Hadi Symfony’ye alan adının ne olacağını söyleyelim.

IDE’mizde, `config/packages/routing.yaml` dosyasını açın. `framework`, `router` altında, yorum satırlarında bu sorun tam olarak açıklanıyor. `default_uri` satırının başındaki yoruyu kaldırın ve değeri `https://universal-travel.com` olarak ayarlayın:

config/packages/routing.yaml

```yaml
framework:
    router:
// ... lines 3 - 4
        default_uri: https://universal-travel.com
// ... lines 6 - 19
```

👉 Bu ayar, komut satırında oluşturulan URL’lerde alan adını belirler.

Geliştirme ortamında ise, yerel geliştirme sunucumuzun URL’sini kullanmamız gerekir. Örneğin benim için bu `127.0.0.1:8000`, ama diğer ekip üyeleri için farklı olabilir. Mesela Bob, `bob.is.awesome:8000` kullanıyor ve gerçekten de öyle.

## 🧑‍💻 Development Environment Default URL / Geliştirme Ortamı Varsayılan URL’si

Bunu yapılandırılabilir yapmak için bir püf noktası var: Symfony CLI sunucusu, alan adı içeren özel bir ortam değişkeni (`SYMFONY_PROJECT_DEFAULT_ROUTE_URL`) ayarlar.

Yeniden routing yapılandırmasına dönün, yeni bir bölüm ekleyin: `when@dev:`, altında `framework:`, `router:`, `default_uri:` ve bunu `%env(SYMFONY_PROJECT_DEFAULT_ROUTE_URL)%` olarak ayarlayın:

config/packages/routing.yaml

```yaml
// ... lines 1 - 6
when@dev:
// ... lines 8 - 10
    framework:
        router:
            default_uri: '%env(SYMFONY_PROJECT_DEFAULT_ROUTE_URL)%'
// ... lines 14 - 19
```

👉 Bu ortam değişkeni yalnızca Symfony CLI sunucusu çalışıyorsa ve komutlar `symfony console` ile çalıştırılıyorsa kullanılabilir.

Değişken eksikse hata almamak için bir varsayılan değer belirleyin. Yine `when@dev` altında, `parameters:` bölümü ekleyip `env(SYMFONY_PROJECT_DEFAULT_ROUTE_URL): 'http://localhost'` olarak ayarlayın:

config/packages/routing.yaml

```yaml
// ... lines 1 - 6
when@dev:
    parameters:
        env(SYMFONY_PROJECT_DEFAULT_ROUTE_URL): 'http://localhost'
// ... lines 10 - 19
```

👉 Bu, bir ortam değişkeni için Symfony’nin standart varsayılan değer tanımlama yöntemidir.

## 🔄 Restart messenger\:consume / messenger\:consume Komutunu Yeniden Başlatma

Test zamanı! Ama önce terminale geçelim. Yapılandırmamızda değişiklik yaptığımız için, messenger\:consume komutunu yeniden başlatmamız gerekiyor ki uygulama yeni ayarları yüklesin:

```bash
symfony console messenger:consume async -vv
```

👉 Bu komut, kuyruk işleyicisini yeni ayarlarla başlatır.

Komut tekrar çalışıyor ve yeni Symfony yapılandırmamızı kullanıyor. Uygulamaya geri dönün ve bir seyahat rezervasyonu yapın! Terminale çabucak bakın… mesaj işlendi.

Mailtrap’a geçin… işte burada! Gerçek an: Bir bağlantıya tıklayın… Süper, artık tekrar çalışıyor! Bob çok mutlu olacak!

## 🏃 Running messenger\:consume in the Background / messenger\:consume Komutunu Arka Planda Çalıştırma

Benim gibiyseniz, geliştirme sırasında bu messenger\:consume komutunun terminalde sürekli açık kalması sıkıcı gelir. Üstelik her kod veya yapılandırma değişikliğinde tekrar başlatmak da can sıkıcı. Yeter! Bir başka Symfony CLI hilesi ile eğlenceyi geri getirelim!

IDE’de `.symfony.local.yaml` dosyasını açın. Bu dosya, uygulamamız için Symfony CLI sunucu yapılandırmasıdır. `workers` anahtarını göreceksiniz; sunucu başlatıldığında arka planda çalışacak işlemleri tanımlar. `tailwind` komutunu zaten eklemişiz.

Yeni bir worker ekleyin. Adı `messenger` olabilir (fark etmez) ve `cmd` olarak şunu ayarlayın: `['symfony', 'console', 'messenger:consume', 'async']`:

.symfony.local.yaml

```yaml
workers:
// ... lines 2 - 5
    messenger:
        cmd: ['symfony', 'console', 'messenger:consume', 'async']
// ... lines 8 - 9
```

👉 Bu, messenger\:consume komutunu arka planda çalıştırır.

Peki ya kod veya yapılandırma değişikliğinde komutun yeniden başlatılması? Sorun yok! Bir de `watch` anahtarı ekleyin ve şunları izletin: `config`, `src`, `templates`, `vendor`:

.symfony.local.yaml

```yaml
workers:
// ... lines 2 - 5
    messenger:
// ... line 7
        watch: ['config', 'src', 'templates', 'vendor']
```

👉 Bu dizinlerdeki herhangi bir dosya değişirse, worker kendini otomatik olarak yeniden başlatır.

Terminalde sunucuyu yeniden başlatın:

```bash
symfony server:stop
symfony serve -d
```

Artık messenger\:consume arka planda çalışıyor! Kanıtlamak için:

```bash
symfony server:status
```

3 worker çalışıyor! Gerçek PHP web sunucusu, mevcut tailwind\:build worker’ı ve yeni messenger\:consume worker’ı. Harika!

Sırada, işlevsel testlerde e-postalarla ilgili nasıl doğrulama yapılır, ona bakacağız!
