# 🚀 More about Services / Servisler Hakkında Daha Fazlası

Servislerin paketlerden (`bundles`) geldiğini zaten biliyoruz. Ve her servis, bir `ID`, bir `class` ve onu oluşturmak için gereken bir dizi `argument` kombinasyonundan oluşur. Ama kodumuzu daha iyi sürdürülebilirlik için düzenlemek amacıyla kendi servislerimizi de oluşturabileceğimizi biliyor muydunuz? Evet! İnanması güç, ama önceki bölümde zaten bir tane oluşturmuştuk.

`StarshipRepository.php` dosyasını açın. Bunu herhangi bir yapılandırma (`configuration`) olmadan oluşturduk ve hâlâ `StarshipApiController.php` içinde kullanabiliyoruz. Peki bunu nasıl yapabiliyoruz? Bu, `config/services.yaml` sayesinde çalışıyor. Şimdi onu açalım. Aşağıda, `services` anahtarımızın (`key`) altında, bu `App\` bölümünü görüyoruz. Bu kod, `src/` dizinimizdeki her şeyi bir servis olarak kaydeder (`register`). Ama aynı zamanda bazı şeyleri hariç tutar (`exclude`), örneğin `DependencyInjection`, `Entity` ve `Kernel.php`.

## 📄 config/services.yaml

```
services:
    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'
```

👉 Bu yapılandırma, `src/` dizinindeki tüm sınıfları servis olarak kaydeder, ancak belirtilen dizinleri ve dosyaları hariç tutar.

Bu `services.yaml` dosyası, bu yapılandırma dahil olmak üzere, `symfony/framework-bundle` çekirdeğiyle birlikte gelir.

Yukarıda, `_defaults` anahtarımız var.

## 📄 config/services.yaml

```
services:
    _defaults:
        autowire: true      # Automatically injects dependencies in your services.
        autoconfigure: true # Automatically registers your services as commands, event subscribers, etc.
```

👉 Bu ayar, tüm servisler için varsayılan olarak otomatik bağımlılık enjeksiyonu (`autowire`) ve otomatik yapılandırma (`autoconfigure`) sağlar.

Bu dosyadaki tüm servislerin yapılandırması budur. `autowire` anahtarı `true` olarak ayarlandığında, bağımlılıklar servislerinize otomatik olarak enjekte edilir. Ayrıca, `autoconfigure` anahtarı da `true` olarak ayarlanmıştır ve servislerinizi komut, event subscriber gibi olarak otomatik kaydeder. Oldukça güzel! `autoconfigure` hakkında daha sonra daha fazla konuşacağız.

Bir servis listesini görmek için terminalinizde şunu çalıştırın:

```
php bin/console debug:autowiring
```

👉 Bu komut, mevcut autowiring yapılandırmasını gösterir.

Ama bu sefer, sonuna `--all` seçeneğini ekleyelim:

```
php bin/console debug:autowiring --all
```

👉 Bu komut, takma adı (`aliased`) olmayanlar da dahil olmak üzere tüm servisleri gösterir.

Bu, takma adı olmayan servisler de dahil olmak üzere tüm servislerimizi gösterecek. Teknik olarak, Model sınıfımız gibi servis olmayanlar da servis olarak kaydedilir, fakat kodumuzda kullanmadığımız için sonradan kaldırılırlar. Önemli olan, bir servis oluşturmak için tek yapmamız gerekenin `src/` dizinimizde bir sınıf (`class`) oluşturmak ve onun için autowiring'in otomatik olarak etkinleştiğidir.

Bu arada, tüm bu `.yaml` dosyaları aslında aynıdır. `services` veya `framework` gibi kök anahtar (`root key`), onları farklı kılar. Yani, tüm dosyalardaki yapılandırmayı tek bir `.yaml` dosyasına kopyalayabilir ve yine de aynı şekilde çalışmasını sağlayabilirsiniz. Biz bunları sürdürülebilirlik ve sağduyu (`maintainability and sanity`) için ayrı tutuyoruz.

Sıradaki konu: Size defalarca söylediğim gibi, container servisleri tutar, bu doğru. Ama ayrıca bir başka şeyi de tutar: Basit bir yapılandırma olan `parameters`.

---

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./8_The Prod Environment.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./10_Parameters.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
