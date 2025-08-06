# 🎧 Listening to Webhooks / Webhook'ları Dinlemek

LemonSqueezy Müşterisini Bir Kullanıcıya Atama
Sitemizde kullanıcılar, LemonSqueezy tarafında ise müşteriler var… ama kod açısından, bunlar birbirini hiç tanımayan iki farklı dünya. Anlamlı bir entegrasyon kurmak – örneğin bir kullanıcının siparişlerini çekmek, sorunları ayıklamak veya müşteriler bize siparişleriyle ilgili ulaştıklarında daha iyi destek sağlamak – istiyorsak, bunları birbirine bağlamamız gerekiyor. Daha spesifik olarak, ilgili `User` varlığında LemonSqueezy müşteri kimliğini (`customer ID`) saklamak istiyoruz.

Ama burada bir sürpriz var: Birisi ödeme işlemini tamamladığında, LemonSqueezy arka planda bizim için bir “müşteri” oluşturuyor. Yani bir müşteri kimliğini önceden oluşturamazsınız veya ödeme sırasında mevcut bir kimlik gönderemezsiniz. LemonSqueezy, girilen e-posta adresine ve kullanıcının LemonSqueezy hesabında oturum açıp açmadığına göre bir müşteri atar.

## Webhook'lar

Peki, bu müşteri kimliğini nasıl alıp kullanıcıya bağlayacağız? Elle mi? Evet, yapabiliriz… ama kim ister ki? Bunun yerine, bunu webhook’lar ile otomatikleştirelim.

Checkout oluşturulurken bazı `metadata` (örneğin sistemimizdeki kullanıcı kimliği gibi) gönderebiliriz ve webhook’u (birazdan daha fazla bilgi vereceğim) aldığımızda, bu metadataları okuyup, doğru kullanıcıyı veritabanımızda bulup, müşteri kimliğini onunla saklayacağız.

Eğer “Bir dakika… Ödeme sonrası da müşteri kimliğini alamaz mıyız?” dediyseniz, doğru! LemonSqueezy, ödeme sonrası JavaScript’te de `checkout:complete` adlı bir olay yayınlar ve bu olay müşteri kimliğini içerir. Bu, webhook tünellerini yapılandırmak istemediğiniz yerel geliştirmede harika olabilir.

Kısacası… İkisini de yapalım!

## Symfony Webhook Bileşeni

Webhook’lar üretim ortamı için esnek ve sağlam bir çözüm olduğu için, önce onunla başlayalım. LemonSqueezy ana sayfasında “Resources” bölümüne gidin, “Help docs”u seçin ve “Webhooks” kısmında “Event types”a tıklayın.

Biraz kaydırdığınızda… siparişlerle ilgili birkaç etkinlik görebiliyorum. Bizim için, `order_created` etkinliği gerekli görünüyor – bu, aradığımız sipariş verisini sağlamalı. Tıklayın ve aşağı kaydırın… evet! Burada `customer_id` dönüyor! Harika!

Kendi başımıza bir `WebhookController` oluşturabilirdik ama Symfony bu işi bizim için kolaylaştırıyor ve yeni bir Webhook bileşeni çıkardı! Haydi kullanalım! Terminalde şunu çalıştırın:

---

```shell
composer require webhook
```

👉 Bu komut, gerekli webhook bağımlılıklarını kurar.

---

MakerBundle ayrıca başlamak için yardımcı bir komuta sahip. Şunu çalıştırın:

---

```shell
bin/console make:webhook
```

👉 Bu komut, webhook için temel yapıyı oluşturur.

---

Buna lemon-squeezy adını verelim. Eşleştirici olarak: PathRequestMatcher (seçenek 6), sonra MethodRequestMatcher (seçenek 5) ve LemonSqueezy bize verileri JSON formatında gönderdiği için ayrıca 4 – IsJsonRequestMatcher’ı seçin. Bir kez daha enter’a basın ve… harika! Bir parser ve bir consumer oluşturuldu – bunlar hakkında birazdan konuşacağız – ve ayrıca yeni bir uç nokta (endpoint) oluşturuldu. Şunu çalıştırarak görebilirsiniz:

---

```shell
bin/console debug:router | grep webhook
```

👉 Bu komut, uygulamanızdaki webhook rotalarını listeler.

---

... işte burada – yeni `/webhook/{type}` URL’imiz! `{type}` kısmı, az önce belirlediğimiz lemon-squeezy webhook ismi olmalı. Bu, LemonSqueezy webhook’larını yönetecek özel bir URL. Tarayıcıda açarsak… evet! Bir `RejectWebhookException` fırlatıyor:

> Request does not match.

Bunu yapılandıralım! Bu URL’yi kopyalayın ve LemonSqueezy kontrol panelinde, “Settings”, “Webhooks” bölümüne gidin, artı simgesine tıklayarak webhook’u düzenleyin. “Callback URL” alanına az önce kopyaladığınız URL’yi yapıştırın. Ama… burada bir problem var. Bu URL halka açık değil, dolayısıyla LemonSqueezy bizim localhost’umuzdaki adrese ulaşamaz.

Webhook’larımızı düzgünce ayarlamak için Ngrok gibi bir aracı kullanmamız gerekiyor, böylece LemonSqueezy bizim yerel makinemizle iletişim kurabilir. Sıradaki adım bu olacak.
