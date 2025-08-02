# 😴 More Laziness Attributes / Daha Fazla Tembellik (Lazy) Özniteliği

"Lazy servislerin" en güzel yanı, kodunuzda hiçbir değişiklik gerektirmemesi (servisleriniz final olmadığı sürece). Ama ya `ParentalControls` servisi bir üçüncü parti pakette yaşasaydı ve final olsaydı? Zor! Ama bazı seçeneklerimiz var.

## 🌀 #\[AutowireServiceClosure]

Diyelim ki `ParentalControls` final ve bir üçüncü parti paketten geliyor. `VolumeUpButton`'da `#[Lazy]` yerine `#[AutowireServiceClosure]` kullanın ve parametre olarak `ParentalControls::class` verin:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 9
final class VolumeUpButton implements ButtonInterface
{
// ... lines 12 - 14
    public function __construct(
        #[AutowireServiceClosure(ParentalControls::class)]
        private \Closure $parentalControls,
    ) {
    }
// ... lines 20 - 28
}
```

👉 Bu kullanım, ihtiyacımız olduğunda `ParentalControls` örneğini döndüren bir closure (kapanış) enjekte eder; sadece çağrıldığında örneklenir.

IDE'nize yardımcı olmak için, constructor'un üstüne şu docblock'u ekleyin: `@param \Closure():ParentalControls $parentalControls`


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 9
final class VolumeUpButton implements ButtonInterface
{
    /**
     * @param \Closure():ParentalControls $parentalControls
     */
    public function __construct(
// ... lines 16 - 17
    ) {
    }
// ... lines 20 - 28
}
```

Şimdi aşağıda, `press()` metodundaki if bloğunda false'u true yapın. Çünkü `$parentalControls` artık bir closure, çağırmak için `$this->parentalControls`'u parantez ile çağırmalı ve ardından `->volumeTooHigh()` demelisiniz:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 9
final class VolumeUpButton implements ButtonInterface
{
// ... lines 12 - 20
    public function press(): void
    {
        if (true) { // determine if volume is too high
            ($this->parentalControls)()->volumeTooHigh();
        }
        dump('Change the volume up');
    }
}
```

👉 Artık closure çağrıldığında servis örneklenir ve kullanılır.

IDE docblock sayesinde, otomatik tamamlama, metotlara hızlı erişim ve static analiz araçlarından daha fazla fayda alırsınız.

`dump()`'u kaldırın, uygulamayı yenileyin ve "volume up" butonuna basın. Profilere bakın; `volumeTooHigh()` mantığı çağrılıyor. Harika! `ParentalControls` servisi sadece closure çağrıldığında örneklenir.

## ☎️ #\[AutowireCallable]

Aynı işi farklı şekilde yapmak için bir yöntem daha var. `#[AutowireServiceClosure]` yerine `#[AutowireCallable]` kullanın. Yine ilk argüman olarak `ParentalControls::class` verin fakat başına `service:` ekleyin:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 9
final class VolumeUpButton implements ButtonInterface
{
// ... lines 12 - 14
    public function __construct(
        #[AutowireCallable(
            service: ParentalControls::class,
// ... lines 18 - 19
        )]
        private \Closure $parentalControls,
    ) {
    }
// ... lines 24 - 32
}
```

`#[AutowireCallable]` da bir closure enjekte eder. Fakat, tam servis objesini döndürmek yerine, servisi örnekler, tek bir metodunu çağırır ve sonucu döndürür.

Daha açıklayıcı olması için ikinci bir argüman ekleyin: `method: 'volumeTooHigh'`:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 14
    public function __construct(
        #[AutowireCallable(
            service: ParentalControls::class,
            method: 'volumeTooHigh',
// ... line 19
        )]
        private \Closure $parentalControls,
    ) {
    }
// ... lines 24 - 34
```

Symfony, default olarak bu servisi hemen örnekler. Bunu engellemek için üçüncü bir argüman ekleyin: `lazy: true`:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 9
final class VolumeUpButton implements ButtonInterface
{
// ... lines 12 - 14
    public function __construct(
        #[AutowireCallable(
            service: ParentalControls::class,
            method: 'volumeTooHigh',
            lazy: true,
        )]
        private \Closure $parentalControls,
    ) {
    }
// ... lines 24 - 32
}
```

👉 Bu şekilde, servis sadece closure çağrıldığında örneklenir.

Yukarıdaki docblock'u, closure'ın dönüş tipine uygun şekilde void yapın:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 9
final class VolumeUpButton implements ButtonInterface
{
    /**
     * @param \Closure():void $parentalControls
     */
    public function __construct(
// ... lines 16 - 21
    ) {
    }
// ... lines 24 - 32
}
```

Aşağıda, `press()` metodunda artık `->volumeTooHigh()` yerine closure'ı doğrudan çağırın:


```php
// src/Remote/Button/VolumeUpButton.php
// ... lines 1 - 9
final class VolumeUpButton implements ButtonInterface
{
// ... lines 12 - 24
    public function press(): void
    {
        if (true) { // determine if volume is too high
            ($this->parentalControls)();
        }
// ... lines 30 - 31
    }
}
```

👉 Bu closure çağrıldığında, servis örneklenir ve metodu çağrılır.

Uygulamayı yenileyin, "volume up" butonuna basın, profiler'da kontrol edin. `ParentalControls::volumeTooHigh()` mantığı çağrılıyor.

## 📝 Notlar

* `#[AutowireCallable]` güzel bir seçenek, fakat çoğu durumda `#[AutowireServiceClosure]` daha uygundur:

  * Varsayılan olarak lazy'dir.
  * Tüm servis objesini döndürdüğü için daha esnektir.
  * Doğru docblock ile otomatik tamamlama, refaktör ve statik analiz avantajı sağlar.

YAML servis konfigürasyonu tamamen yok olmuyor, ama bu öznitelikler kodunuzu ve servis yapılandırmanızı bir arada tutarak geliştirici deneyimini iyileştiriyor.

Symfony'nin her yeni sürümünde yeni öznitelikler ekleniyor. Symfony blogunu takip ederek güncel kalın! Örneğin, Symfony 7.2 ile birlikte yeni bir `#[WhenNot]` özniteliği geldi. Temelde, daha önce gördüğümüz `#[When]`'in tersi gibi çalışıyor.

Symfony Attributes Overview dokümanındaki "Dependency Injection" bölümüne göz atarak, mevcut tüm bağımlılık enjeksiyonu özniteliklerini ve çalışma şekillerini inceleyebilirsiniz.

Bir sonraki sefere kadar! Mutlu kodlamalar!
