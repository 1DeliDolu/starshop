## 📦 KnpTimeBundle: Bundle'ı Kurmak ve Servisini Kullanmak

### KnpTimeBundle: Install the Bundle, Get its Service / KnpTimeBundle: Bundle'ı Kurmak ve Servisini Almak

Sitemizde, müşterilerin kolayca görebileceği bir "Gemi Onarım Kuyruğu" var. Bu kuyruk, onarılan tüm gemileri ve durumlarını listeliyor. Bu eğitimde, `Starship` sınıfımıza yeni bir `$arrivedAt` alanı ekledik. Hedefimiz bu alanı ana sayfada göstermek.

```php
// src/Model/Starship.php

class Starship
{
    public function __construct(
        // ...
        private \DateTimeImmutable $arrivedAt,
    ) {
    }

    public function getArrivedAt(): \DateTimeImmutable
    {
        return $this->arrivedAt;
    }
}
```

Ana sayfadan sorumlu denetleyiciyi unuttuysanız, web debug araç çubuğunda sayfa bilgisinin üzerine gelebilirsiniz: "MainController::homepage" yazar. `MainController.php` dosyasını açın ve `homepage()` metodunu bulun. Bu metod, `main/homepage.html.twig` şablonunu render ediyor.

---

### Twig Şablonlarında Tarih Yazdırmak

Bu şablonu açın, "Ship Repair Queue" kısmını bulun ve `{{ ship.name }}`'den sonra bir `<div>` ekleyin:

```twig
<div>
    Arrived at: {{ ship.arrivedAt }}
</div>
```

Sayfayı yenileyin... ve bir hata alırsınız:

> Object of class DateTimeImmutable could not be converted to string

Bu normal, çünkü PHP bir `DateTime` nesnesini doğrudan yazdırmayı bilmez — hangi formatta yazdırması gerektiğini bilmez. Bunu düzeltmek için bir Twig filtresi kullanabiliriz. `arrivedAt` ifadesinden sonra `|date` ekleyin:

```twig
<div>
    Arrived at: {{ ship.arrivedAt|date }}
</div>
```

Sayfayı yenilediğinizde artık tarih düzgün şekilde görünecektir.

---

### Varsayılan Tarih Formatını Öğrenmek

Varsayılan olarak hangi tarih formatının kullanıldığını merak ediyorsanız, terminalden şu komutu çalıştırın:

```bash
bin/console config:dump twig
```

Aslında bu komutun tam hali şudur:

```bash
bin/console config:dump-reference twig
```

Symfony komutlarında, isimleri kısaltabilirsiniz — sadece başka komutlarla çakışmamasına dikkat edin.

---

### Yeni Bir Bundle Kurmak

Ancak şöyle bir şey yazmak daha havalı olurdu: "2 saat önce". Ne yazık ki şu an uygulamamızda bunu yapabilecek bir servis yok. Ama yazmamıza da gerek yok — böyle bir servisi sağlayan bir bundle var: **KnpTimeBundle**.

GitHub'dan bundle'ı bulun ve "Installation" bölümündeki komutu terminalde çalıştırın:

```bash
composer require knplabs/knp-time-bundle
```

Bu işlem bundle'ı, bağımlılıklarını ve bazı Symfony tariflerini yükler. Ardından şu komutla dosya değişikliklerini kontrol edebilirsiniz:

```bash
git status
```

Göreceksiniz ki `composer.json`, `composer.lock`, `symfony.lock` ve `bundles.php` dosyaları değişmiş.

```php
// config/bundles.php

return [
    // ...
    Knp\Bundle\TimeBundle\KnpTimeBundle::class => ['all' => true],
];
```

Bundle böylece uygulamaya kayıt edilmiş olur. Bundle’lar bize servis sağlar. Peki bu bundle bize hangi servisleri sundu?

---

### Yeni Servisleri Keşfetmek

Terminalde şu komutu çalıştırın:

```bash
bin/console debug:container time
```

`datetime_formatter` servisini seçin (örneğin 10. seçenek olabilir) ve detaylarına bakın.

Bu servisi autowire (otomatik bağlama) ile kullanabilir miyiz diye kontrol etmek için:

```bash
bin/console debug:autowiring time
```

Evet, kullanabiliriz! Ama bunu sadece Twig şablonunda kullanmak istiyoruz. Neyse ki bu bundle bir Twig filtresi de sağlıyor.

---

### Twig Filter Kullanmak

Bu bundle’ın hangi Twig filtrelerini sunduğunu görmek için:

```bash
bin/console debug:twig
```

`ago` filtresini arayın. Daha önce kullandığımız `|date` filtresinin yerini alacak bu filtre, tarih nesnesini daha okunabilir bir biçime dönüştürüyor.

Şimdi `|date` filtresini `|ago` ile değiştirin:

```twig
<div>
    Arrived at: {{ ship.arrivedAt|ago }}
</div>
```

Sayfayı yenileyin ve işte bu kadar! Artık tarih "2 hours ago" gibi okunabilir bir formatta görüntüleniyor.

---

### Özet

Bundle’lar bize servis sağlar, servisler araçlardır ve araçlarla oynamak eğlencelidir.

Sırada: Yeni Symfony bileşenlerini kurarak daha fazla servis eklemek!

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./1_Setup, Services & the Service Container.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./3_The HTTP Client Service.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
