## 🛢️ Parameters / Parametreler

Daha önce, konteynerimizden ve içindeki tüm servis nesnelerinden bahsetmiştik. Bunları görmek için şu komutu çalıştırabiliriz:

```
php bin/console debug:container
```

👉 Bu komut, uygulamanızdaki mevcut servisleri listeler.

Ama bu servisler, konteynerimizdeki tek şey değil. Konteyner aynı zamanda parametreleri de tutar. Aynı komutu tekrar çalıştırın, fakat bu kez `--parameters` seçeneğini ekleyin:

```
php bin/console debug:container --parameters
```

👉 Bu komut, konteynerdeki tüm parametreleri listeler.

Bunlar, kodunuzda başvurabileceğiniz değişkenlerdir. Çoğu dahili amaçlar için kullanılır, ancak işinize yarayabilecek bazı temel parametreler de vardır. Örneğin, `kernel.` ile başlayanlar gibi, mesela `kernel.environment`, bu da `APP_ENV` ortam değişkenine ayarlanmıştır. Ya da projenizin kök dizin yolunu belirten `kernel.project_dir` gibi.

Symfony Parametreleri
Peki, bunu konteynerimizden nasıl alabiliriz? Aslında, kontrolcümüzde bunun için özel bir kısayol metodumuz var. `/src` dizininde, `Controller/MainController.php` dosyasını açın. `homepage()` metodunda, en başa şunu yazın: `dd($this->getParameter())`. Ve içine de parametre adını yazın: `'kernel.project_dir'`. Gördüğünüz gibi, PhpStorm (Symfony eklentisiyle birlikte), bunu zaten otomatik tamamladı. Güzel.

```
src/Controller/MainController.php
// ... lines 1 - 13
class MainController extends AbstractController
{
// ... line 16
    public function homepage(
// ... lines 18 - 20
    ): Response {
        dd($this->getParameter('kernel.project_dir'));
// ... lines 23 - 36
    }
}
```

👉 Bu kod, kontrolcünüzde `kernel.project_dir` parametresinin değerini ekrana basar.

Tarayıcıda sayfayı yenileyin ve... işte yolumuz burada! Çoğu zaman, parametreleri servislere enjekte etmemiz gerekir ve bunu özel bir sözdizimiyle yapabiliriz. Size göstereceğim! `config/packages/twig.yaml` dosyasını açın. Orada `twig.default_path` parametresinin `%kernel.project_dir%/templates` olarak ayarlandığını görebilirsiniz. `.yaml` dosyalarında bir parametreye başvurmak için bu `%[parametre adı]%` özel sözdizimi kullanılır.

```
config/packages/twig.yaml
twig:
    default_path: '%kernel.project_dir%/templates'
// ... lines 3 - 7
```

👉 Bu, `twig.default_path` parametresine, `kernel.project_dir` parametresinin değerini ekler.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./9_More about Services.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./11_Non-Autowireable Arguments.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
