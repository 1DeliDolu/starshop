# 🤖 Joining Across a Many-to-Many Relationship / Çoktan Çoğa İlişki Üzerinden Birleştirme

Filo içindeki hangi yıldız gemisinin en fazla `droid` ile dolu olduğunu hiç merak ettin mi? Ben de! Haydi, her bir gemiyi sahip oldukları `droid` sayısına göre artan şekilde listeleyelim.

`src/Controller/MainController.php` dosyasına geç. Sorgu şöyle: `$ships = $repository->findIncomplete();`.

O metoda tıkla ve ona yeni, havalı bir isim ver: `findIncompleteOrderedByDroidCount()`:

---


```php
// src/Repository/StarshipRepository.php
// ... lines 1 - 14
class StarshipRepository extends ServiceEntityRepository
{
// ... lines 17 - 24
    public function findIncompleteOrderedByDroidCount(): Pagerfanta
    {
// ... lines 27 - 34
    }
// ... lines 36 - 65
}
```

👉 Bu, `findIncompleteOrderedByDroidCount()` adlı yeni metodu ekleyeceğimiz yerdir.

---

Bunu yaptıktan sonra, kontrolcüye geri dön ve eski metodu yenisiyle değiştir:

---


```php
// src/Controller/MainController.php
// ... lines 1 - 10
class MainController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(
// ... lines 15 - 16
    ): Response {
        $ships = $repository->findIncompleteOrderedByDroidCount();
// ... lines 19 - 27
    }
}
```

👉 Bu kodda, artık yeni metodu çağırıyoruz.

---

Şu ana kadar henüz bir değişiklik yapmadık, bu yüzden sayfayı yenilediğinde aynı şeyleri görürsün.

`starship`leri `droid` sayılarına göre sıralamak için, birleştirme (join) işlemiyle önce ilişki tablosuna, ardından da `droid` tablosuna ulaşmamız gerekiyor. Kulağa karmaşık gelse de aslında gayet güzel!

`StarshipRepository` içinde bir `leftJoin()` ekle. Ancak ilişki tablosunu ya da veritabanını düşünmene gerek yok. Sadece Doctrine içindeki ilişkilere odaklan. Yani, burada `s` (starship) ve `droids` (ManyToMany ilişkisi olan property) üzerinden gidiyoruz ve `droids` için `droid` takma adını (alias) veriyoruz.

`droid`leri saymak için `groupBy('s.id')` ekle.

Sıralama için mevcut `orderBy()` ifadesini `orderBy('COUNT(droid)', 'ASC')` ile değiştir:

---


```php
// src/Repository/StarshipRepository.php
// ... lines 1 - 14
class StarshipRepository extends ServiceEntityRepository
{
// ... lines 17 - 24
    public function findIncompleteOrderedByDroidCount(): Pagerfanta
    {
        $query = $this->createQueryBuilder('s')
// ... line 28
            ->orderBy('COUNT(droid)', 'ASC')
            ->leftJoin('s.droids', 'droid')
            ->groupBy('s.id')
// ... lines 32 - 33
        ;
// ... lines 35 - 36
    }
// ... lines 38 - 67
}
```

👉 Burada, `droid`lerle birleştirip (`leftJoin`), gruplayıp (`groupBy`) ve sayıya göre sıralıyoruz (`orderBy('COUNT(droid)', 'ASC')`).

---

Bundan sonra sayfayı yenile ve işte! En üstte `droid` olmayan gemiler göreceksin. Aşağıya indikçe, `droid` sayısı artar. İleride birkaç sayfa daha gidersen, iki, üç, hatta dört `droid`i olan `starship`ler bile göreceksin!

Buradaki anahtar nokta şu: Bu birleştirmenin özel bir yanı yok. İlişki üzerinden birleştiriyoruz, gerisini Doctrine hallediyor.

Sayfadaki sorguya bakarsan, tüm detayları nasıl ele aldığını görebilirsin. Sorguda `starship_droid` ifadesini arayabilirsin. Bu kısım karmaşık görünebilir, ama sorguyu formatlarsan aslında `starship`ten başlıyor, ilişki tablosuna, sonra da tekrar `droid` tablosuna geçiyor. Böylece `droid` tablosundaki sayıya göre sıralama yapılabiliyor. Doctrine gerçekten etkileyici!

Teknik olarak `ManyToMany` kısmı bu kadar! Ama sırada daha gelişmiş, ama yaygın bir kullanım var: ilişki (join) tablosuna veri eklemek, örneğin bir `droid`in bir `starship`e katıldığı tarih gibi.
