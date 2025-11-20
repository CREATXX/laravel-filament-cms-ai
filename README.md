# Laravel Filament CMS AI 🚀

## Proje Tanımı

Bu proje, **Laravel 11 + Filament v3** tabanlı, OpenAI destekli dinamik bir CMS ve Page Builder sistemidir. Mevcut HTML şablonlarını blok tabanlı yapıya dönüştürerek, yapay zeka destekli içerik üretimi ve SEO optimizasyonu sunar.

## 🎯 Temel Özellikler

### 1. Dinamik Sayfa Oluşturucu (Page Builder)
- HTML bölümlerini (Hero, Features, Contact vb.) Filament Block'larına dönüştürme
- Sürükle-bırak ile sayfa tasarlama
- JSON tabanlı içerik yönetimi

### 2. AI Destekli İçerik Üretimi
- OpenAI entegrasyonu ile otomatik blog yazısı oluşturma
- Tek tuşla SEO uyumlu içerik üretimi
- Akıllı özet (excerpt) oluşturma

### 3. AI SEO Optimizasyonu
- İçerik analizi ve puanlama (1-100)
- Otomatik anahtar kelime önerileri
- Meta açıklama optimizasyonu

### 4. Google API Entegrasyonu
- Search Console verileri
- Analytics istatistikleri
- Google Places yorumları

## 🛠️ Teknoloji Yığını

- **Backend:** Laravel 11.x
- **Admin Panel:** FilamentPHP v3
- **Veritabanı:** MySQL
- **Frontend:** Blade Template
- **AI:** OpenAI PHP Client
- **Dil:** Türkçe arayüz, İngilizce kod yapısı

## 📋 Kurulum

### Gereksinimler

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM

### Adım 1: Projeyi İndirin

```bash
git clone https://github.com/CREATXX/laravel-filament-cms-ai.git
cd laravel-filament-cms-ai
```

### Adım 2: Bağımlılıkları Yükleyin

```bash
composer install
npm install
```

### Adım 3: Ortam Değişkenlerini Ayarlayın

```bash
cp .env.example .env
php artisan key:generate
```

`.env` dosyasında aşağıdaki değerleri yapılandırın:

```env
DB_DATABASE=laravel_cms
DB_USERNAME=root
DB_PASSWORD=

OPENAI_API_KEY=sk-...

GOOGLE_ANALYTICS_VIEW_ID=...
GOOGLE_SERVICE_ACCOUNT_CREDENTIALS_JSON=...
```

### Adım 4: Veritabanını Hazırlayın

```bash
php artisan migrate --seed
```

### Adım 5: Depolama Bağlantısı

```bash
php artisan storage:link
```

### Adım 6: Admin Kullanıcısı Oluşturun

```bash
php artisan make:filament-user
```

### Adım 7: Uygulamayı Çalıştırın

```bash
php artisan serve
npm run dev
```

Admin paneline erişim: `http://localhost:8000/admin`

## 📚 Veritabanı Yapısı

### Pages (Dinamik Sayfalar)
- `title` - Sayfa başlığı
- `slug` - URL dostu slug
- `content` - JSON builder blokları
- `seo_title` - SEO başlığı
- `seo_description` - Meta açıklama
- `is_published` - Yayın durumu

### Posts (Blog Yazıları)
- `title` - Yazı başlığı
- `slug` - URL slug
- `content` - Rich editor içeriği
- `excerpt` - AI ile oluşturulan özet
- `featured_image` - Öne çıkan görsel
- `seo_score` - AI SEO puanı (1-100)
- `ai_keywords` - Otomatik anahtar kelimeler (JSON)

### Settings (Genel Ayarlar)
- Google API anahtarları
- OpenAI API anahtarı
- Site logosu ve iletişim bilgileri

## 🚀 Geliştirme Planı

- [x] Step 1: GitHub deposu oluşturma
- [ ] Step 2: Laravel ve Filament kurulumu
- [ ] Step 3: Veritabanı migration'ları
- [ ] Step 4: Filament Block yapısı
- [ ] Step 5: AI servis entegrasyonu
- [ ] Step 6: Page ve Post kaynakları
- [ ] Step 7: Frontend render sistemi
- [ ] Step 8: Google API widgetları

## 🎨 Filament Block Yapısı

Her HTML section ayrı bir block olarak tanımlanacak:

```php
// app/Filament/Blocks/HeroBlock.php
Block::make('hero')
    ->schema([
        TextInput::make('title')->label('Başlık'),
        Textarea::make('subtitle')->label('Alt Başlık'),
        FileUpload::make('image')->label('Görsel'),
    ])
```

## 🤖 AI Kullanımı

### İçerik Oluşturma

```php
// Admin panelde "AI ile Yaz" butonuna tıklayın
Action::make('generateContent')
    ->label('AI ile Yaz')
    ->action(fn (Post $record) => 
        AiContentService::generateBlog($record->title)
    )
```

### SEO Analizi

```php
// Otomatik SEO puanlama
Action::make('analyzeSeo')
    ->label('SEO Analizi Yap')
    ->action(fn (Post $record) => 
        AiContentService::analyzeSeo($record)
    )
```

## 📊 Google Entegrasyonu

Admin dashboard'da görüntülenecek widgetlar:

- Ziyaretçi istatistikleri (Analytics)
- Popüler sayfalar
- Arama terimleri (Search Console)
- Google yorumları (Places API)

## 🔒 Güvenlik

- API anahtarları `.env` dosyasında saklanır
- `.gitignore` ile hassas dosyalar korunur
- Filament'in yerleşik authentication sistemi

## 📝 Kod Standartları

- **Clean Code** ve **DRY** prensipleri
- İş mantığı `Services` klasöründe
- Controller'lar yalın tutulur
- %100 Türkçe arayüz ve yorumlar
- İngilizce değişken ve fonksiyon isimleri

## 🤝 Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'feat: Harika özellik eklendi'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request açın

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## 👨‍💻 Geliştirici

CREATXX - [GitHub](https://github.com/CREATXX)

## 🙏 Teşekkürler

- Laravel ekibine
- FilamentPHP topluluğuna
- OpenAI'ya

---

**Not:** Bu proje aktif geliştirme aşamasındadır. Önerilerinizi issue olarak paylaşabilirsiniz.
