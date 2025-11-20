<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AiContentService;
use App\Services\AiSeoService;
use App\Services\AiImageService;

class AiServiceProvider extends ServiceProvider
{
    /**
     * Register AI services
     */
    public function register(): void
    {
        // AI Content Service - Singleton olarak kaydet
        $this->app->singleton(AiContentService::class, function ($app) {
            return new AiContentService();
        });
        
        // AI SEO Service - Singleton olarak kaydet
        $this->app->singleton(AiSeoService::class, function ($app) {
            return new AiSeoService();
        });
        
        // AI Image Service - Singleton olarak kaydet
        $this->app->singleton(AiImageService::class, function ($app) {
            return new AiImageService();
        });
    }

    /**
     * Bootstrap AI services
     */
    public function boot(): void
    {
        //
    }
}
