# Symfony Projesinde MySQL Ayarları

## 1. Docker Compose ile MySQL Servisi

`compose.yaml` dosyanızda aşağıdaki gibi bir MySQL servisi tanımlı olmalı:

```yaml
services:
    database:
        image: mysql:8.0
        environment:
            MYSQL_DATABASE: ${MYSQL_DATABASE:-starship}
            MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD:-root}
            MYSQL_USER: ${MYSQL_USER:-app}
            MYSQL_PASSWORD: ${MYSQL_PASSWORD:-!ChangeMe!}
        ports:
            - "3306:3306"
        healthcheck:
            test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
            timeout: 5s
            retries: 5
            start_period: 60s
        volumes:
            - database_data:/var/lib/mysql:rw
```

## 2. .env Dosyası Ayarı

`.env` dosyanızda MySQL bağlantısı için aşağıdaki satır olmalı:

```
DATABASE_URL="mysql://root@127.0.0.1:3306/starship?serverVersion=8.0.32&charset=utf8mb4"
```

> Kullanıcı adı, şifre ve veritabanı adını kendi ortamınıza göre değiştirebilirsiniz.

## 3. Migration ve Şema Güncelleme

Migration dosyalarınızda MySQL uyumlu SQL ifadeleri olmalı. Örneğin:

-   `SERIAL` yerine `INT AUTO_INCREMENT`
-   `TIMESTAMP(0) WITHOUT TIME ZONE` yerine `DATETIME`
-   `COMMENT ON COLUMN ...` satırları MySQL'de gereksizdir.

## 4. Komutlar

-   Servisleri başlatmak için:
    ```sh
    docker compose up -d
    ```
-   Migration çalıştırmak için:
    ```sh
    symfony console doctrine:migrations:migrate
    ```
-   Veritabanı sorgulamak için (Windows):
    ```sh
    symfony console doctrine:query:sql "select * from starship"
    ```

## 5. Notlar

-   Migration dosyalarınızın MySQL ile uyumlu olduğundan emin olun.
-   Gerekirse veritabanını elle oluşturabilirsiniz.
-   `.env` ve `compose.yaml` dosyalarındaki bilgiler birbiriyle uyumlu olmalı.
