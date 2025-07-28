# 3_Rotalar, Controllerlar & Yanıtlar

Tamam, işin aslı şu: Wesley Crusher - Star Trek’in en sevilen ensign’ı - Starfleet’ten emekli oldu ve bizimle yeni bir iş kuruyor: Wesley’s Star Shop. Birinin galaksideki Ferengi tekelini kırması gerekiyor ve siteyi inşa etmemiz için bizi tuttu. Ferengi’ye latinumlarını kaptırmayacağız!

## Controller Oluşturmak

Ve ilk sayfamızı oluşturmakla başlıyoruz. Her sayfanın mantığı aynı: 1. Adım, ona havalı bir URL ver. Buna route (yol) denir. 2. Adım, sayfayı üreten bir PHP fonksiyonu yaz. Buna controller (denetleyici) denir. Ve bu sayfa HTML, JSON, ASCII sanatı, ne istersen olabilir.

Symfony’de controller her zaman bir PHP sınıfı içindeki bir metottur. Yani ilk PHP kodumuzu oluşturmalıyız! PHP kodu uygulamamızda nerede yaşar? Evet, src/ dizininde.

src/Controller/ dizini içinde yeni bir dosya oluştur. Normalde yeni "PHP class" seçerdim ama bu sefer boş bir dosya oluştur, her şeyi elle yapalım. Adı MainController.php olsun, ama istediğin gibi adlandırabilirsin.

İçine PHP açılış etiketini ekle, sonra class MainController yaz. Üstüne namespace olarak App\Controller ekle.

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('namespace App\\Controller;\nclass MainController\n{\n}')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-php">namespace App\Controller;
class MainController
{
}</code></pre>
  </div>
</div>

### Namespace & Dizinler

Bununla ilgili birkaç şey: Sınıfı Controller adlı bir dizine koymam tamamen isteğe bağlı. Bu sadece bir gelenek. Bunu Klingonca Controller kelimesiyle değiştirsen bile her şey aynı olurdu... ve muhtemelen daha ilginç olurdu!

Ama PHP sınıflarıyla ilgili birkaç kural var. Birincisi, her sınıfın bir namespace’i olmalı ve bu namespace dizin yapısıyla eşleşmeli. Her zaman App\ ve içinde bulunduğun dizin. Detaya girmeden, bu kuralı her PHP projesinde bulacaksın.

İkinci kural, sınıf adın dosya adınla aynı olmalı. Bunlardan birini yanlış yaparsan, PHP sınıfı bulamadığına dair hata verir. Ferengi asla bu hatayı yapmaz.

## Controller Metodu & Route Oluşturmak

Hedefimiz bir controller oluşturmak, yani sayfayı üreten bir sınıf metodu. Yeni bir public fonksiyon ekle ve adını homepage yap. Yine, isim önemli değil. Ve... işte! Henüz bitmedi ama bu bizim controller’ımız!

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('class MainController\n{\n    public function homepage()\n    {\n    }\n}')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-php">class MainController
{
    public function homepage()
    {
    }
}</code></pre>
  </div>
</div>

Ama unutma, bir sayfa controller ve route’un birleşimidir, route sayfanın URL’sini tanımlar. Route’u nereye koyarız? Controller metodunun üstüne, PHP’nin attribute özelliğiyle. #[] yaz, Route ile başla. Otomatik tamamlama harika!

İki seçenek de çalışır ama Attribute olanı kullan, bu daha yeni. Tab’a basınca editör dosyanın üstüne use satırı ekler. Her attribute kullandığında, aynı dosyada ilgili use satırı olmalı.

Attribute’lar neredeyse PHP fonksiyonları gibi çalışır: bir sürü argüman verebilirsin. İlk argüman path. Bunu / olarak ayarla.

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('use Symfony\\Component\\Routing\\Attribute\\Route;\nclass MainController\n{\n    #[Route(\'/\')]\n    public function homepage()\n    {\n    }\n}')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-php">use Symfony\Component\Routing\Attribute\Route;
class MainController
{
    #[Route('/')]
    public function homepage()
    {
    }
}</code></pre>
  </div>
</div>

Bunun sayesinde, biri ana sayfaya (/) gittiğinde Symfony bu controller metodunu çağıracak!

## Controllerlar & Yanıtlar

Metodumuz ne döndürmeli? Sadece istediğimiz HTML, değil mi? Ya da API yapıyorsak JSON?

Neredeyse. Web iyi bilinen bir sistemle çalışır. Önce bir kullanıcı bir sayfa ister:

Hey, /products’u görmek istiyorum... ya da /users.json’u görmek istiyorum.

Geri döndürdüğümüz şey evet, HTML veya JSON içerir. Ama bundan fazlası var. Ayrıca bir status code (durum kodu) döndürürüz - yanıtın başarılı mı hatalı mı olduğunu belirtir - ve header’lar (başlıklar) ile ekstra bilgi iletiriz, örneğin dönen verinin formatı gibi.

Bu bütün pakete response (yanıt) denir. Yani çoğu zaman HTML veya JSON döndürmeyi düşünürüz. Ama aslında gönderdiğimiz şey daha büyük, daha nerd bir şey: response.

Yani web geliştiricisi olarak işimiz - hangi dili kullanırsak kullanalım - kullanıcının isteğini anlamak, sonra yanıtı oluşturup döndürmek.

Ve bu bizi Symfony’de sevdiğim bir şeye getiriyor. Controller’ımız ne döndürür? Symfony’den yeni bir Response nesnesi! Yine, PhpStorm otomatik tamamlama ile birkaç Response sınıfı önerir. Biz Symfony HttpFoundation bileşeninden olanı istiyoruz. Bu, istek ve yanıtla ilgili her şeyi içeren Symfony kütüphanesi.

Tab’a basınca editör dosyanın üstüne use satırı ekler. Bu numarayı hep kullanacağım. Her class adı referansında, ilgili use satırı olmalı, yoksa PHP Response sınıfını bulamaz.

İçine ilk argüman olarak döndürmek istediğimiz içeriği veriyoruz. Sabit bir string ile başla.

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('use Symfony\\Component\\HttpFoundation\\Response;\nclass MainController\n{\n    #[Route(\'/\')]\n    public function homepage()\n    {\n        return new Response(\'<strong>Starshop</strong>: your monopoly-busting option for Starship parts!\');\n    }\n}')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-php">use Symfony\Component\HttpFoundation\Response;
class MainController
{
    #[Route('/')]
    public function homepage()
    {
        return new Response('<strong>Starshop</strong>: your monopoly-busting option for Starship parts!');
    }
}</code></pre>
  </div>
</div>

Route, tamam! Response döndüren controller, tamam! Deneyelim. Tarayıcıda bu sayfa sadece demo olarak görünüyordu, gerçek ana sayfamız yoktu. Şimdi yenileyince... işte orada!

Henüz çok bir şey yok ama Symfony’nin ilk temel parçasını öğrendik: her sayfa bir route & controller’dır... ve her controller bir response döndürür.

Ve bu isteğe bağlı, ama controller’ımız her zaman Response döndürüyorsa, Response return type ekleyebiliriz. Bu kodun çalışma şeklini değiştirmez ama okunmasını daha açıklayıcı yapar. Ve bir gün yanlışlıkla response dışında bir şey döndürürsek, PHP bize net bir uyarı verir.

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('class MainController\n{\n    #[Route(\'/\')]\n    public function homepage(): Response\n    {\n        return new Response(\'<strong>Starshop</strong>: your monopoly-busting option for Starship parts!\');\n    }\n}')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-php">class MainController
{
    #[Route('/')]
    public function homepage(): Response
    {
        return new Response('<strong>Starshop</strong>: your monopoly-busting option for Starship parts!');
    }
}</code></pre>
  </div>
</div>

Sırada: Geliştirmeyi hızlandırmak için ilk üçüncü parti paketi kurup Symfony’nin harika recipe sistemini öğrenelim.


If you already have PHP and Composer installed, you may install the Laravel installer via Composer:

composer global require laravel/installer

For a fully-featured, graphical PHP installation and management experience, check out Laravel Herd.
