# 🔧 Ship Upgrades: Updating an Entity / Gemi Yükseltmeleri: Bir Varlığı Güncelleme

Yıldız gemisi tamir programımız – pardon, işimiz – gayet iyi gidiyor! Şimdi artık bazı müşterilerimiz geri geliyor ve gemilerine ek yükseltmeler istiyor. Statüsü `completed` olan mevcut bir yıldız gemisini "giriş yapmış" (check-in) olarak güncelleyebilmemiz gerekiyor.

## Finding a Completed Ship / Tamamlanmış Bir Gemi Bulma

Ana sayfamızdaki liste sadece tamamlanmamış yıldız gemilerini gösteriyor, bu yüzden tamamlanmış bir tanesini terminalden bulmamız gerekiyor. Şunu çalıştırın:

```bash
symfony console doctrine:query:sql "SELECT slug, status FROM starship"
```

`lunar-marauder-1` tamamlanmış bir gemi. `slug`’ı kopyalayın ve uygulamada `/starships/lunar-marauder-1` adresine gidin. Evet, ulaştık. Güncellemeyi daha iyi görebilmek için `arrivedAt` tarihini gösterim sayfasında gösterelim.

`templates/starship/show.html.twig` dosyasında `h4` ve `p` etiketlerini kopyalayın. Aşağıya yapıştırın. `h4` içeriğini `Arrived At`, `p` içeriğini ise `{{ ship.arrivedAt|ago }}` olarak güncelleyin:

```twig
// templates/starship/show.html.twig

// ... lines 1 - 4
{% block body %}
// ... lines 6 - 11
<div class="md:flex justify-center space-x-3 mt-5 px-4 lg:px-8">
// ... lines 13 - 15
    <div class="space-y-5">
        <div class="mt-8 max-w-xl mx-auto">
            <div class="px-8 pt-8">
                // ... lines 19 - 35
                <h4 class="text-xs text-slate-300 font-semibold mt-2 uppercase">Arrived At</h4>
                <p class="text-[22px] font-semibold">{{ ship.arrivedAt|ago }}</p>
            </div>
        </div>
    </div>
</div>
{% endblock %}
```

👉 Bu bölüm, geminin ne zaman geldiğini insan okunur biçimde gösterir.

Uygulamaya dönüp sayfayı yenileyin. Bu gemi tamamlanmış ve 1 ay önce ulaşmış.

## `app:ship:check-in` Command/  `app:ship:check-in` Komutu

Yeni bir komut oluşturalım:

```bash
symfony console make:command
```

Ad olarak `app:ship:check-in` girin.

## Updating Command Boilerplate / Komut Şablonunu Güncelleme

Yeni sınıfı açın: `src/Command/ShipCheckInCommand.php`. Açıklamayı şu şekilde güncelleyin: `Check-in a ship`

```php
// src/Command/ShipCheckInCommand.php
use App\Repository\StarshipRepository;
use Doctrine\ORM\EntityManagerInterface;
// ... lines 1 - 14
#[AsCommand(
    name: 'app:ship:check-in',
    description: 'Check-in a ship',
)]
class ShipCheckInCommand extends Command
// ... lines 20 - 59
```

👉 Bu açıklama, komutun yıldız gemisini "giriş" olarak işaretleyeceğini belirtir.

Yapıcı metotta, `remove` komutundaki ile aynı bağımlılıkları kullanacağız. Oradan kopyalayın ve `ShipCheckInCommand::__construct()` içine yapıştırın:

```php
// src/Command/ShipCheckInCommand.php

// ... lines 1 - 18
class ShipCheckInCommand extends Command
{
    public function __construct(
        private StarshipRepository $shipRepo,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }
// ... lines 27 - 57
}
```

👉 `StarshipRepository` ile gemiyi bulacağız, `EntityManagerInterface` ile güncelleme yapacağız.

Ayrıca gemiyi `slug` ile bulacağımız için, `configure()` metodunu `ShipRemoveCommand`’dan kopyalayın:

```php
// src/Command/ShipCheckInCommand.php

// ... lines 1 - 18
class ShipCheckInCommand extends Command
{
// ... lines 21 - 27
    protected function configure(): void
    {
        $this
            ->addArgument('slug', InputArgument::REQUIRED, 'The slug of the starship')
        ;
    }
// ... lines 34 - 57
}
```

👉 Bu, `slug` parametresini zorunlu hale getirir.

## Command Logic / Komut Mantığı

`execute()` metodunun ilk kısmı da `remove` komutuyla aynı. Oradan kopyalayın, sadece yorum satırını şu şekilde değiştirin: `"Checking-in starship..."`

```php 
// src/Command/ShipCheckInCommand.php

// ... lines 1 - 18
class ShipCheckInCommand extends Command
{
// ... lines 21 - 34
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $slug = $input->getArgument('slug');
        $ship = $this->shipRepo->findOneBy(['slug' => $slug]);
        if (!$ship) {
            $io->error('Starship not found.');
            return Command::FAILURE;
        }
        $io->comment(sprintf('Checking-in starship: %s', $ship->getName()));
// ... lines 48 - 56
    }
}
```

👉 Bu kod, gemiyi bulur ve işlem başlatıldığında bilgi verir.

Şimdi "check-in" mantığını ekleyelim. Önce `arrivedAt` alanını şu anki zamanla güncelleyin:
`$ship->setArrivedAt(new \DateTimeImmutable('now'));`
Sonra durumu `"waiting"` olarak ayarlayın:
`$ship->setStatus(StarshipStatusEnum::WAITING);`

```php
// src/Command/ShipCheckInCommand.php

// ... lines 1 - 18
class ShipCheckInCommand extends Command
{
// ... lines 21 - 34
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
// ... lines 37 - 48
        $ship->setArrivedAt(new \DateTimeImmutable('now'));
        $ship->setStatus(StarshipStatusEnum::WAITING);
// ... lines 51 - 56
    }
}
```

👉 Bu, geminin geldiği zamanı ve yeni durumunu ayarlar.

Şimdi bu değişiklikleri veritabanına işlemek için şu satırı ekleyin: `$this->em->flush();`

```php 
// src/Command/ShipCheckInCommand.php

// ... lines 1 - 18
class ShipCheckInCommand extends Command
{
// ... lines 21 - 34
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
// ... lines 37 - 51
        $this->em->flush();
// ... lines 53 - 56
    }
}
```

👉 Doctrine, değişiklikleri algılar ve doğru SQL güncellemesini otomatik olarak yapar.


## Just flush()? / Sadece flush() mı?

Bekleyin, bekleyin! Bir varlığı kaydederken (persist) veya silerken (remove), niyetimizi Doctrine'e bildirmek için entity manager üzerinde bir metot çağırmamız gerekiyordu. Burada ise gerek yok mu? Hayır! Doctrine çok akıllı. Yukarıda, varlığı bulduğumuzda Doctrine onu izlemeye başlar. flush() çağırdığımızda, değiştirildiğini görür ve veritabanını güncellemek için en uygun SQL'i belirler. Harika!

Son olarak, "Starship checked-in" şeklinde bir başarı mesajı ekleyin:

```php
// src/Command/ShipCheckInCommand.php
// ... lines 1 - 18
class ShipCheckInCommand extends Command
{
// ... lines 21 - 34
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
// ... lines 37 - 53
        $io->success('Starship checked-in.');
        return Command::SUCCESS;
    }
}
```

👉 Bu, komutun başarıyla çalıştığını bildirir.

## Running the Command / Komutu Çalıştırma

Uygulamaya dönün. Giriş yapmak istediğiniz gemiyi belirleyin ve `slug`’ını URL'den kopyalayın.

Terminalde şu komutu çalıştırın:

```bash
symfony console app:ship:check-in
```

`slug`’ı yapıştırın ve çalıştırın. Başarılı! Uygulamaya dönüp sayfayı yenileyin. Gemi artık "waiting" durumunda ve 9 saniye önce ulaşmış. Çalışıyor!

`ShipCheckInCommand` içindeki güncelleme mantığına tekrar dönün. Şu anda iki alanı güncellemek için `setter` metodlarını çağırıyoruz. Sıradaki adım, bu mantığı `Starship` varlığı içinde özel bir metoda kapsüllemek olacak.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./13_Black Hole Deleting Entities.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./15_Quantum Refactor Rich Entities.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
