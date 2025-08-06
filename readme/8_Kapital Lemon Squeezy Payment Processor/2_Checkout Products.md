# 🛒 Checkout Products / Ürün Satın Alma

Önceki bölümde yeni bir ürün oluşturduk ve onu mağaza sayfamızda gösterdik. Artık müşterilerimiz bir web sitemiz olmasa bile ürünü görebiliyor ve doğrudan satın alabiliyor. Harika! 😎

Mağaza sayfamızı sol kenar çubuğundan açabiliriz. Mağaza adımızın — `"Squeeze the Day"` — altında bulunan `"My Store"` bağlantısına tıklayın. İşte burada:

[https://squeeze-the-day.lemonsqueezy.com/](https://squeeze-the-day.lemonsqueezy.com/)

Mağazamızın adı bir `alt alan adı` (subdomain) olarak görünüyor, ancak henüz etkinleştirilmemiş. LemonSqueezy mağazayı kullanabilmemiz için önce etkinleştirmemizi istiyor. Bunu yapmak için `Setup` sayfasında birkaç adımı daha tamamlamamız gerekiyor. Ancak bu kursun asıl odak noktası `API entegrasyonu` olduğu için mağazayı henüz etkinleştirmeyeceğiz.

"Eee, önceki bölümde test modundayken mağaza sayfasını görebiliyorduk?" dediyseniz, haklısınız! Görünüşe göre LemonSqueezy yakın zamanda bazı değişiklikler yaptı, bu yüzden kurulum adımlarını tamamlamadan da mağazaya tekrar erişim sağlanabilecek. Şimdilik ürün listesine gidip, ürün satırındaki üç noktaya tıklayıp `"Preview"` seçeneğini kullanarak LemonSqueezy ödeme sayfasını görebiliriz. İşte bu kadar! Ödeme sayfasındayız ve ürünü satın almaya hazırız! 🎉

Her ürünün ayrıca `"Share"` butonu vardır. Bu butonla ürünün ödeme sayfasına özel bir bağlantı oluşturabilir ve müşterilerinizle paylaşabilirsiniz. Böylece doğrudan LemonSqueezy ödeme sayfasına yönlendirilirler. Burada `quantity` (miktar) gibi bazı parametreleri de yapılandırabilirsiniz. Benzersiz bağlantıyı oluşturduğunuzda, bağlantıyı kopyalayıp paylaşabilir veya yeni bir sekmede açabilirsiniz. Aşağı kaydırırsanız, "Quantity" değerinin şimdi `"5"` olarak ayarlandığını göreceksiniz. LemonSqueezy mağazası, kendi web siteniz kadar esnek olmasa da, düğme renkleri, arka plan gibi bazı stil seçeneklerini özelleştirebilirsiniz.

Haydi ona da bir göz atalım. Pano’ya dönün, `"Share Product"` modülünü kapatın ve sol menüde `"Design"` bölümünü açın. Burada birkaç sekme bulacaksınız. `"General"` sekmesinde bir logo veya favicon yükleyebilirsiniz. Küçük ama etkili! 💪 Ayrıca önceden tanımlı bir tema seçebilir veya kendi renklerinizi belirleyebilirsiniz. Hayal gücünüzü serbest bırakın!

Daha fazla esneklik `"Store"` sekmesinde! Bu, müşterilerinizin mağazanızı nasıl göreceğini belirler ve burada oluşturduğumuz yeni ürünü de görebilirsiniz. Mağazanızı farklılaştırmak için burası harika bir yerdir.

`"Checkout"` sekmesi ise mağazanızın ödeme sayfasını farklı stillerle önizlemenizi sağlar ve gerekirse bazı stilleri geçersiz kılmanıza da olanak tanır.

Tamam, şimdi ödeme sayfamıza geri dönelim ve gerçekten e-limonatamızı satın almayı deneyelim. Burada iki seçeneğimiz var: `"Pay by Card"` (Kartla Öde) veya `"Pay with PayPal"` (PayPal ile Öde). `"Pay by Card"` seçeneğini seçelim. E-posta alanı, LemonSqueezy panosunda oturum açtığımız için önceden doldurulmuş durumda. Kart numarası için LemonSqueezy'nin `test kartları`nı kullanabiliriz, bu kartlara belgeler kısmından ulaşabilirsiniz. Sitesini yeni bir sekmede açıp "Resources", "Help Docs" ve sol kenar çubuğunda `"Test Mode"` sekmesine giderek sağ tarafta ihtiyacımız olan test kartlarını bulabiliriz.

Birden fazla seçenek mevcut ama ilkini kullanacağım. Bu numarayı kopyalayıp... yapıştırın... son kullanma tarihi olarak herhangi bir gelecek tarih kullanabilirsiniz. Ben "12/25" diyorum, güvenlik kodu için de rastgele üç hane yeterli. Ayrıca bir `fatura adresi` girmemiz gerekiyor. Eğer "Buy" butonuna adres girmeden tıklarsak, doğrulama hatası alırız. "Broadway 1" yazalım ve açılan öneri listesinden birini seçerek tüm alanları otomatik dolduralım. "Tax ID number" (Vergi Numarası) girmek isteğe bağlı, boş bırakabiliriz.

"Pay" butonuna tıklarsak... bum! `"Thanks for your order!"` (Siparişiniz için teşekkürler!) Eğer "View Order" butonuna tıklarsak... siparişimiz için bir `PDF fatura` oluşturabileceğimiz bir sayfaya yönlendiriliriz.

Bunu yapmak için LemonSqueezy bizden tekrar bir adres girmemizi istiyor — ya güvenlik sebebiyle ya da otomatik doldurma çalışmadığı için. Özel notlar da ekleyebilir ve faturanın oluşturulacağı dili seçebilirsiniz. Adres: "Broadway 1", şehir: "New York", eyalet: "New York" ve diğer alanları olduğu gibi bırakıyoruz.

"Generate Invoice" butonuna tıklarsak bir fatura dosyası indiriliyor. Açarsak... güzel! Bilgilerimiz orada!

## 📧 Invoice Emails / Fatura E-postaları

İlk ürününüzü satın aldıktan sonra, ödeme işlemi sırasında kullandığınız e-posta adresinin gelen kutusunu kontrol edin. Evet! Geldi bile! `"Your Classic E-Lemonade receipt"` başlıklı e-postayı açın. Siparişimizi görüntüleyebilir ve daha önce gördüğümüz faturayı tekrar oluşturabiliriz. Mağaza sahibi olarak, mağaza aynı e-posta adresine bağlı olduğu için fazladan bir e-posta daha alabilirsiniz. Gelen kutum spam dolmasın diye bu e-postaları ben kapattım, siz de kapatabilirsiniz: Mağaza adımız "Squeeze the Day", `"My Account"` ve `"Sales notifications"` anahtarını kapatın.

Değişiklikleri kaydetmeyi unutmayın! Ayrıca bu ayarı LemonSqueezy panosunda `"Design"`, `"Email"` sekmesinden de yapılandırabilirsiniz.

Ayrıca ödeme sırasında oluşturulan e-postayı da özelleştirebiliriz. Buna daha sonra değineceğiz!

## 📊 LemonSqueezy Dashboard / LemonSqueezy Panosu

Şimdiye kadar hep müşteri gözünden ilerledik, peki ya mağaza sahibi açısından? Pano’ya geri dönelim... işte burada! Satış grafiğimiz yaptığımız satın alımı zaten gösteriyor! `"Store"`... `"Orders"` sekmesini açarsanız, sipariş listesini görebilirsiniz. Sonuncusu, \$4.95 tutarındaki satın alım olmalı. Harika! Burada ayrıca `"Customers"` sayfası da var; müşteriler hakkında bazı bilgiler ve hangi ürünleri sipariş ettikleri görüntülenebilir. Ekranımda bazı siparişlerin `"Archived"` (Arşivlenmiş) olarak işaretlendiğini görebilirsiniz. Bu sadece müşterinin artık pazarlama e-postası almayacağı anlamına gelir. Bu... aslında bir kazaydı. Bir siparişi arşivlemeye çalışmıştım ve şimdi geri alamıyorum. Umarım LemonSqueezy bu özelliği daha sonra ekler. 😅

Her neyse, gördüğünüz gibi LemonSqueezy tüm işlem e-postalarını bizim yerimize gönderiyor, bu nedenle bu konuda endişelenmemize gerek yok. Ekşi... yani... tatlı! Bir web siteniz olmasa bile ürünlerinizi satmaya başlayabilirsiniz; sadece daha fazla ürün oluşturun, mağaza sayfanızda yayınlayın ve mağaza bağlantınızı arkadaşlarınızla paylaşın! 🎉

Ama biz geliştiriciyiz ve zaten çok popüler bir e-limonata tezgâhımız var (satış istatistikleri bunu açıkça gösteriyor), ayrıca çok yakında göreceğimiz harika bir web sitemiz de var. Şimdi LemonSqueezy'yi sitenize `API` ile doğrudan entegre etmek istiyoruz. Haydi bunu bir sonraki adımda yapalım!
