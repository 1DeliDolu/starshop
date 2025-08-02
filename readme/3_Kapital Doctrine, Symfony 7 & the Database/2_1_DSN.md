## DSN (Data Source Name) Nedir?

`DSN` (Data Source Name), bir uygulamanın bir veritabanına nasıl bağlanacağını tanımlayan özel bir bağlantı dizesidir. Symfony projelerinde, `.env` dosyasındaki `DATABASE_URL` değişkeni bir DSN içerir.

Bir DSN şu bilgileri içerir:

-   **Veritabanı türü:** (`mysql`, `postgresql`, `sqlite` vb.)
-   **Kullanıcı adı**
-   **Şifre**
-   **Sunucu adresi**
-   **Port**
-   **Veritabanı adı**
-   (Opsiyonel) **Ek parametreler**

Örnek bir DSN:

```
DATABASE_URL="postgresql://kullanici:sifre@localhost:5432/veritabani_adi"
```

Bu yapı sayesinde Doctrine, hangi veritabanına, hangi kullanıcıyla, hangi adreste ve hangi porttan bağlanacağını bilir. Ek olarak, bazı bağlantı ayarları da DSN sonuna eklenebilir.

Kısacası: **DSN, veritabanı bağlantı bilgilerinin tek satırda özetlenmiş halidir.**

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
    <a href="./2_Database Setup & Docker.md" title="Önceki" style="text-decoration: none; font-size: 1.2em;">⬅️ Önceki</a>
    <a href="../README.md" title="Ana Sayfa" style="text-decoration: none; font-size: 1.2em;">🏠 Ana Sayfa</a>
    <a href="./3_Starship Entity.md" title="Sonraki" style="text-decoration: none; font-size: 1.2em;">Sonraki ➡️</a>
</div>
