## ⚙️ Servisler (Services)

### \[What is a Service?] / \[Servis Nedir?]

İlk olarak, **servis** bir işi yapan bir nesnedir. Hepsi bu.
Örneğin, bir `Logger` nesnesi oluşturduysanız ve bunun `log()` metodu varsa, bu bir servistir! Bir işi yapar: bir şeyleri loglar!
Veya bir veritabanı bağlantısı nesnesi oluşturduysanız ve bu nesne veritabanına sorgular gönderiyorsa... evet! Bu da bir servistir.

Peki... bir servis sadece iş yapan bir nesne ise... hangi "tembel" nesneler servis değildir?
`Starship` sınıfımız bunun mükemmel bir örneğidir.
Asıl görevi iş yapmak değildir: veriyi tutmaktır.
Elbette, birkaç public metodu olabilir... hatta bu metodlara biraz mantık da ekleyebilirsiniz.
Ama nihayetinde bu bir işçi değil, bir veri tutucudur.

Peki ya controller sınıfları? Evet, onlar da servistir.
Yaptıkları iş **response** nesneleri oluşturmaktır.

Symfony'de yapılan her iş aslında bir servis tarafından yapılır.
Log mesajlarını bir dosyaya mı yazıyorsunuz? Evet, bunun için bir servis var.
Güncel URL ile eşleşen rotayı mı buluyorsunuz? Bu, yönlendirici (router) servisidir!
Twig şablonunu render etmek mi? Evet, `render()` metodu aslında doğru servis nesnesini bulup onun bir metodunu çağıran bir kısayoldur.

---

## 🧰 Container ve `debug:container`

### \[The Container & debug\:container] / \[Container ve debug\:container]

Bu servislerin büyük bir nesne içinde organize edildiğini de duyabilirsiniz: bu nesneye **service container** (servis kabı) denir.
Container’ı, her biri benzersiz bir ID’ye sahip olan servis nesnelerinden oluşan dev bir ilişkisel dizi gibi düşünebilirsiniz.

Uygulamamızdaki tüm servislerin listesini görmek mi istiyorsunuz? Ben de!

Terminali açın ve şunu çalıştırın:

```
bin/console debug:container
```

Bu bir sürü servis! Her biri kendi satırında daha iyi okunuyor.

Sol tarafta her servisin **ID'si**, sağ tarafta ise bu ID’nin karşılık geldiği **sınıf** var. Güzel, değil mi?

Controller’ımıza geri dönün ve `json()` metodunun üstüne gelin ve Ctrl (veya Cmd) tuşuna basarak içine girin.
Artık daha mantıklı geliyor!
Container’da `serializer` ID’sine sahip bir servis var mı diye kontrol ediyor.
Eğer varsa, o servisi container’dan alıyor ve `serialize()` metodunu çağırıyor.

Servislerle çalışırken tam olarak böyle görünmeyecek.
Ama önemli olan artık ne olup bittiğini anlıyoruz.

---

## 🧩 Bundle’lar Servis Sağlar

### \[Bundles Provide Services] / \[Bundle’lar Servis Sağlar]

Peki bu servisler nereden geliyor?
Yani, kim “`twig` ID’sine sahip bir servis olmalı ve bu bir `Twig\Environment` nesnesi olmalı” diyor?
Cevap: tamamen **bundle**’lardan geliyor.
Yeni bir bundle kurmanın temel amacı da budur.
**Bundle’lar bize servisler sağlar.**

Twig’i kurduğumuz zamanı hatırlıyor musunuz?
Uygulamamıza bir bundle ekledi.
Ve bu bundle ne yaptı? Evet: bize yeni servisler sağladı, `twig` servisi dahil.
**Bundle’lar bize servis sağlar... ve servisler birer araçtır.**

---

## 🧠 Autowiring (Otomatik Bağlantı)

### \[Autowiring] / \[Otomatik Bağlantı]

Bu listedeki servislerin çoğu düşük seviyeli servislerdir ve çoğu zaman bizim için önemli değildir.
Ayrıca, genellikle bu servislerin ID’sini bilmemiz gerekmez.

Bunun yerine şu komutu çalıştırın:

```
php bin/console debug:autowiring
```

Bu komut, **autowireable** (otomatik bağlanabilir) olan tüm servisleri gösterir.
Yani ihtiyaç duyacağımız servislerin özel bir listesini.

---

## 📝 Logger Servisini Autowire Etmek

### \[Autowiring the Logger Service] / \[Logger Servisini Otomatik Bağlamak]

Bir deneme yapalım: Controller'dan bir şey loglayalım.
Düşünce tarzı şu şekilde olabilir:

> Bir şey loglamam lazım!
> Loglama bir iştir.
> İş yapan şeyler servistir!
> O zaman, bu işi yapan bir **logger** servisi olmalı!

Komutu tekrar çalıştırın ama bu kez "log" için arama yapın:

```
php bin/console debug:autowiring log
```

İşte bu! `Psr\Log\LoggerInterface` ile başlayan yaklaşık 10 servis buldu.
Şimdilik asıl odaklanmamız gereken servis bu.
Yani container’da bir **logger servisi** var ve ona bu interface üzerinden erişebiliriz.

Bu ne anlama geliyor? Controller metodumuza `LoggerInterface` tipiyle bir argüman ekleyin:

```php
use Psr\Log\LoggerInterface;

class StarshipApiController extends AbstractController
{
    public function getCollection(LoggerInterface $logger): Response
    {
        dd($logger);
    }
}
```

Argümanın adı önemli değil: `$logger`, `$log`, `$banana` olabilir.
Önemli olan **tip ipucu** (`LoggerInterface`) ile `debug:autowiring` çıktısındaki `Psr\Log\LoggerInterface` değerinin eşleşmesidir.

Symfony bu eşleşmeyi görür ve:

> Bu tip ipucu bir servisin tipine uyuyor. Demek ki bu servisi enjekte etmeliyim.

Sayfayı yenileyin: evet! Nesne güzelce dump edildi ve işlem durdu.
Symfony bize bir `Monolog\Logger` nesnesi verdi.

---

## 🧬 Autowiring Nerelerde Geçerli?

Autowiring şu **iki yerde** geçerlidir:

1. Controller metodları
2. Servislerin `__construct()` metodları

İkinci durumu bir sonraki bölümde göreceğiz.

---

## ⚙️ Servis Davranışını Ayarlamak

### \[Controlling how Services Behave] / \[Servislerin Davranışını Kontrol Etmek]

Logger servisi nereden geliyor?
Cevabı zaten biliyoruz: bir bundle'dan.
Bu örnekte: **MonologBundle**.

Peki bu servisin davranışını nasıl ayarlarız, mesela başka bir dosyaya log yazması için?

Cevap: `config/packages/monolog.yaml` dosyası.

Bu yapılandırma (örneğin `%kernel.logs_dir%/dev.log`), MonologBundle’a ne yapması gerektiğini söyler.
Yani bu bundle’ın sağladığı servislerin davranışını belirler.

---

## 🧪 Logger Kullanımı

### \[Using the Logger] / \[Logger Servisini Kullanmak]

Logger servisini aldığımıza göre, onu kullanalım!
Nasıl mı? Editor’ünüz size yardımcı olacak.
`LoggerInterface` içinde birçok metod var. Biz `->info()` metodunu kullanalım:

```php
use Psr\Log\LoggerInterface;

class StarshipApiController extends AbstractController
{
    public function getCollection(LoggerInterface $logger): Response
    {
        $logger->info('Starship collection retrieved');
    }
}
```

Sayfayı yenileyin. Sayfa çalıştı... ama loglandı mı?
`var/log/dev.log` dosyasına bakabilirsiniz.
Veya Symfony Profiler’ın Log bölümünü kullanabilirsiniz.

---

## 🧭 API İsteğinde Profiler’ı Görmek

### \[Seeing the Profiler for an API Request] / \[Bir API İsteği İçin Profiler’ı Görüntülemek]

Ama durun! Bu bir API isteği... yani ekranın altında o güzel debug araç çubuğu yok!
Doğru... ama Symfony bu bilgileri yine de toplar.

Bu isteğin profiler’ına erişmek için URL’yi `/ _profiler` olarak değiştirin:

```
https://localhost:8000/_profiler
```

Bu sayfa, uygulamamızdaki en son istekleri listeler. En yenisi en üsttedir.
Bir dakikadan önce yaptığımız API isteğini bulun, sağdaki token’a tıklayın: işte karşınızda tam teşekküllü profiler!

Log bölümünde mesajımızı göreceksiniz:

> Starship collection retrieved

---

Şimdi bir servisin nasıl kullanılacağını gördük.
Sıradaki adım: **kendi servislerimizi yazmak!**
