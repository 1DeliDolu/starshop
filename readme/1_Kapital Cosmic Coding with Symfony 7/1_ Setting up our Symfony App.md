# 🚀 Setting up our Symfony App

Symfony 7 eğitimine hoş geldiniz! Benim adım Ryan - Symfonycasts'in fantastik dünyasında yaşıyorum ve... bu seride size rehberlik edeceğim için fazlasıyla heyecanlıyım. Symfony, web geliştirme... kötü şakalar... uzay animasyonları ve en önemlisi, gurur duyabileceğimiz gerçek şeyler inşa etmek hakkında her şey burada olacak. Benim için, Enterprise'ın kişisel turunu size sunan şanslı kişi gibi hissediyorum... ya da sizi en çok heyecanlandıran başka bir nerd şeyi.ymfony Uygulamamızı Kurmak

Symfony 7 eğitimine hoş geldiniz! Benim adım Ryan - Symfonycasts’in fantastik dünyasında yaşıyorum ve... bu seride size rehberlik edeceğim için fazlasıyla heyecanlıyım. Symfony, web geliştirme... kötü şakalar... uzay animasyonları ve en önemlisi, gurur duyabileceğimiz gerçek şeyler inşa etmek hakkında her şey burada olacak. Benim için, Enterprise’ın kişisel turunu size sunan şanslı kişi gibi hissediyorum... ya da sizi en çok heyecanlandıran başka bir nerd şeyi.

Çünkü ben bu işi gerçekten seviyorum. Veritabanlarını başlatmak, güzel kullanıcı arayüzleri oluşturmak, yüksek kaliteli kod yazmak... bunlar sabah yataktan kalkmamı sağlıyor. Ve Symfony, tüm bunları yapmak ve daha iyi bir geliştirici olmak için en iyi araç.

Asıl amacım şu: Tüm bunlardan benim kadar keyif almanızı ve aklınızda dolaşan harika şeyleri inşa etme konusunda kendinizi güçlü hissetmenizi istiyorum.

## Symfony’yi Özel Kılan Nedir?

Symfony’yi öğretmeyi en çok sevdiğim şeylerden biri, projemizin çok küçük başlaması. Bu öğrenmeyi kolaylaştırıyor. Ama sonra, ihtiyacımız olan araçları eşsiz bir “recipe” sistemiyle otomatik olarak ekleyebiliyoruz. Symfony aslında 200’den fazla küçük PHP kütüphanesinden oluşan bir koleksiyon. Yani bir sürü araç var... ama hangisine ihtiyacımız varsa onu seçiyoruz.

Çünkü siz belki sadece bir API geliştiriyorsunuz... ya da bizim bu eğitimde odaklanacağımız gibi tam bir web uygulaması. Yine de bir API geliştiriyorsanız, bu serinin ilk birkaç dersini takip edin, sonra API Platform eğitimlerimize geçin. API Platform, Symfony üzerine inşa edilmiş, API’ler oluşturmak için inanılmaz eğlenceli ve güçlü bir sistem.

Symfony ayrıca son derece hızlı, uzun vadeli destek sürümleri var ve geliştirici deneyimini harika hale getirmek için çok çalışıyor, aynı zamanda en iyi programlama uygulamalarına sadık kalıyor. Bu da yüksek kaliteli kod yazmamızı ve işlerimizi hızlıca bitirmemizi sağlıyor.

Tamam, Symfony hakkında övgü dolu sözlerim bu kadar. Çalışmaya hazır mısınız? O zaman gemiye katılın.

## Symfony Binary’sini Kurmak

https://symfony.com/download adresine gidin. Bu sayfada, symfony adında bağımsız bir binary’i nasıl indireceğinizle ilgili talimatlar var. Bu Symfony’nin kendisi değil... sadece yeni Symfony projeleri başlatmak, yerel bir web sunucusu çalıştırmak veya uygulamamızı üretime dağıtmak gibi şeyleri yapmamıza yardımcı olan küçük bir araç.

Bunu indirip kurduktan sonra, bir terminal açın ve herhangi bir dizine geçin. Symfony binary’sinin hazır olup olmadığını kontrol etmek için şunu çalıştırın:

<!-- Terminal kutusu ve kopyala butonu örneği -->

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('symfony --help')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">symfony --help</code></pre>
  </div>
</div>

Bir sürü komut var, ama sadece birkaçına ihtiyacımız olacak. Projeye başlamadan önce, ayrıca şunu çalıştırın:

<!-- Kod kutusu: üstte üç daire ve sağ üstte kopyala simgesi -->

<div style="background:#222; border-radius:8px; margin-bottom:16px; position:relative;">
  <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div>
      <span >●</span>
      <span >●</span>
      <span >●</span>
    </div>
    <div>
      <span title="Kopyala" style="cursor:pointer; font-size:22px;" onclick="navigator.clipboard.writeText('symfony check:req')">🗒️</span>
    </div>
  </div>
  <pre style="color:#fff; margin:0; padding:16px; background:#222; border-radius:0 0 8px 8px;"><code>symfony check:req</code></pre>
</div>

Bu, gereksinimleri kontrol etmek anlamına geliyor. Symfony’yi çalıştırmak için sistemimizde gerekli olan her şeyin - doğru PHP sürümü ve bazı PHP eklentileri gibi - olup olmadığını kontrol ediyor.

Her şey yolundaysa, yeni bir proje başlatabiliriz! Bunu symfony new ve ardından bir dizin adı ile yapın. Benimkine starshop diyeceğim. Daha sonra bundan bahsedeceğim.

<!-- Terminal kutusu ve kopyala butonu örneği -->

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('symfony new starshop')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">symfony new starshop</code></pre>
  </div>
</div>

Bu, bize sadece temel şeylerin kurulu olduğu küçük bir proje verecek. Sonra, ihtiyacımız oldukça yavaş yavaş daha fazla şey ekleyeceğiz. Harika olacak! Ama ileride Symfony’ye alıştığınızda, daha hızlı başlamak isterseniz, aynı komutu <b style="color:red">--webapp</b> ile çalıştırabilirsiniz, böylece önceden daha fazla şey kurulu olur.

Her neyse, dizine geçin -<b style="color:red"> cd starshop </b>- sonra ls yazarak kontrol edin. Harika! Bu dosyaları bir sonraki bölümde tanıyacağız, ama işte projemiz... ve şimdiden çalışıyor!

## Symfony Web Sunucusunu Başlatmak

Bunu bir tarayıcıda çalışır halde görmek için bir web sunucusu başlatmamız gerekiyor. İstediğiniz herhangi bir web sunucusunu kullanabilirsiniz - Apache, Nginx, Caddy, ne isterseniz. Ama yerel geliştirme için, yeni kurduğumuz symfony binary’sini kullanmanızı şiddetle tavsiye ediyorum. Şunu çalıştırın:

<!-- Terminal kutusu ve kopyala butonu örneği -->

<div class="terminal-wrapper bg-black-4 rounded-t" style="background:#222; border-radius:8px; margin-bottom:16px;">
  <div class="flex justify-between py-1 px-3" style="display:flex; justify-content:space-between; align-items:center; padding:8px 16px;">
    <div class="terminal-controls">
      <span>●</span>
      <span>●</span>
      <span>●</span>
    </div>
    <div class="terminal-copy js-activate-clipboard">
      <span class="fa fa-clipboard text-white" title="Copy Code" style="cursor:pointer; font-size:18px;" onclick="navigator.clipboard.writeText('symfony serve')">📋</span>
      <span class="js-copy-button-text"></span>
    </div>
  </div>
  <div class="js-copy-clipboard-target">
    <pre class="notranslate" style="color:#fff; margin:0; padding:16px;"><code class="language-bash">symfony serve</code></pre>
  </div>
</div>

İlk kez çalıştırdığınızda, SSL sertifikası kurmak için başka bir komut çalıştırmanızı isteyebilir, bu da sunucunun https desteği olmasını sağlar.

Ve... işte! Projemiz için yeni bir web sunucusu https://127.0.0.1:8000 adresinde çalışıyor. Bunu kopyalayın, favori tarayıcınıza yapıştırın ve... Symfony 7’ye hoş geldiniz! Benim de söylemek istediğim buydu!

Sonraki adımda, bir fincan Earl Grey çayı sipariş edip, uygulamamızdaki her dosyayla arkadaş olacağız... ki aslında çok fazla dosya yok.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="#file:README.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
