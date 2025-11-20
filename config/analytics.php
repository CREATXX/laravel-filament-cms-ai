<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Analytics Property ID
    |--------------------------------------------------------------------------
    |
    | Google Analytics 4 Property ID (örn: 123456789)
    | Settings model ile birlikte kullanılır
    |
    */
    'property_id' => env('GOOGLE_ANALYTICS_PROPERTY_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Service Account Credentials
    |--------------------------------------------------------------------------
    |
    | Google Analytics API için service account JSON dosyasının yolu
    | storage/app/analytics/service-account-credentials.json
    |
    */
    'service_account_credentials_json' => storage_path('app/analytics/service-account-credentials.json'),

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Analytics verilerini cache'leme süresi (saniye)
    |
    */
    'cache_lifetime_in_minutes' => 60 * 24, // 24 saat

    /*
    |--------------------------------------------------------------------------
    | Date Range
    |--------------------------------------------------------------------------
    |
    | Varsayılan tarih aralığı (gün)
    |
    */
    'default_date_range' => 30, // Son 30 gün
];
