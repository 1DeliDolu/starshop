## 👋 Goodbye SensioFrameworkExtraBundle / SensioFrameworkExtraBundle’a Elveda

Uygulamamız bozuldu: sorun `SensioFrameworkExtraBundle` ile ilgili. Tarifleri (recipes) yükseltirken bu oldu. `framework.yaml` dosyasında, bu `annotations: false` satırında:

```yaml
config/packages/framework.yaml
framework:
    annotations: false
    # ... diğer ayarlar ...
```

👉 Bu yapılandırma, annotation sistemini devre dışı bırakır. SensioFrameworkExtraBundle ise annotation'lara dayanırdı.

`SensioFrameworkExtraBundle` bize `@Route` annotation’ı, security annotation’ı ve param converter gibi özellikler sunuyordu. Bunların hepsi annotation sistemine bağlıydı; bu sistem artık çekirdek PHP attribute’larıyla değiştirildi. `annotations: false` yapınca bundle çalışmaz hale geldi.

Ama sorun değil! Tüm bu güzel özellikler artık Symfony’nin çekirdeğinde yeni bir yuva buldu. Artık SensioFrameworkExtraBundle’a veda etme zamanı.

---

## 🗑️ Uninstalling it / Kaldırılması

Terminalde şunu çalıştırın:

```shell
composer remove sensio/framework-extra-bundle
```

**Simüle Edilen Çıktı:**

```
Loading composer repositories with package information
Removing sensio/framework-extra-bundle (v6.2.1)
Updating dependencies
Lock file operations: 0 installs, 0 updates, 1 removal
  - Removing sensio/framework-extra-bundle (v6.2.1)
Writing lock file
```

Elveda ve tüm annotated balıklar için teşekkürler. İşlem bittiğinde... ve sayfayı yenilediğimizde, site tekrar çalışıyor!

---

## 🔍 Checking for SensioFrameworkExtraBundle Features / SensioFrameworkExtraBundle Özelliklerini Kontrol Etmek

Ama... herhangi bir özelliğini kullanıyor muyduk? Emin değiliz! Kolayca kontrol etmenin yolu:

```shell
git grep FrameworkExtra
```

**Simüle Edilen Çıktı:**

```
(no results)
```

Görünüşe göre doğrudan herhangi bir `use` satırı yok. Eğer varsa, bu özelliğin Symfony’deki yeni attribute karşılığını bulup güncellemek yeterli.

Symfony’nün mükemmel bir dokümantasyon sayfası var: **Symfony Attributes Overview**. Burada Symfony’deki tüm PHP attribute’ları gösteriliyor. Örneğin, SensioFrameworkExtraBundle’da bir `Security` annotation’ı vardı. Artık Symfony’de bunun yerine kullanabileceğiniz bir `IsGranted` attribute’u var.

Yani eski sistemden bir şey kullanıyorsanız, yeni karşılığını bulup güncelleyin.

---

## 🔄 The New "Param Converter" / Yeni "Param Converter"

Ancak... SensioFrameworkExtraBundle’ın annotation gerektirmeyen bir özelliği daha vardı... yani farkında olmadan da kullanıyor olabilirsiniz. Mesela bir entity'yi doğrudan route parametresiyle eşleştirmek.

Örnek:

```php
src/Controller/MixController.php
class MixController extends AbstractController
{
    #[Route('/mix/{slug}', name: 'app_mix_show')]
    public function show(VinylMix $mix): Response
    {
        // ...
    }
}
```

👉 Bu metotta, route’daki `{slug}` parametresi otomatik olarak `VinylMix` entity’sine çevrilir.

Arka planda, param converter otomatik olarak slug değeri URL’deki `{slug}` ile eşleşen bir `VinylMix` arardı. Herhangi bir annotation gerekmeden: sadece çalışıyordu.

**İyi haber:** Bu sihir hâlâ çalışıyor! Bu özellik artık Symfony çekirdeğinde. Ve çoğu durumda, eskisi gibi sessizce işini yapmaya devam edecek.

Eğer slug’ın sonuna fazladan bir harf eklerseniz ve 404 alırsanız, bu sistemin arkasında `EntityValueResolver` olduğunu görürsünüz. Eğer daha fazla kontrole ihtiyacınız olursa, bunu `#[MapEntity]` attribute’u ile yapılandırabilirsiniz.

---

## 📝 Alternatifler ve Dikkat Edilecekler

-   Eğer kodunuzda eski annotation’lar varsa, Symfony attribute’larına geçin.
-   Param converter davranışı Symfony 6+ ile çekirdekte, ekstra bir bundle gerekmez.
-   `annotations: false` ile annotation sistemi devre dışı, sadece attribute’lar çalışır.

---

## 🚦 Sonraki Adım

Sıradaki adım: Symfony 7’ye yükseltmek! Ama önce tüm deprecation uyarılarından kurtulmamız gerekiyor.
