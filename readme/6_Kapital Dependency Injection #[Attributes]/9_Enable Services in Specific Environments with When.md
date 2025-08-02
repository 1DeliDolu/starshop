# 🕹️ Enable Services in Specific Environments with When / Belirli Ortamlarda Servisleri When ile Etkinleştirmek

Uzaktan kumandamızda doğru çalışıp çalışmadığını kontrol etmek için özel bir gizli butonumuz olsaydı harika olmaz mıydı? Elbette olurdu! Sadece `dev` ortamında kullanılabilecek gizli bir "Diagnostics" butonu ekleyelim.

## ➕ Adding a Diagnostics Button / Diagnostics Butonu Eklemek

`App\Remote\Button` içinde yeni bir sınıf oluşturun: `DiagnosticsButton`. `ButtonInterface`'i implements etsin... ve "control" + "enter" ile `press()` metodunu ekleyin. İçerisine `dump('Running diagnostics...')` yazın... ve daha önce olduğu gibi, `#[AsTaggedItem]` özniteliğini `diagnostics` indexiyle ekleyin:


```php
// src/Remote/Button/DiagnosticsButton.php
// ... lines 1 - 6
#[AsTaggedItem('diagnostics')]
final class DiagnosticsButton implements ButtonInterface
{
    public function press(): void
    {
        dump('Running diagnostics...');
    }
}
```

👉 Bu kod, yalnızca `diagnostics` etiketiyle işaretlenmiş yeni bir buton oluşturur.

Son olarak, `diagnostics.svg` dosyasını `tutorial/` klasöründen `assets/icons/` dizinine kopyalayın.

Uygulamaya dönüp sayfayı yenileyin... yeni buton görünecek! Ve butona bastığınızda... çalışıyor gibi görünüyor!

## ⏳ #\[When]

Yeni butonumuz otomatik olarak servis konteynerine kaydoldu, fakat biz onu sadece `dev` ortamında istiyoruz. `#[When]` özniteliği bunun için mükemmel. `DiagnosticsButton`'a dönüp, başına `#[When]` ekleyin ve parametre olarak `dev` yazın:


```php
// src/Remote/Button/DiagnosticsButton.php
// ... lines 1 - 8
#[When('dev')]
final class DiagnosticsButton implements ButtonInterface
// ... lines 11 - 17
```

👉 Bu kod ile, bu sınıf yalnızca `dev` ortamında servis konteynerine kaydedilir.

Bunun sayesinde, dev ortamında değilsek bu sınıf servis konteyneri tarafından tamamen göz ardı edilir. Sayfayı yenileyin, hala orada. Mantıklı, çünkü şu an dev ortamındayız. Şimdi, bunu test etmek için `#[When]` argümanını `dev` yerine `prod` olarak değiştirin:


```php
// src/Remote/Button/DiagnosticsButton.php
// ... lines 1 - 8
#[When('prod')]
final class DiagnosticsButton implements ButtonInterface
// ... lines 11 - 17
```

👉 Artık sadece prod ortamında kaydolur. Yenileyin... buton yok! Harika!

## 🚫 #\[Exclude]

Bu çalıştığına göre, şimdi `#[When]`'in kuzeni olan `#[Exclude]`'dan bahsedelim. Bu öznitelik, Symfony'ye belirli bir sınıfı asla, hiçbir şekilde servis konteynerine kaydetmemesini söyler. Şu anda, `config/services.yaml` dosyasındaki `App/:` bölümü, Symfony'ye `src/` klasöründeki her sınıfı otomatik bağlamasını söyler. Buradaki `exclude` anahtarı ise, göz ardı edilmesi gereken yolların listesini içerir ve bu, geleneksel olarak sınıfların servis olarak kaydedilmesini engellemenin yoludur. Bu yöntem iyi, ama biraz hantal. İşte burada `#[Exclude]` devreye girer.

`MuteButton` sınıfında, en üste `#[Exclude]` ekleyin:


```php
// src/Remote/Button/MuteButton.php
// ... lines 1 - 8
#[Exclude]
final class MuteButton implements ButtonInterface
// ... lines 11 - 17
```

👉 Bu öznitelik sayesinde, `MuteButton` servis konteynerinde asla yer almaz.

Uygulamaya dönüp sayfayı yenileyin. "Mute" butonu artık yok! Çalıştı.

Bu öznitelik uygulamanızda çok yaygın olmayabilir, fakat bu DI öznitelik dersi olduğu için tüm güzel özellikleri görüyorsunuz!

Sonraki adım: Lazy servisleri konuşacağız.
