# 🧪 Profiling Commands / Komutları Profilleme

Sitemizdeki `dev` ortamında, web debug araç çubuğunu elde ediyoruz. Ve daha da önemlisi, bir sürü faydalı özellikle dolu olan profiler. Uygulamamız tamamen bir API bile olsa, doğrudan `/_profiler` adresine giderek herhangi bir API isteği için profiler'ı kontrol edebiliriz.

Bu, Symfony'nin en önemli özelliklerinden biridir. Symfony 6.4’te, katkıcı Jules Pietri şöyle düşündü: Neden bunu konsol komutları için de kullanamıyoruz?

Artık kullanabiliyoruz! Bu, büyük veya karmaşık olabilecek özel konsol komutlarınız için kullanılması amaçlanmış olsa da, temel komutlarla da kullanabiliriz.

## 🏁 Triggering a Profile: --profile / Profil Başlatma: --profile

Şimdi şunu çalıştırın:

```bash
php bin/console debug:container
```

👉 Bu komutu normal şekilde çalıştırırsanız, profiler sistemi etkinleşmez ve bilgi toplanmaz.

Bunu tetiklemek için, komutu `--profile` ile çalıştırmanız gerekir.

```bash
php bin/console debug:container --profile
```

👉 Burada görünürde bir değişiklik yok, ancak aslında profiler etkinleşti... bilgi topladı ve bir yere kaydetti... Ama... bu bilgiyi nerede görebileceğimiz açık değil!

Aslında yapmak istediğiniz şey, `-v` parametresini eklemek:

```bash
php bin/console debug:container --profile -v
```

👉 Şimdi, en altta profiler URL’sinde kullanılabilecek benzersiz bir token yer alıyor. Ama daha da pratik olmak için `-vvv` ile çalıştırın:

```bash
php bin/console debug:container --profile -vvv
```

👉 Bu sefer bir bağlantı ve hatta bellek ile zamanla ilgili detaylar da alıyoruz. Bağlantıya tıklayınca… çalışmıyor. Neredeyse doğru bir URL, fakat terminalim yerel web sunucumun hangi portu kullandığını bilmiyor. O token’ı kopyalayın ve... herhangi bir isteğin profiler’ına gidip URL’ye token’ı yapıştırın... harika!

## 🕵️ Exploring the Profiler / Profiler’ı İncelemek

Komutla ilgili bilgileri, girdiyi, çıktıyı... ve en önemlisi, normal profiler bölümlerini görüyoruz! İlginç bir bölüm ise `events`: burada tetiklenen gerçek olaylar ve her biri için dinleyiciler gösteriliyor. Bunlar, bir istek sırasında tetiklenen olaylardan tamamen farklıdır; bunu görmek güzel.

Muhtemelen çoğu profiler bölümünün gri renkte olduğunu fark ettiniz. Ama bir Twig şablonu render ederseniz, bir HTTP isteği yaparsanız veya bir veritabanı sorgusu yaparsanız, bu bölümler etkinleşir.

Bu basit komutla bile, performans bölümünün kilidini açıyoruz. Bu örnekte burada çok fazla bilgi yok, ama yine de tehlikeli hissettiriyor.

İşte bu kadar! Yine, havalı ve iyi düşünülmüş bir özellik. İnsanların bunu nasıl kullanacağını görmek isterim.

Şimdi son konumuza geçelim: Symfony'nin en iyi yeni bileşenlerinden biri olan Scheduler’ı deneyeceğiz.
