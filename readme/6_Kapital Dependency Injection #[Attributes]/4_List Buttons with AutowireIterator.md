# 🔄 List Buttons with AutowireIterator / AutowireIterator ile Butonları Listele

Uygulamamızı her butonun çalıştırılması için Komut tasarım desenini kullanacak şekilde yeniden düzenledik. Harika! Şimdi yeni hedef: butonları daha dinamik hale getirmek. Yani yeni bir buton sınıfı eklediğimizde şablonu düzenlememiz gerekmesin.

## 📝 ButtonRemote İçinde Buton İsimlerini Listeleme

Öncelikle, `ButtonRemote` içinde tüm buton isimlerinin (container'daki index'lerin) listesini almanın bir yoluna ihtiyacımız var. Bunun için burada `buttons()` adında, dizi döndüren bir public metot oluşturuyoruz. Bu metot bir dizi string döndürecek: buton isimlerimiz!


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 7
final class ButtonRemote
{
// ... lines 10 - 20
    /**
     * @return string[]
     */
    public function buttons(): iterable
    {
// ... lines 26 - 28
    }
}
```

👉 Bu metot, tüm buton isimlerini (slug) içeren bir dizi döndürmek için oluşturuldu.

## 🔁 #\[AutowireIterator] Kullanımı

Mini-container, tek tek servisleri almak için harikadır. Fakat içindeki tüm buton servislerinde döngü kuramazsınız. Bunu düzeltmek için `#[AutowireLocator]`'ı `#[AutowireIterator]` olarak değiştirin. Bu, Symfony'ye servislerin bir iterablesini enjekte etmesini söyler. Yani artık bir `ContainerInterface` değil, `iterable` kullanacağız ve `$container` ismini `$buttons` ile değiştireceğiz.


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 7
final class ButtonRemote
{
    public function __construct(
        #[AutowireIterator(ButtonInterface::class)]
        private iterable $buttons,
    ) {
    }
// ... line 15
    public function press(string $name): void
    {
        $this->buttons->get($name)->press();
    }
// ... lines 20 - 29
}
```

👉 Bu değişiklik, servisleri iterable olarak almanızı sağlar.

Şimdi aşağıda butonlar üzerinde döngü kuruyoruz: `foreach ($this->buttons as $name => $button)`. `$button` gerçek servis olsa da, biz sadece `$name`'i alıp `$buttons` dizisine ekleyeceğiz. En sonda, `$buttons`'ı döndürüyoruz.


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 23
    public function buttons(): iterable
    {
        $buttons = [];
        foreach ($this->buttons as $name => $button) {
            $buttons[] = $name;
        }
        return $buttons;
    }
// ... lines 34 - 35
```

👉 Bu döngü, tüm buton isimlerini bir dizi olarak toplar.

## 📨 Butonları Şablona Aktarma

Denetleyicide, zaten `ButtonRemote` enjekte ediyoruz. Şablonu render ettiğimiz yerde, yeni bir `buttons` değişkeni ile `'buttons' => $remote->buttons()` şeklinde aktarın.


```php
// src/Controller/RemoteController.php
// ... lines 1 - 12
final class RemoteController extends AbstractController
{
// ... line 15
    public function index(Request $request, ButtonRemote $remote): Response
    {
// ... lines 18 - 29
        return $this->render('index.html.twig', [
            'buttons' => $remote->buttons(),
        ]);
    }
}
```

👉 `buttons` değişkeni, şablonda kullanılmak üzere aktarılır.

Bir `dd()` ekleyerek çıktıyı görebilirsiniz:


```php
// src/Controller/RemoteController.php
// ... lines 1 - 29
        dd($remote->buttons());
        return $this->render('index.html.twig', [
// ... lines 33 - 37
```

👉 Bu satır, dönen buton isimlerini hızlıca görmenizi sağlar.

Tarayıcıda sayfayı yenileyin ve... beklediğimiz gibi değil! Bir sayı listesi yerine buton isimlerini istiyoruz. Bunu düzeltmek için `ButtonRemote` içinde `#[AutowireIterator]`'ı bulun. `#[AutowireLocator]` daha önce `#[AsTaggedItem]` üzerindeki `$index` değerini otomatik olarak anahtar olarak kullanıyordu, fakat `#[AutowireIterator]` bunu yapmaz! Varsayılan olarak integer anahtarlar döner.

## #\[AutowireIterator]'ın indexAttribute Parametresi

`#[AsTaggedItem]`'daki `$index`'i anahtar olarak kullanmasını söylemek için, `indexAttribute` parametresini `key` olarak ayarlayın:


```php
// src/Remote/ButtonRemote.php
// ... lines 1 - 9
    public function __construct(
        #[AutowireIterator(ButtonInterface::class, indexAttribute: 'key')]
        private iterable $buttons,
// ... lines 13 - 31
```

👉 Bu, iterable'daki anahtarları buton isimlerimiz (slug) yapar.

Şimdi döngüdeki `$name` buton adı olur. Kontrolcüde hala bir `dd()` var, uygulamaya dönüp yenileyin ve... işte oldu! Artık buton isimleri elimizde. Harika!

`dd()`'yi kaldırın, sonra `index.html.twig` dosyasını açın.


```php
// src/Controller/RemoteController.php
// ... lines 1 - 29
        dd($remote->buttons());
        return $this->render('index.html.twig', [
// ... lines 33 - 37
```

👉 Bu satırı kaldırıp işlemi tamamlayın.

## 🖼️ Butonları Dinamik Olarak Render Etme

Şu anda şablonda (index.html.twig) butonlar sabit kodlanmış durumda. Biraz boşluk bırakın ve sonra `buttons` için döngü kurun:


```twig
// templates/index.html.twig
// ... lines 1 - 4
{% block body %}
    <div class="mx-auto max-w-5xl">
        <div class="bg-[#1B1B1D] w-[477px] mx-auto rounded-xl p-6">
// ... lines 8 - 18
            <form method="post">
                <div class="flex justify-center">
                    <ul class="grid grid-cols-2 row-span-3 gap-8">
                        {% for button in buttons %}
// ... lines 23 - 35
                        {% endfor %}
                    </ul>
                </div>
            </form>
// ... lines 40 - 47
        </div>
    </div>
{% endblock %}
```

👉 Bu döngü ile şablondaki butonlar otomatik ve dinamik şekilde oluşturulur.

Arayüzde, ilk buton (yani "Power" butonu) diğerlerinden farklı görünüyor: kırmızı ve daha büyük. Bu özel stilin devamı için, `loop.first` kullanarak if-else ekleyin:


```twig
// templates/index.html.twig
// ... lines 1 - 21
                        {% for button in buttons %}
                            {% if loop.first %}
// ... lines 24 - 28
                            {% else %}
// ... lines 30 - 34
                            {% endif %}
                        {% endfor %}
// ... lines 37 - 51
```

👉 Bu yapı, ilk butonu (Power) özel şekilde render eder.

İlk butonun kodunu kopyalayıp buraya yapıştırın. "power" sabit yerine, `button` değişkenini kullanın. Aynı şekilde, Twig ikonunun adında da `button` değişkenini kullanın:


```twig
// templates/index.html.twig
// ... lines 1 - 22
                            {% if loop.first %}
                                <li class="col-span-2 flex justify-center -mb-4">
                                    <button name="button" value="{{ button }}" class="flex rounded-full border border-[#3F4241] hover:border-[#C33E21] w-[100px] h-[100px] justify-center items-center focus:bg-[#C33E21] group">
                                        <twig:ux:icon name="{{ button }}" width="184" height="184" class="fill-[#C33E21] group-focus:fill-[#ffffff]" />
                                    </button>
                                </li>
                            {% else %}
// ... lines 30 - 51
```

👉 İlk buton özel stil ve dinamik isim ile gösterilir.

Diğer butonlar için de, ikinci butonun kodunu kopyalayıp, value ve ikon adı olarak yine `button` değişkenini kullanın:


```twig
// templates/index.html.twig
// ... lines 1 - 28
                            {% else %}
                                <li>
                                    <button name="button" value="{{ button }}" class="flex rounded-full border border-[#3F4241] hover:border-white w-[80px] h-[80px] justify-center items-center focus:bg-[#ffffff] group">
                                        <twig:ux:icon name="{{ button }}" width="36" height="36" class="fill-white group-focus:fill-[#0E0E0E]" />
                                    </button>
                                </li>
                            {% endif %}
// ... lines 36 - 51
```

👉 Diğer tüm butonlar için dinamik isimler ve ikonlarla render edilir.

Geri kalan sabit kodlu butonları silin.

## 🔢 Sıralama: AsTaggedItem::\$priority ile Servislerin Sırası

Şimdi uygulamayı yenileyin... Butonlar render ediliyor fakat sıraları istediğimiz gibi değil. En üstte olması gereken başka bir buton var. Bunu düzeltmek için `AsTaggedItem`'ın ikinci parametresi olan `priority` kullanılır.

Örneğin, PowerButton için önceliği 50 olarak ayarlayın:


```php
// src/Remote/Button/PowerButton.php
// ... lines 1 - 6
#[AsTaggedItem('power', priority: 50)]
final class PowerButton implements ButtonInterface
// ... lines 9 - 15
```

👉 Power butonu için öncelik (priority) ayarlanır.

"Channel Up" butonunda öncelik 40:


```php
// src/Remote/Button/ChannelUpButton.php
// ... lines 1 - 6
#[AsTaggedItem('channel-up', priority: 40)]
final class ChannelUpButton implements ButtonInterface
// ... lines 9 - 15
```

👉 Channel Up butonu için öncelik 40 yapılır.

"Channel Down" için öncelik 30:


```php
// src/Remote/Button/ChannelDownButton.php
// ... lines 1 - 6
#[AsTaggedItem('channel-down', priority: 30)]
final class ChannelDownButton implements ButtonInterface
// ... lines 9 - 15
```

👉 Channel Down butonu için öncelik 30 yapılır.

"Volume Up" için öncelik 20:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 6
#[AsTaggedItem('volume-up', priority: 20)]
final class VolumeUpButton implements ButtonInterface
// ... lines 9 - 15
```

👉 Volume Up butonu için öncelik 20 yapılır.

"Volume Down" için öncelik 10:


```php
// src/Remote/Button/VolumeDownButton.php
// ... lines 1 - 6
#[AsTaggedItem('volume-down', priority: 10)]
final class VolumeDownButton implements ButtonInterface
// ... lines 9 - 15
```

👉 Volume Down butonu için öncelik 10 yapılır.

Bir butona öncelik atanmazsa, varsayılan değer 0'dır.

Uygulamayı yenileyin... işte oldu! Tüm butonlar otomatik ekleniyor ve doğru sırada gösteriliyor.

## ⚠️ Önemli: RewindableGenerator Hatası

Fakat büyük bir problem var. Herhangi bir butona bastığınızda... Hata!

**Attempted to call an undefined method "get" of class RewindableGenerator.**

Bu `RewindableGenerator`, Symfony'nin `#[AutowireIterator]` ile enjekte ettiği iterable nesnedir. Bunun üzerinde döngü kurabilirsiniz ama `get()` metodu yoktur.

## ⏭️ Sonraki Adım: Hem servis iterator'ü hem de locator olan bir nesne enjekte ederek bu problemi çözeceğiz.
