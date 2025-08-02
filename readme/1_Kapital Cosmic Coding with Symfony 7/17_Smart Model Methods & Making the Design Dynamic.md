---

🚀 Akıllı Model Metotları ve Dinamik Tasarım

### \[Smart Model Methods & Making the Design Dynamic] / \[Akıllı Model Metotları ve Tasarımı Dinamik Hale Getirmek]

Enum'un sonuna `.value` eklemek harika çalışıyor. Ama daha şık bir çözüm daha göstermek istiyorum.

---

🧠 Daha Akıllı Model Metotları Ekleme

Starship uygulamasında, genellikle bir Starship'in string durumunu almak isteyebiliriz. Bunu kolaylaştırmak için `getStatusString()` adında bir kısayol metodu ekleyelim. Bu bir string döndürecek ve içerisinde `$this->status->value` ifadesini döndürecek.

```php
// ... lines 1 - 4
class Starship
{
// ... lines 7 - 40
    public function getStatusString(): string
    {
        return $this->status->value;
    }
}
```

Bunun sayesinde şablonda `ship.statusString` şeklinde kısaltabiliriz.

---

🖼️ Twig'de Akıllı Özellik Erişimi

Twig bu konuda oldukça akıllıdır! `Starship` sınıfında `statusString` adında bir property yoktur. Ama Twig bunu önemsemez. `getStatusString()` metodunu görür ve çağırır.

```twig
<p class="uppercase text-xs text-nowrap">{{ ship.statusString }}</p>
```

Bunu alt etiketinde de kullanabiliriz:

```twig
<img class="h-[83px] w-[84px]" src="/images/status-in-progress.png" alt="Status: {{ ship.statusString }}">
```

---

🛠 show\.html.twig Güncellemesi

`show.html.twig` dosyasında da durum yazdırılıyor. Aynı değişikliği burada da yapalım:

```twig
<p class="uppercase text-xs">{{ ship.statusString }}</p>
```

---

🌌 Dinamik Şablonun Tamamlanması

Ana sayfa şablonunu tamamen dinamik hale getirelim. Gemi adı, kaptanı ve sınıfı için:

```twig
{{ ship.name }}
{{ ship.captain }}
{{ ship.class }}
```

Ve bağlantı için:

```twig
<a href="{{ path('app_starship_show', { id: ship.id }) }}">{{ ship.name }}</a>
```

---

🖼️ Dinamik Görsel Yolları

Görsel yolu hâlâ sabit ve yanlış. Doğru yolu şöyle alabiliriz:

```twig
<img src="{{ asset(ship.statusImageFilename) }}" alt="Status: {{ ship.statusString }}">
```

Görsel yolunu model sınıfına taşıyalım:

```php
public function getStatusImageFilename(): string
{
    return match ($this->status) {
        StarshipStatusEnum::WAITING => 'images/status-waiting.png',
        StarshipStatusEnum::IN_PROGRESS => 'images/status-in-progress.png',
        StarshipStatusEnum::COMPLETED => 'images/status-complete.png',
    };
}
```

---

🏠 Ana Sayfa Bağlantısı

Logo ana sayfaya gitmeli. Bunun için route'a isim veriyoruz:

```php
#[Route('/', name: 'app_homepage')]
```

Sonra bu yolu `base.html.twig` dosyasında kullanıyoruz:

```twig
<a href="{{ path('app_homepage') }}">
```

Ve navigasyon linki olarak tekrar:

```twig
<a class="hover:text-amber-400 pt-2" href="{{ path('app_homepage') }}">
    Home
</a>
```

---

🔙 "Geri Dön" Linki

`show.html.twig` dosyasında "Back" linkini de aynı şekilde güncelliyoruz:

```twig
<a class="bg-white hover:bg-gray-200 rounded-xl p-2 text-black" href="{{ path('app_homepage') }}">
    <svg>...</svg>
    Back
</a>
```

---

🎉 Tebrikler!

Tasarım artık tamamlandı! Artık sayfamız gerçek bir uygulama gibi görünüyor ve hissediliyor. Harikasınız!

Sırada: Tıklanınca sidebar’ı kapatma gibi daha ince detaylar. Bunun için Stimulus kullanacağız.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./16_ PHP Enums.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./18. Stimulus Writing Pro JavaScript.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
