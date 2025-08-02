#  ⚡ Modernizing with fetch() and await / fetch() ve await ile Modernleştirme

Bu bölüm, `Symfony` yükseltmesiyle ilgili değildir. Ancak geri kalan kodlarımız - JavaScript dahil - modernize edilmeyi hak ediyor!

## 🔄 axios Yerine fetch() Kullanmak / axios Yerine fetch() Kullanmak

`song-controls_controller.js` içinde, başlangıçta Ajax çağrıları yapmak için `axios` kullanılmıştı.



```javascript
// assets/controllers/song-controls_controller.js 
// ... lines 1 - 11
import axios from 'axios';
// ... line 13
export default class extends Controller {
// ... lines 15 - 18
    play(event) {
// ... lines 20 - 21
        axios.get(this.infoUrlValue)
// ... lines 23 - 26
    }
}
```

👉 Bu örnekte, `axios` ile Ajax isteği yapılmaktadır.

Artık bunu kullanmıyoruz. Bunun yerine, yerleşik `fetch()` fonksiyonunu kullanıyoruz.

`axios`'u kaldırmak için şu komutu çalıştırın:



```bash
php bin/console importmap:remove axios
```

👉 Bu komut, `axios` paketini projeden kaldırır.

Ardından, import satırını ve varsa ilgili yorumu silin. `axios.get()` ifadesini sadece `fetch()` ile değiştirin. Çalışıp çalışmadığını görmek için `console.log(response)` ekleyin.



```javascript
// assets/controllers/song-controls_controller.js
// ... lines 1 - 2
export default class extends Controller {
// ... lines 4 - 7
    play(event) {
// ... lines 9 - 10
        fetch(this.infoUrlValue)
            .then((response) => {
                console.log(response);
                const audio = new Audio(response.data.url);
                audio.play();
            });
    }
}
```

👉 Burada, `fetch` fonksiyonu ile veri çekiliyor ve yanıt konsola yazdırılıyor.

Tarayıcıda play tuşuna bastığınızda method tetiklenir. Son iki satır çalışmaz, ama yanıtı görebilirsiniz! Ajax çağrısı yapılmıştır.

İlk yazarken, Promise'ı yönetmek için `.then()` kullandım. Ancak artık asenkron kodda genellikle `.then()` yerine `await` kullanıyorum.

## ⏳ await & async Kullanmak / await & async Kullanmak

`fetch` önüne `const response = await fetch()` yazın. Callback'in içeriğini hemen sonrasına ekleyin.



```javascript
// assets/controllers/song-controls_controller.js
// ... lines 1 - 2
export default class extends Controller {
// ... lines 4 - 7
    async play(event) {
// ... lines 9 - 10
        const response = await fetch(this.infoUrlValue);
        console.log(response);
        //const audio = new Audio(response.data.url);
        //audio.play();
    }
}
```

👉 Bu kodda, fetch çağrısı yapılır ve sonucunun dönmesi beklenir.

Fakat muhtemelen editörünüz kızacaktır:
`await` operatörü sadece async fonksiyonlarda kullanılabilir.

Yani, `await` kullanabilmek için doğrudan içinde bulunduğumuz fonksiyona `async` eklememiz gerekir. Detayına girmeyeceğim, ama bu, fonksiyonun artık asenkron olduğunu belirtir. Çağırıp dönüş değerini almak isterseniz, o çağrıyı da `await` ile yapmanız gerekir.

Bizim durumumuzda ise, bu methodu Stimulus çağırıyor ve dönüş değerini umursamıyor. Dolayısıyla `async` eklemek hiçbir şeyi değiştirmez.

Denediğimizde... aynı sonucu, callback olmadan elde ederiz.

Şimdi bitirelim: `const data = await response.json();`

Bu, API endpoint'imizin yanıtındaki JSON'u bir objeye çevirir. Bu da asenkron bir fonksiyon olduğu için yine `await` ile kullanılır. Sonrasında, `data.url`'u Audio'ya aktarın.

 

```javascript
// assets/controllers/song-controls_controller.js
// ... lines 1 - 2
export default class extends Controller {
// ... lines 4 - 7
    async play(event) {
        event.preventDefault();
        const response = await fetch(this.infoUrlValue);
        const data = await response.json();
        const audio = new Audio(data.url);
        audio.play();
    }
}
```

👉 Bu kod, yanıtı JSON olarak çözümler ve gelen URL ile ses çalar.

Şimdi kutlayın, çünkü artık modern, sade ve derleyicisiz bir kodunuz var!

Artık yükselttiğimize göre, sırada yeni favori özelliklerden bazılarına göz atmak var. İlk olarak, `autowiring` nimetleriyle başlıyoruz; belki de bir daha asla `services.yaml` dosyasını düzenlemek zorunda kalmayacaksınız.
