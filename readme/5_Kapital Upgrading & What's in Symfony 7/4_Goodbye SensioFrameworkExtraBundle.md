#  👋 Goodbye SensioFrameworkExtraBundle / SensioFrameworkExtraBundle’a Elveda

Uygulamamız bozuldu: sorun `SensioFrameworkExtraBundle` ile ilgili. Tarifleri yükseltirken bu oldu. `framework.yaml` dosyasında, bu `annotations: false` satırında.

```php
config/packages/framework.yaml
// ... line 1
framework:
// ... lines 3 - 4
    annotations: false
// ... lines 6 - 33
```

👉 Bu yapılandırma, `annotations` özelliğini devre dışı bırakır.

`SensioFrameworkExtraBundle` bize `@Route` annotation’ı, güvenlik annotation’ı ve param converter gibi çeşitli özellikler sunuyordu. Bunların hepsi annotation sistemine dayanıyordu; bu sistem artık çekirdek PHP attribute’larıyla değiştirildi. Bunları `false` yaptığımızda... bundle bunu sevmedi.

Ama sorun değil! Tüm o güzel özellikler artık Symfony’nin çekirdeğinde yeni bir yuva buldu. Artık SensioFrameworkExtraBundle’a veda etme zamanı.

## 🗑 Uninstalling it / Kaldırılması

Terminalde şunu çalıştırın:

```shell
composer remove sensio/framework-extra-bundle
```

👉 Bu komut, `sensio/framework-extra-bundle` paketini projeden kaldırır.

Elveda ve tüm annotated balıklar için teşekkürler. İşlem bittiğinde... ve sayfayı yenilediğimizde, site tekrar çalışıyor!

## 🔍 Checking for SensioFrameworkExtraBundle Features / SensioFrameworkExtraBundle Özelliklerini Kontrol Etmek

Ama... herhangi bir özelliğini kullanıyor muyduk? Bilmiyorum! Kolayca kontrol etmenin yolu:

```shell
git grep FrameworkExtra
```

👉 Bu komut, kod tabanında `FrameworkExtra` ifadesini arar.

Hayır! Görünüşe göre doğrudan herhangi bir `use` satırı yok. Eğer varsa, bu özelliğin Symfony’deki yeni attribute karşılığını bulup güncellemek yeterli.

Buna yardımcı olmak için Symfony’nin mükemmel bir dokümantasyon sayfası var: `Symfony Attributes Overview`. Burada Symfony’deki tüm PHP attribute’ları gösteriliyor. Örneğin, `SensioFrameworkExtraBundle`’da bir `Security` annotation’ı vardı. Artık Symfony’de bunun yerine kullanabileceğiniz bir `IsGranted` attribute’u var.

Yani eski sistemden bir şey kullanıyorsanız, yeni karşılığını bulup güncelleyin.

## 🔄 The New "Param Converter" / Yeni "Param Converter"

Ancak... `SensioFrameworkExtraBundle`’ın annotation gerektirmeyen bir özelliği daha vardı... yani farkında olmadan da kullanıyor olabilirsiniz. Karışımlardan (mix) birine tıklayın. URL’de bir slug var. Bunun denetleyicisi `src/Controller/MixController.php`. Burada, route bir `{slug}` wildcard’ı içeriyor... ama parametre olarak bir `$mix` var ve bu bir Doctrine entity’si.

```php
src/Controller/MixController.php
// ... lines 1 - 12
class MixController extends AbstractController
{
// ... lines 15 - 35
    #[Route('/mix/{slug}', name: 'app_mix_show')]
    public function show(VinylMix $mix): Response
    {
// ... lines 39 - 41
    }
// ... lines 43 - 60
}
```

👉 Bu metotta, route’daki `{slug}` parametresi otomatik olarak `VinylMix` entity’sine çevrilir.

Arka planda, param converter otomatik olarak slug değeri URL’deki `{slug}` ile eşleşen bir `VinylMix` arardı. Herhangi bir annotation gerekmeden: sadece çalışıyordu.

İyi haber şu ki, gördüğünüz gibi, bu sihir hâlâ çalışıyor! Bu özellik artık çekirdekte yer alıyor. Ve çoğu durumda, eskisi gibi sessizce işini yapmaya devam edecek.

Slug’ın sonuna fazladan bir harf eklerseniz ve 404 alırsanız, bu sistemin arkasında `EntityValueResolver` olduğunu görürsünüz. Eğer daha fazla kontrole ihtiyacınız olursa, bunu `#[MapEntity]` attribute’u ile yapılandırabilirsiniz.

Sıradaki adım: Symfony 7’ye yükseltmek istiyorum! Ama bunu yapabilmek için tüm bu uyarılardan kurtulmamız gerekiyor.
