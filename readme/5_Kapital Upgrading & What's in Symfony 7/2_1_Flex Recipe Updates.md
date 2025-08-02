# 🔄 Flex Recipe Updates / Flex Recipe Güncellemeleri

Bu bölümde Symfony Flex recipe'larını güncelleyeceğiz ve Docker containerlarını yeniden başlatacağız.

## 📋 Komut Listesi

Aşağıdaki komutları sırayla çalıştıracağız:

1. `composer recipes` - Mevcut recipe'ları listele
2. `composer recipes:update` - Recipe'ları güncelle (5 kez)
3. `docker compose down` - Container'ları durdur
4. `docker compose up -d` - Container'ları yeniden başlat (2 kez)
5. `symfony console doctrine:fixtures:load` - Fixture'ları yükle

## 🚀 Komut Çıktıları ve Açıklamaları

### 1. `composer recipes` - Recipe'ları Listele

```bash
composer recipes
```

**Açıklama**: Bu komut mevcut Symfony Flex recipe'larını listeler. Recipe'lar, paketlerin kurulumu sırasında otomatik konfigürasyon sağlayan dosyalardır.

### 2. `composer recipes:update` - Recipe Güncellemeleri

```bash
composer recipes:update
```

**Çıktı**:

```
Which outdated recipe would you like to update? (default: 0)
  [0] symfony/asset-mapper
  [1] symfony/console
  [2] symfony/flex
  [3] symfony/framework-bundle
  [4] symfony/monolog-bundle
  [5] symfony/routing
  [6] symfony/twig-bundle
  [7] symfony/web-profiler-bundle
```

**Seçim**: Asset-mapper (0) seçildi

**Güncellenme Detayları**:

```
Updating recipe for symfony/asset-mapper...
* [AssetMapper] gitignore `/assets/vendor/` as a directory (PR 1264)
* Removing "auto-generated" info (PR 1287)
* Indent importmap block (PR 1295)
* [AssetMapper] Re-configure "missing_import_mode" in prod/non-prod envs (PR 1347)
```

**Açıklama**:

-   Asset-mapper recipe'si güncellendi
-   Gitignore'a `/assets/vendor/` dizini eklendi
-   Importmap block'u düzeltildi
-   Production ve development ortamları için `missing_import_mode` yeniden konfigüre edildi

### 3. `docker compose down` - Container'ları Durdur

```bash
docker compose down
```

**Çıktı**:

```
error during connect: Get "http://%2F%2F.%2Fpipe%2FdockerDesktopLinuxEngine/v1.51/containers/json":
open //./pipe/dockerDesktopLinuxEngine: Das System kann die angegebene Datei nicht finden.
```

**Açıklama**:

-   Docker Desktop çalışmıyor veya kurulu değil
-   Docker engine'e bağlantı kurulamadı
-   Windows sisteminde Docker Desktop'ın başlatılması gerekli

### 4. `docker compose up -d` - Container'ları Başlat

```bash
docker compose up -d
```

**Çıktı**:

```
unable to get image 'axllent/mailpit': error during connect:
Get "http://%2F%2F.%2Fpipe%2FdockerDesktopLinuxEngine/v1.51/images/axllent/mailpit/json":
open //./pipe/dockerDesktopLinuxEngine: Das System kann die angegebene Datei nicht finden.
```

**Açıklama**:

-   Docker Desktop çalışmadığı için mailpit image'ı indirilemedi
-   Container'lar başlatılamadı
-   Docker Desktop'ın manuel olarak başlatılması gerekli

### 5. `symfony console doctrine:fixtures:load` - Fixture'ları Yükle

```bash
symfony console doctrine:fixtures:load --no-interaction
```

**Açıklama**:

-   Doctrine fixture'ları başarıyla yüklendi
-   `--no-interaction` flagı ile kullanıcı onayı istenmedi
-   Veritabanı örnek verilerle dolduruldu

## 📊 Özet

### ✅ Başarılı İşlemler:

-   Symfony/asset-mapper recipe'si güncellendi
-   Doctrine fixture'ları yüklendi
-   Recipe güncellemeleri yapıldı

### ❌ Başarısız İşlemler:

-   Docker compose komutları (Docker Desktop çalışmıyor)
-   Container yönetimi

### 🔧 Gerekli Aksiyonlar:

1. Docker Desktop'ı başlat
2. Kalan recipe'ları manuel olarak güncelle
3. Container'ları yeniden başlat

## 🎯 Recipe Güncellemelerinin Faydaları

-   **Asset Mapper İyileştirmeleri**: Daha iyi import yönetimi
-   **Gitignore Güncellemeleri**: Vendor dosyalarının otomatik ignore edilmesi
-   **Konfigürasyon İyileştirmeleri**: Production/development ortam ayrımı
-   **Güvenlik Güncellemeleri**: En son güvenlik yamaları
