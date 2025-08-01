# 🧩 How autowiring works / Otomatik Bağlamanın Nasıl Çalıştığı

Hey, bak! Favori komutumuz burada!

```
php bin/console debug:autowiring
```

👉 Bu komut, kodumuzda otomatik olarak bağlayabileceğimiz servislerin bir listesini gösterir.

Ama otomatik bağlama (autowiring) aslında nasıl çalışıyor? Bir başka komutu çalıştıralım:

```
php bin/console debug:container
```

👉 Bu komut bize devasa bir servis listesi verir ve eğer bir Servis Kimliği (Service ID) bir sınıf veya arayüz adıysa, otomatik olarak bağlanabilir demektir. Yani, bu servisi kendi servisimizin yapıcı metodunda (constructor) typehint olarak kullanabiliriz ve servis konteyneri bu servisi otomatik olarak enjekte eder. Tersine, eğer bir Servis Kimliği bir arayüz veya sınıf adı değilse, otomatik bağlanamaz. Bu kasıtlı olarak böyledir, çünkü çoğu servis düşük seviyeli servislerdir ve sahne arkasında diğer servislere yardımcı olmak için vardır. Bu düşük seviyeli servisleri doğrudan kullanmamıza nadiren gerek olur ve bu yüzden onları otomatik bağlama yoluyla elde edemeyiz. İşte bu yüzden debug\:container komutunda debug\:autowiring komutuna göre çok daha fazla giriş bulunur.

## Debugging the Container / Servis Konteynerini Hata Ayıklama

Servis konteyneri, temelde her servisin kendine özgü bir adı olan ve bu ismin ilgili servis nesnesine işaret ettiği dev bir dizi gibidir. Örneğin twig servisi için, konteyner bu servisi örneklemek (instantiate) için Twig\Environment sınıfının bir örneğini oluşturması gerektiğini bilir. Ve burada argümanları görmesek bile, hangilerinin geçirilmesi gerektiğini tam olarak bilir. Ek olarak, eğer aynı servisi birden fazla yerde talep edersek, servis konteyneri yalnızca bir örnek oluşturur, böylece her yerde tam olarak aynı örneği kullanırız.

Ayrıca bu servis sınıflarını fark etmiş olabilirsiniz. Mesela CacheInterface, daha önce cache.app servisi için bir takma ad (alias) olarak kullanıldı. Bu, cache.app gibi bir servisin otomatik bağlanabilir olmasını sağlamanın bir yoludur. Bu servislerin büyük çoğunluğu snake case isimlendirme stratejisini kullanır, bu nedenle bunları kodumuzda otomatik bağlanabilir yapmak için, paketler bazı takma adlar (sınıf isimleri veya arayüzler) ekler. Böylece, kodumuzda typehint olarak kullanabiliriz. Takma adlar, esasen diğer servislere işaret eden sembolik bağlantılar gibidir. Ancak, bazen konteynerde aynı sınıfı veya arayüzü uygulayan birden fazla servis olabilir.

## 📦 Custom Cache Pool / Özel Önbellek Havuzu

Bunu ele almak için, kodumuzda özel bir önbellek havuzu oluşturalım. config/packages/cache.yaml dosyasında, aşağıda, pools anahtarının yorumunu kaldırın ve bu örnek yerine iss_location_pool: null yazın.

```
framework:
    cache:
        // ... lines 3 - 19
        pools:
            iss_location_pool: null
```

👉 Bu YAML yapılandırması, iss_location_pool adında yeni bir önbellek havuzu tanımlar.

Şimdi terminalde şu komutu çalıştırın:

```
php bin/console debug:autowiring
```

👉 Bu yapılandırma, aynı CacheInterface'i kullanan yeni bir servis (iss_location_pool) ekler.

Şimdi src/Controller/MainController.php dosyasında, homepage() fonksiyonunda değişken adını \$issLocationPool olarak değiştirin ve CacheInterface typehint'ini aynı bırakın. Bu değişken adını kopyalayıp aşağıda kullanın.

```
class MainController extends AbstractController
{
    public function homepage(
        CacheInterface $issLocationPool,
    ): Response {
        $issData = $issLocationPool->get('iss_location_data', function (ItemInterface $item) use ($client): array {
            // ...
        });
    }
}
```

👉 Bu, "isimli otomatik bağlama" (named autowiring) olarak adlandırılır; servis konteyneri değişken adı ve typehint'e bakarak doğru servisi enjekte eder. Bu nadir bir durumdur ama logger servisinde de görebiliriz.

Tarayıcınızı yenileyin ve önbellek profilini kontrol edin. iss_location_pool burada ve iss_location_data verimiz bu havuza yazıldı. Eğer bu havuzun önbelleğini temizlemek gerekirse, terminalde şunu çalıştırın:

```
php bin/console cache:pool:clear iss_location_pool
```

👉 Bu komut, sadece bu havuzun önbelleğini temizler, diğer havuzlara dokunmaz.

Ayrıca, bu havuzu diğerlerinden farklı şekilde yapılandırabiliriz. Örneğin, yeni havuzumuz için config dosyasında varsayılan sona erme süresi ayarlayalım. cache.yaml dosyasında null yerine yeni bir satırda default_lifetime: 5 yazın.

```
framework:
    cache:
        // ... lines 3 - 19
        pools:
            iss_location_pool:
                default_lifetime: 5
```

👉 Buradaki 5 değeri saniye cinsindendir. Bu havuzdaki tüm önbellek öğelerini etkiler.

Şimdi MainController.php dosyasında, \$item->expiresAfter() kodunu kaldırabiliriz. \$item argümanına da artık gerek yok.

```
class MainController extends AbstractController
{
    public function homepage(
    ): Response {
        $issData = $issLocationPool->get('iss_location_data', function () use ($client): array {
            $response = $client->request('GET', 'https://api.wheretheiss.at/v1/satellites/25544');
            return $response->toArray();
        });
    }
}
```

👉 Bu şekilde, havuzumuzun yapılandırması üzerinden tüm önbellek süresi yönetilmiş olur.

Çalıştığını görmek için ana sayfayı tekrar yenileyin ve... hata yok. Çalışıyor!

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./5_Bundle Config Configuring the Cache Service.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./7_Symfony Environments.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
