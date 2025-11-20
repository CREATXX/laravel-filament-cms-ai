<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Ayarları
    |--------------------------------------------------------------------------
    | OpenAI API bağlantı ayarları. API anahtarını .env dosyasından alır.
    */
    'api_key' => env('OPENAI_API_KEY'),
    
    'model' => env('OPENAI_MODEL', 'gpt-4'),
    
    'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
    
    /*
    |--------------------------------------------------------------------------
    | Prompt Şablonları
    |--------------------------------------------------------------------------
    | AI için kullanılacak sistem promptları
    */
    'prompts' => [
        'blog_writer' => 'Sen uzman bir SEO uyumlu içerik yazarısın. Türkçe blog yazıları yazıyorsun. İçerikler profesyonel, bilgilendirici ve SEO optimizasyonlu olmalıdır.',
        
        'seo_analyzer' => 'Sen bir SEO uzmanısın. İçerikleri analiz edip, SEO skorları verip, iyileştirme önerileri sunuyorsun. Cevaplarını JSON formatında ver.',
        
        'keyword_generator' => 'Sen bir anahtar kelime uzmanısın. Verilen içerik için en uygun anahtar kelimeleri belirle ve JSON array olarak döndür.',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | AI Limitleri
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'blog_min_length' => 500,
        'blog_max_length' => 3000,
        'excerpt_max_length' => 200,
    ],

];
