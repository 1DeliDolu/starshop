# 🕳️ Black Hole: Deleting Entities / Kara Delik: Varlıkları Silme

Aman! USS Leafy Cruiser adlı geminin bir kara deliğe düştüğünü öğrendik. Neyse ki uzun vadeli, sevilen karakterler gemide değildi, ama bu gemi artık spagettiye dönüştü. Bu gerçeklikte artık var olmadığı için, veritabanımızdan silmemiz gerekiyor.

## `app:ship:remove` Command / `app:ship:remove` Komutu

Bu işlemi gerçekleştirecek bir komut oluşturalım. Terminalde şunu çalıştırın:

```bash
symfony console make:command
```

Ad olarak `app:ship:remove` kullanın. Bu, yeni bir komut sınıfı oluşturur.

## Command Constructor / Komut Yapıcısı

Dosyayı açın: `src/Command/ShipRemoveCommand.php`. Maker bizim için bazı şablon kodlar ekledi. Açıklamayı şu şekilde güncelleyin: `Delete a starship`

```php
// src/Command/ShipRemoveCommand.php

// ... lines 1 - 13
#[AsCommand(
    name: 'app:ship:remove',
    description: 'Delete a starship',
)]
class ShipRemoveCommand extends Command
// ... lines 19 - 56
```

👉 Bu açıklama, komutun ne yaptığını belirtir: bir yıldız gemisini silmek.

Yapıcı metoduna iki şey enjekte etmemiz gerekiyor: `private ShipRepository $shipRepo` ve `private EntityManagerInterface $em`

```php
// src/Command/ShipRemoveCommand.php
use App\Repository\StarshipRepository;
use Doctrine\ORM\EntityManagerInterface;

// ... lines 1 - 17
class ShipRemoveCommand extends Command
{
    public function __construct(
        private StarshipRepository $shipRepo,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }
// ... lines 26 - 54
}
```

👉 Repository varlıkları bulmak için, `EntityManager` ise silmek, kaydetmek ve güncellemek gibi işlemler için kullanılır.

## Command Configuration / Komut Yapılandırması

`configure()` metodunda `addOption()`'ı kaldırın. `addArgument()` kısmında isim olarak `slug` verin, `InputArgument::REQUIRED` olarak ayarlayın ve açıklamayı şu şekilde güncelleyin: `The slug of the starship`

```php
// src/Command/ShipRemoveCommand.php

// ... lines 1 - 17
class ShipRemoveCommand extends Command
{
// ... lines 20 - 26
    protected function configure(): void
    {
        $this
            ->addArgument('slug', InputArgument::REQUIRED, 'The slug of the starship')
        ;
    }
// ... lines 33 - 54
}
```

👉 Bu ayar, komut çalıştırıldığında bir `slug` parametresi girmeyi zorunlu kılar.

## Command Logic / Komut Mantığı

`execute()` metodunda `$arg1 =` ifadesini şu şekilde değiştirin: `$slug = $input->getArgument('slug')`

```php
// src/Command/ShipRemoveCommand.php

// ... lines 1 - 17
class ShipRemoveCommand extends Command
{
    // ... lines 20 - 33
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
    // ... line 36
        $slug = $input->getArgument('slug');
    // ... lines 38 - 53
    }
}
```

👉 Girilen `slug` argümanını `$slug` değişkenine alır.

Sonra bu `slug` ile gemiyi bulmamız gerekiyor. Her `EntityRepository` zaten `findOneBy()` metoduna sahiptir. Şöyle yazın: `$ship = $this->shipRepo->findOneBy(['slug' => $slug])`

```php
// src/Command/ShipRemoveCommand.php

// ... lines 1 - 17
class ShipRemoveCommand extends Command
{
    // ... lines 20 - 33
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
    // ... lines 36 - 37
        $ship = $this->shipRepo->findOneBy(['slug' => $slug]);
    // ... lines 39 - 53
    }
}
```

👉 Bu satır, `slug` değerine göre veritabanından gemiyi bulur.

`if` ifadesini şu şekilde düzenleyin: `if (!$ship)` — çünkü `findOneBy()` bulunamazsa `null` döner. İçine şu satırları ekleyin: `$io->error('Starship not found.')` ve `return Command::FAILURE`

```php 
// src/Command/ShipRemoveCommand.php

// ... lines 1 - 17
class ShipRemoveCommand extends Command
{
// ... lines 20 - 33
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
    // ... lines 36 - 39
        if (!$ship) {
            $io->error('Starship not found.');
            return Command::FAILURE;
        }
    // ... lines 45 - 53
    }
}
``` 

👉 Bu kontrol, gemi bulunamadığında hatayı bildirir ve komutu başarısız olarak sonlandırır.

Kullanıcıya hangi geminin silineceğini göstermek için bir yorum satırı yazın: `$io->comment(sprintf('Removing starship: %s', $ship->getName()))`

```php 
// src/Command/ShipRemoveCommand.php

// ... lines 1 - 17
class ShipRemoveCommand extends Command
{
    // ... lines 20 - 33
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
    // ... lines 36 - 45
        $io->comment(sprintf('Removing starship: %s', $ship->getName()));
    // ... lines 47 - 53
    }
}
```

👉 Bu satır, hangi geminin silineceğini konsola yazar.

Şablon kodu kaldırın ve şu iki satırı yazın: `$this->em->remove($ship);` ve ardından `$this->em->flush();`

```php 
// src/Command/ShipRemoveCommand.php

// ... lines 1 - 17
class ShipRemoveCommand extends Command
{
    // ... lines 20 - 33
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
    // ... lines 36 - 47
        $this->em->remove($ship);
        $this->em->flush();
    // ... lines 50 - 53
    }
}
```

👉 Bu kod, gemiyi silme işlemini gerçekleştirir.

Bir başarı mesajı ekleyin: `$io->success('Starship removed.')` ve ardından `return Command::SUCCESS;`

```php 
// src/Command/ShipRemoveCommand.php
// ... lines 1 - 17
class ShipRemoveCommand extends Command
{
// ... lines 20 - 33
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
// ... lines 36 - 50
        $io->success('Starship removed.');
        return Command::SUCCESS;
    }
}
```

👉 Bu, başarılı silme işlemini bildirir.

> Komut Tamamlandı!

Uygulamaya dönün, sayfayı yenileyin ve geminin hala orada olduğundan emin olun. URL’den `slug`’ı kopyalayın.

## Running the Command / Komutu Çalıştırma

Terminale dönün ve şu komutu çalıştırın:

```bash 
symfony console app:ship:remove
```

Kopyaladığınız `slug`'ı yapıştırın ve çalıştırın. Başarılı! Gemi silindi. Aynı komutu tekrar çalıştırın:

```bash 
symfony console app:ship:remove leafy-cruiser-ncc-0001
```

"Starship not found." Harika! Uygulamaya dönün ve sayfayı yenileyin. 404. Gemi veritabanından silinmiş!

Tamamdır! Varlıkları kaydetmeyi ve silmeyi gördük. Sırada yıldız gemisi varlığını güncellemeyi öğrenmek var.
