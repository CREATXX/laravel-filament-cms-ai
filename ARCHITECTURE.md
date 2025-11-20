# Proje Mimarisi ve Geliştirme Kuralları

## 1. Genel Bakış

Bu doküman, Laravel 11 + Filament v3 tabanlı CMS projesinin mimari yapısını ve geliştirme kurallarını detaylandırır.

## 2. Klasör Yapısı

```
laravel-filament-cms-ai/
├── app/
│   ├── Filament/
│   │   ├── Blocks/          # Page Builder blokları
│   │   │   ├── HeroBlock.php
│   │   │   ├── TextBlock.php
│   │   │   ├── FeaturesBlock.php
│   │   │   └── ContactBlock.php
│   │   ├── Resources/       # Filament CRUD kaynakları
│   │   │   ├── PageResource.php
│   │   │   ├── PostResource.php
│   │   │   └── SettingResource.php
│   │   └── Widgets/         # Dashboard widgetları
│   │       ├── AnalyticsWidget.php
│   │       └── ReviewsWidget.php
│   ├── Models/
│   │   ├── Page.php
│   │   ├── Post.php
│   │   └── Setting.php
│   ├── Services/            # İş mantığı katmanı
│   │   ├── AiContentService.php
│   │   ├── AiSeoService.php
│   │   └── GoogleApiService.php
│   └── Http/
│       └── Controllers/
│           └── PageController.php
├── database/
│   ├── migrations/
│   │   ├── create_pages_table.php
│   │   ├── create_posts_table.php
│   │   └── create_settings_table.php
│   └── seeders/
├── resources/
│   └── views/
│       ├── pages/
│       │   └── show.blade.php
│       └── blocks/          # Block render şablonları
│           ├── hero.blade.php
│           ├── text.blade.php
│           └── features.blade.php
└── routes/
    └── web.php
```

## 3. Katmanlı Mimari

### A. Presentation Layer (Sunum Katmanı)
- **Filament Resources:** Admin paneli CRUD işlemleri
- **Blade Views:** Frontend görünümleri
- **Widgets:** Dashboard göstergeleri

### B. Business Logic Layer (İş Mantığı Katmanı)
- **Services:** Tüm iş mantığı burada
  - `AiContentService`: OpenAI entegrasyonu
  - `AiSeoService`: SEO analiz ve optimizasyon
  - `GoogleApiService`: Google API'leri

### C. Data Layer (Veri Katmanı)
- **Models:** Eloquent ORM modelleri
- **Migrations:** Veritabanı şemaları
- **Repositories:** (Opsiyonel) Veri erişim katmanı

## 4. Kod Standartları

### A. Naming Conventions (İsimlendirme Kuralları)

```php
// Sınıflar: PascalCase
class AiContentService {}

// Metodlar: camelCase
public function generateBlog() {}

// Değişkenler: camelCase
$blogContent = '';

// Sabitler: UPPER_SNAKE_CASE
const MAX_TOKENS = 2000;

// Veritabanı tabloları: snake_case, çoğul
'pages', 'posts', 'settings'

// Veritabanı sütunları: snake_case
'seo_title', 'is_published', 'featured_image'
```

### B. Filament Component Standartları

```php
// Form alanları her zaman Türkçe etiketli
TextInput::make('title')
    ->label('Başlık')
    ->required()
    ->maxLength(255);

// Placeholder'lar Türkçe
Textarea::make('description')
    ->label('Açıklama')
    ->placeholder('Kısa bir açıklama yazın...');

// Bildirimler Türkçe
Notification::make()
    ->title('Başarılı!')
    ->body('İçerik başarıyla oluşturuldu.')
    ->success()
    ->send();
```

### C. Service Pattern

```php
// ❌ YANLIŞ: Controller'da iş mantığı
public function store(Request $request)
{
    $client = new OpenAI\Client(config('openai.api_key'));
    $response = $client->chat()->create([...]);
    // ...
}

// ✅ DOĞRU: Service kullanımı
public function store(Request $request)
{
    $content = AiContentService::generateBlog($request->title);
    Post::create(['content' => $content]);
}
```

## 5. Filament Builder Bloklarının Yapısı

### Block Tanımlama

```php
use Filament\Forms\Components\Builder\Block;

Block::make('hero')
    ->label('Hero Bölümü')
    ->schema([
        TextInput::make('title')
            ->label('Başlık')
            ->required(),
        Textarea::make('subtitle')
            ->label('Alt Başlık'),
        FileUpload::make('background')
            ->label('Arka Plan Görseli')
            ->image(),
    ])
```

### Frontend Render

```blade
@foreach($page->content as $block)
    @include("blocks.{$block['type']}", ['data' => $block['data']])
@endforeach
```

## 6. AI Servis Mimarisi

### A. İçerik Üretimi

```php
class AiContentService
{
    protected OpenAI\Client $client;

    public function generateBlog(string $topic, int $maxTokens = 2000): string
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sen uzman bir SEO uyumlu içerik yazarısın. Türkçe blog yazıları yazıyorsun.'
                ],
                [
                    'role' => 'user',
                    'content' => "Şu konu hakkında SEO uyumlu bir blog yazısı yaz: {$topic}"
                ]
            ],
            'max_tokens' => $maxTokens,
        ]);

        return $response['choices'][0]['message']['content'];
    }
}
```

### B. SEO Analizi

```php
public function analyzeSeo(Post $post): array
{
    $prompt = """
    İçerik: {$post->content}
    Başlık: {$post->title}
    Meta: {$post->seo_description}
    
    Bu içeriği SEO açısından analiz et ve:
    1. 1-100 arası puan ver
    2. İyileştirme önerileri sun (Türkçe)
    3. Anahtar kelime öner
    
    JSON formatında döndür.
    """;

    // OpenAI çağrısı...
    
    return [
        'score' => 85,
        'suggestions' => ['Başlık daha kısa olabilir', 'İç linkler eklenebilir'],
        'keywords' => ['laravel', 'cms', 'filament']
    ];
}
```

## 7. Veritabanı İlişkileri

```php
// Page Model
class Page extends Model
{
    protected $casts = [
        'content' => 'array',  // JSON builder blokları
        'is_published' => 'boolean',
    ];
}

// Post Model
class Post extends Model
{
    protected $casts = [
        'ai_keywords' => 'array',
        'seo_score' => 'integer',
    ];
    
    // Otomatik slug
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($post) {
            $post->slug = Str::slug($post->title);
        });
    }
}
```

## 8. Frontend Route Yapısı

```php
// web.php
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/sayfa/{slug}', [PageController::class, 'show'])->name('page.show');
Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');
```

## 9. Güvenlik ve Optimizasyon

### A. API Key Yönetimi

```php
// config/services.php
return [
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],
    'google' => [
        'analytics_view_id' => env('GOOGLE_ANALYTICS_VIEW_ID'),
    ],
];
```

### B. Cache Stratejisi

```php
// Google Analytics verilerini 1 saat cache'le
Cache::remember('analytics_data', 3600, function () {
    return GoogleApiService::getAnalytics();
});
```

### C. Queue Kullanımı

```php
// AI işlemleri queue'ya alınabilir
GenerateBlogContentJob::dispatch($post);
```

## 10. Test Stratejisi

```php
// Feature Test örneği
class PageBuilderTest extends TestCase
{
    public function test_page_with_blocks_can_be_created()
    {
        $page = Page::create([
            'title' => 'Test Sayfası',
            'content' => [
                ['type' => 'hero', 'data' => ['title' => 'Merhaba']],
            ],
        ]);
        
        $this->assertDatabaseHas('pages', ['title' => 'Test Sayfası']);
    }
}
```

## 11. Deployment Checklist

- [ ] `.env.production` yapılandırması
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `npm run build`
- [ ] Database migration
- [ ] Storage link
- [ ] SSL sertifikası
- [ ] Google API credentials

---

**Son Güncelleme:** 20 Kasım 2025
