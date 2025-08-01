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
