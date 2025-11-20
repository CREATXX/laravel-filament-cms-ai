# Google Analytics Dashboard Widget'ları Kurulum Rehberi

## 1. Google Analytics Data API Kurulumu

### Composer Paketini Yükleyin
```bash
composer require google/analytics-data
```

## 2. Google Cloud Console Ayarları

### Service Account Oluşturma
1. [Google Cloud Console](https://console.cloud.google.com/) açın
2. Yeni bir proje oluşturun veya mevcut projeyi seçin
3. **APIs & Services > Library** bölümüne gidin
4. **Google Analytics Data API** aratın ve **Enable** edin
5. **APIs & Services > Credentials** bölümüne gidin
6. **Create Credentials > Service Account** seçin
7. Service account adı girin (örn: `cms-analytics-reader`)
8. **Create and Continue** tıklayın
9. Role olarak **Viewer** seçin
10. **Continue** ve **Done** tıklayın

### Service Account JSON Key Oluşturma
1. Oluşturduğunuz service account'a tıklayın
2. **Keys** sekmesine gidin
3. **Add Key > Create new key** seçin
4. **JSON** formatını seçin
5. İndirilen JSON dosyasını `storage/app/analytics/service-account-credentials.json` olarak kaydedin

### Google Analytics'e Erişim Verme
1. [Google Analytics](https://analytics.google.com/) açın
2. **Admin** (sol alt köşe) tıklayın
3. **Property** sütununda **Property Access Management** seçin
4. **Add users** butonuna tıklayın
5. Service account email adresinizi girin (örn: `cms-analytics-reader@PROJECT_ID.iam.gserviceaccount.com`)
6. Role olarak **Viewer** seçin
7. **Add** tıklayın

## 3. Laravel Konfigürasyonu

### .env Dosyasına Ekleyin
```env
GOOGLE_ANALYTICS_PROPERTY_ID=123456789
```

**Property ID nasıl bulunur:**
1. Google Analytics > Admin > Property Settings
2. Property Details sayfasında **PROPERTY ID** göreceksiniz (örn: 123456789)

### Storage Klasörü Oluşturun
```bash
mkdir storage/app/analytics
```

### Service Account JSON Dosyasını Yerleştirin
```
storage/
  app/
    analytics/
      service-account-credentials.json  <- Buraya
```

## 4. Analytics Service'i Kaydedin

`bootstrap/app.php` dosyasına ekleyin:
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        // ... mevcut providerlar
    ])
    ->withSingletons([
        \App\Services\AnalyticsService::class,
    ])
    // ...
```

## 5. Cache Temizleme

Dashboard'da yeni veriler görmek için cache'i temizleyin:
```bash
php artisan cache:clear
```

## 6. Test Etme

Filament Admin Panel'e giriş yapın:
```
http://localhost/admin
```

Dashboard'da görmelisiniz:
- **Aktif Kullanıcılar** (son 30 gün)
- **Oturumlar** (son 30 gün)
- **Sayfa Görüntülenme** (son 30 gün)
- **Ziyaretçi Grafiği** (30 günlük)
- **Gerçek Zamanlı Kullanıcılar** (şu anda sitede)
- **Trafik Kaynakları** (top 5)
- **Cihaz Dağılımı** (Desktop/Mobile/Tablet)
- **En Çok Ziyaret Edilen Sayfalar** (top 10)

## Sorun Giderme

### "Service account credentials file not found" Hatası
- `storage/app/analytics/service-account-credentials.json` dosyasının var olduğunu kontrol edin
- Dosya izinlerini kontrol edin: `chmod 644 storage/app/analytics/service-account-credentials.json`

### "Permission denied" Hatası
- Service account'a Google Analytics'te **Viewer** rolü verildiğinden emin olun
- Property ID'nin doğru olduğunu kontrol edin

### "API not enabled" Hatası
- Google Cloud Console'da **Google Analytics Data API**'nin aktif olduğunu kontrol edin

### Widget'lar Görünmüyor
- `php artisan cache:clear` komutunu çalıştırın
- `app/Providers/Filament/AdminPanelProvider.php` dosyasında widget'ların kayıtlı olduğunu kontrol edin

## Özellikler

✅ Google Analytics Data API v1beta entegrasyonu
✅ Gerçek zamanlı kullanıcı takibi
✅ 30 günlük visitor ve pageview grafikleri
✅ Trafik kaynaklarını analizi
✅ Cihaz dağılımı (Desktop/Mobile/Tablet)
✅ En popüler sayfalar listesi
✅ 24 saatlik cache mekanizması
✅ Exception handling ve log kaydı
✅ Filament dashboard widget'ları
