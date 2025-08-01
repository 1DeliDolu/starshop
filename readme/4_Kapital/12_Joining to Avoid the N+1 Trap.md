# 🏗️ Joining to Avoid the N+1 Trap / N+1 Tuzagından Kaçınmak için Join Kullanmak

Bir `parts` tablomuz var ve artık onu kullanıyoruz! Ama şimdi parçaları, fiyatına göre azalan şekilde sıralamak istiyoruz; çünkü satış yapacaksak, en pahalı olanlardan başlamak iyi olur, değil mi? Bu basit bir işlem, ama bunu biraz daha heyecanlı hale getirmek için özel bir sorgu oluşturacağız. `src/Repository/StarshipPartRepository.php` dosyasını açın.

O hazırdaki method gövdesini görüyor musunuz? Onu kopyalayın, ardından yorumu kaldırın; çünkü bu PHP dokümantasyonu faydalı ve kaybetmek istemeyiz. Son stubu silin ve adını `findAllOrderedByPrice()` yapın. `$value` parametresini kaldırın, buna ihtiyacımız yok:

````
src/Repository/StarshipPartRepository.php
```php
// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 40
    public function findAllOrderedByPrice(): array
    {
// ... lines 43 - 46
    }
}
````

👉 Bu kod, yeni bir `findAllOrderedByPrice` fonksiyonunun iskeletini oluşturur.

Basit bir sorgu oluşturun: `StarshipPart` için takma ad olarak `sp` kullanacağım. Aşağıdaki `andWhere()` ve `setParameter()` fonksiyonlarını kaldırın. Ama `orderBy()` fonksiyonuna ihtiyacımız var: `orderBy('sp.price', 'DESC')` olarak. `setMaxResults()` da kaldırılabilir:

```php
//src/Repository/StarshipPartRepository.php

// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 40
    public function findAllOrderedByPrice(): array
    {
        return $this->createQueryBuilder('sp')
            ->orderBy('sp.price', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
```

👉 Bu kod, parçaları fiyatına göre azalan şekilde getirir.

Özel sorgu hazır! Method adını kopyalayın, sonra `PartController` dosyasına gidin. Bunu, `findAll()` yerine kullanın:

```php
// src/Controller/PartController.php

// ... lines 1 - 9
final class PartController extends AbstractController
{
    #[Route('/parts', name: 'app_part_index')]
    public function index(StarshipPartRepository $repository): Response
    {
        $parts = $repository->findAllOrderedByPrice();
// ... lines 16 - 19
    }
}
```

👉 Bu kod, kontrolcüde parçaları fiyatına göre sıralı şekilde çeker.

## 🔎 Examining Our Queries / Sorgularımızı İncelemek

Bu sayfa için sorguları kontrol edin: 9 tane sorgu var. İlki tam tahmin ettiğimiz gibi: tüm `starship_parts` verilerini fiyatına göre azalan şekilde sorguluyor. Ama diğer bu ek sorgular ne? Her bir yıldız gemisi için ekstra bir sorgu var. Neler oluyor?

## 🕵️‍♂️ The N + 1 Problem / N + 1 Problemi

Tüm parçaları sorguluyoruz, sonra şablonda parçaların üzerinde döngü kurarken `part.starship` kullandığımızda, Doctrine'nin aklına bir fikir geliyor. Parça verisine sahip ama bu parçaya ait `Starship` verisine sahip değil. Onu sorguluyor. Sonuç olarak bir sorgu parça için, her bir `Starship` için de ekstra sorgu oluşuyor. Bu, kötü ünlü N + 1 problemi.

Şöyle düşünün: 10 parçamız varsa, parçalar için bir sorgu, her bir parça için de `Starship` verisini almak için toplamda 10 ek sorgu yapıyoruz. Bu bir performans sorunudur. Belki şu an önemli gözükmeyebilir, ama dikkat etmemiz gereken bir durum. Ve bunu bir `join` ile çözebiliriz.

## 🔗 Joining Across the Relationship / İlişki Üzerinden Join Yapmak

Tekrar `StarshipPartRepository` dosyasına dönelim ve `findAllOrderedByPrice()` fonksiyonunu bir join ile güçlendirelim. `innerJoin('sp.starship', 's')` ekleyin. Tek yapmamız gereken, property üzerinden join yapmak. Doctrine hangi kolonların join yapılacağını otomatik belirler. Böylece `starship` tablosunu `s` takma adıyla bağlamış oluyoruz:

```php
// src/Repository/StarshipPartRepository.php

// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 40
    public function findAllOrderedByPrice(): array
    {
        return $this->createQueryBuilder('sp')
// ... line 44
            ->innerJoin('sp.starship', 's')
// ... lines 46 - 47
    }
}
```

👉 Bu kod, parçalara ait yıldız gemilerini de join ile dahil eder.

Daha önce 9 veritabanı sorgumuz vardı. Sayfayı yenileyin ve... hala 9 sorgu var. Neden? Zaten `starship` tablosuna join eklemedik mi? Evet, ama join kullanmanın iki sebebi var. Birincisi bu N + 1 sorununu önlemek, ikincisi ise join yapılan tabloda `where()` veya `orderBy()` uygulamaktır. İkinci sebebi birazdan göreceğiz.

## ➕ addSelect ile N+1 Çözümü / addSelect ile N+1 Problemine Çözüm

N+1 sorununu çözmek için, join'e ek olarak, `Starship` verisini de seçmemiz gerekir. Bunun için tek yapmamız gereken `addSelect('s')` eklemek:

```php
// src/Repository/StarshipPartRepository.php

// ... lines 1 - 13
class StarshipPartRepository extends ServiceEntityRepository
{
// ... lines 16 - 40
    public function findAllOrderedByPrice(): array
    {
        return $this->createQueryBuilder('sp')
// ... line 44
            ->innerJoin('sp.starship', 's')
            ->addSelect('s')
// ... lines 47 - 48
    }
}
```

👉 Bu kod, hem parçaları hem de ilişkili yıldız gemisi verisini tek sorguda getirir.

`Starship` tablosunun tamamını `s` takma adıyla seçiyoruz. `addSelect()` ile tek tek kolonlarla uğraşmıyoruz. Sadece şöyle diyoruz:

Tüm verileri istiyorum.

## ✨ The Magic of join and addSelect() / join ve addSelect() Büyüsü

Artık 9 sorgudan 1 sorguya düştük. Gerçekten sihirli bir çözüm. Gördüğünüz gibi, `StarshipPart` tablosundan seçiyoruz, hem `Starship` hem de `StarshipPart` verilerini alıyoruz ve ortada güzel bir `innerJoin()` var. En güzel yanı ise, hangi kolonların birleştirileceğiyle uğraşmak zorunda olmamamız. Sadece ilişki property’sini belirtiyoruz, gerisini Doctrine hallediyor.

Sırada sayfamıza arama eklemek var. O zaman `JOIN` kullanımının ikinci sebebini göreceğiz ve son olarak `Request` objesiyle de oynayacağız.
