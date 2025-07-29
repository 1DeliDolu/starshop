# ⚙️ Bundle Config: Configuring the Cache Service / Bundle Yapılandırması: Önbellek Servisinin Yapılandırılması

Şimdiye kadar, HTTP İstemcisi ve önbellek servislerini nasıl kullanacağımızı öğrendik ve bunu homepage() metoduna enjekte ettik. Ancak, bu nesnelerin oluşturulmasından biz sorumlu değiliz. Zaten biliyoruz ki, bundle’lar bize servisler sağlar ve bir servisi autowire ettiğimizde, bundle’ımız onu örneklemek için ihtiyaç duyduğu tüm ayrıntıları sağlar. Ancak, bu nesneleri başlatmaktan başka bir şey sorumluysa, onu nasıl kontrol edebiliriz? Cevap: bundle yapılandırmasıdır.

## Bundle Configuration / Bundle Yapılandırması

/config/packages dizinini açın. Bu .yaml yapılandırma dosyalarının tümü Symfony uygulamamızda otomatik olarak yüklenir ve her birinin görevi, her bundle’ın bize verdiği servisleri yapılandırmaktır. homepage() metodumuzun hemen başında dd(\$cache) ekleyelim, böylece aldığımız nesnenin sınıf adını görebiliriz.

```
src/Controller/MainController.php
// ... lines 1 - 13
class MainController extends AbstractController
{
// ... line 16
    public function homepage(
// ... lines 18 - 20
    ): Response {
        dd($cache);
// ... lines 23 - 38
    }
}
```

👉 Bu kod, cache nesnesinin hangi sınıftan geldiğini görmek için dd(\$cache) fonksiyonunu kullanır.

Örneğin, önbellek servisi için FrameworkBundle servis konteynerine şunu söyler:

Hey! CacheInterface istediğimde, bana belirli argümanlarla birlikte bu TraceableAdapter nesnesini oluşturmanı istiyorum.

Yani önbellek servisimiz yalnızca bu TraceableAdapter gibi görünüyor, ancak daha yakından bakarsak aslında FilesystemAdapter üzerinde bir sarmalayıcı olduğunu ve önbelleğin dosya sisteminde saklandığını görebiliriz. Bu güzel, ama önbelleği bunun yerine bellekte tutmak istersek ne olur? Ya da dosya sisteminde başka bir yerde saklamak istersek? İşte bundle yapılandırması burada devreye girer. framework.yaml dosyasını açın ve framework anahtarını bulun. Bu, FrameworkBundle’a yapılandırma gönderdiğimiz anlamına gelir ve bundle bu yapılandırmayı kullanarak servislerini nasıl oluşturacağını değiştirir. Bu arada, buradaki dosya adı önemli değildir. Bunu pizza.yaml olarak da adlandırabiliriz ve yine çalışır.

```
config/packages/framework.yaml
// ... line 1
framework:
    secret: '%env(APP_SECRET)%'
// ... lines 4 - 24
```

👉 Bu YAML dosyası, framework bundle’ı için yapılandırma ayarlarını içerir.

Şimdi terminalinize geçin ve şu komutu çalıştırın:

## Debugging Configuration 

```
php bin/console debug:config framework
```

👉 Bu komut, framework bundle’ının mevcut yapılandırmasını gösterir.

Tüm yapılandırmayı görmek için şunu çalıştırın:

```
php bin/console config:dump framework
```

👉 Bu komut, framework bundle’ının tam yapılandırmasını listeler.

Bu çok fazla bilgi verecektir. Bunu biraz daraltalım. Yalnızca önbellek servisine ait yapılandırmayı görmek istiyorsak şu komutu çalıştırın:

```
php bin/console config:dump framework cache
```

👉 Bu komut, framework bundle’ındaki sadece cache yapılandırmasını gösterir.

## Cache Array Adapter

Çok daha iyi. cache.yaml dosyasında, bunun hâlâ framework yapılandırmasının bir parçası olduğunu görebiliriz — sadece farklı dosyalarda düzenlenmiştir. Bu örneğin altına app anahtarını cache.adapter.array olarak ayarlayalım.

```
config/packages/cache.yaml
framework:
    cache:
// ... lines 3 - 16
        app: cache.adapter.array
// ... lines 18 - 22
```

👉 Bu yapılandırma, uygulama önbelleğini bellekte (array adapter) tutacak şekilde ayarlar.

Şimdi tekrar tarayıcıya dönün ve sayfayı yenileyin. Harika! Bu, ArrayAdapter olarak değişti. Şimdi dd(\$cache) satırını kaldırın, böylece cache.adapter.array’i gerçekten görebilelim. Sayfayı tekrar yenileyin ve… ah! Her sayfa yenilemesinde HTTP isteğini yeniden çalıştırıyoruz, yani önbellek yalnızca istek süresince canlı kalıyor. Yeni bir istek başlattığımızda önbellek geçersiz oluyor ve HTTP isteğini tekrar görüyoruz.

Sonraki: Autowiring konusuna daha yakından bakacağız.
