# 🧠 Quantum Refactor: Rich Entities / Quantum Refactor: Zengin Entity'ler

Starship entity'sine bir göz atalım. Bir sürü özellik, getter ve setter içeriyor. Biraz sıkıcı, değil mi? Olmak zorunda değil! Entity'ler standart PHP sınıfları olduğundan, iş mantığımızı tanımlayan anlamlı ve açık metotlar ekleyebiliriz, örneğin `goToWarp(7)` veya `enterOrbitAround($millersPlanet)`. Bunlara _zengin entity metotları_ denir.

Haydi bunu deneyelim ve faydalarını keşfedelim.

## Reduce Duplication / Yinelenmeyi Azaltmak

Şu anda `Starship` için check-in (giriş yapma) mantığı `ShipCheckInCommand::execute()` metodunda yer alıyor. Gemiyi aldıktan sonra `arrivedAt` ve `status` alanlarını güncelliyoruz. Diyelim ki gelecekte bir check-in controller'ı eklemek istiyoruz. Bu mantığı oraya da kopyalamamız gerekecek. Ve eğer "check-in" işleminin mantığı değişirse — örneğin başka bir alanın da güncellenmesi gerekirse — bunu birden fazla yerde değiştirmeyi hatırlamamız gerekecek. Bu hiç de bilim kurguya yakışmaz.

## Adding a `Starship::checkIn()` Method / `Starship::checkIn()` Metodu Ekleme

Daha iyi yol, bu check-in mantığını entity içindeki bir metoda taşımak veya kapsüllemektir. `src/Entity/Starship.php` dosyasını açın ve en altına gidin. Yeni bir `public function checkIn()` metodu oluşturun. Bu metod isteğe bağlı olarak `?\DateTimeImmutable $arrivedAt = null` parametresini alsın ve `static` döndürsün. Bu, "mevcut nesneyi döndür" demenin süslü bir yoludur:

```php
// src/Entity/Starship.php

// ... lines 1 - 10
class Starship
{
// ... lines 13 - 159
    public function checkIn(?\DateTimeImmutable $arrivedAt = null): static
    {
// ... lines 162 - 165
    }
}
```

👉 Bu metod, check-in işlemini `Starship` entity'si içinde kapsüller.

> return $this:

```php
// src/Entity/Starship.php

// ... lines 1 - 10
class Starship
{
// ... lines 13 - 159
    public function checkIn(?\DateTimeImmutable $arrivedAt = null): static
    {
// ... lines 162 - 164
        return $this;
    }
}
```

👉 Bu kod, mevcut nesneyi (`$this`) döndürür.

Yukarıya check-in mantığını ekleyin: `$this->arrivedAt = $arrivedAt`, ve eğer parametre verilmemişse `?? new \DateTimeImmutable('now')`. Ardından, `$this->status = StarshipStatusEnum::WAITING`:

```php
// src/Entity/Starship.php

// ... lines 1 - 10
class Starship
{
    // ... lines 13 - 159
    public function checkIn(?\DateTimeImmutable $arrivedAt = null): static
    {
        $this->arrivedAt = $arrivedAt ?? new \DateTimeImmutable('now');
        $this->status = StarshipStatusEnum::WAITING;
    // ... lines 164 - 165
    }
}
```

👉 Bu metod, geminin geldiği zamanı ve durumunu ayarlar.

## Using the `Starship::checkIn()` Method / `Starship::checkIn()` Metodunu Kullanma

`ShipCheckInCommand` sınıfına geri dönün ve mantığı `$ship->checkIn()` ile değiştirin:

```php
// src/Command/ShipCheckInCommand.php

// ... lines 1 - 17
class ShipCheckInCommand extends Command
{
    // ... lines 20 - 33
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
    // ... lines 36 - 47
        $ship->checkIn();
    // ... lines 49 - 54
    }
}
```

👉 Bu kod, entity üzerindeki `checkIn()` metodunu kullanarak işlemi sadeleştirir.

Bu işlemin hala çalıştığından emin olmak için ana sayfaya dönün ve sayfayı yenileyin. Durumu "waiting" olmayan bir gemi bulun... İşte burada: "Stellar Pirate". Buna tıklayın ve URL'den slug'ı kopyalayın. Terminale geri dönün ve şu komutu çalıştırın:

```bash
symfony console app:ship:check-in
```

👉 Bu komut, gemiyi check-in yapmak için çalıştırılır.

Slug'ı yapıştırın ve çalıştırın! Başarılı! Uygulamaya dönüp sayfayı yenileyin. Mükemmel! Gemi artık "waiting" olarak işaretlendi ve 6 saniye önce geldi olarak görünüyor.

Eğer kendinizi entity'ler üzerinde tekrar eden işlemler yaparken buluyorsanız, bu işlemi tanımlayan bir metot eklemeyi ve kullanmayı düşünün. Okunabilirlik ve bakım kolaylığı açısından kolay bir kazançtır.

Tamam ekip, `Doctrine Fundamentals` dersi bu kadar! Doctrine becerilerinizi geliştirmek istiyorsanız, SymfonyCasts üzerinde "Doctrine" araması yaparak daha ileri seviye kurslara ulaşabilirsiniz. Doctrine dokümantasyonu da harika bir kaynaktır. Ve her zaman olduğu gibi, yorumlar bölümünde sorularınızı yanıtlamaya hazırız.

Bir dahaki sefere kadar, mutlu kodlamalar!

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./14_Ship Upgrades Updating an Entity.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
</div>
