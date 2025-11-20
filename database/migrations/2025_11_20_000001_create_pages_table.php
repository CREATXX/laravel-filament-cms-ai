<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dinamik sayfalar tablosunu oluştur.
     * Bu tablo Filament Page Builder ile oluşturulan sayfaları saklar.
     */
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            
            // Sayfa bilgileri
            $table->string('title')->comment('Sayfa başlığı');
            $table->string('slug')->unique()->comment('URL dostu slug');
            
            // Builder içeriği (JSON formatında bloklar)
            $table->json('content')->nullable()->comment('Filament Builder blokları');
            
            // SEO alanları
            $table->string('seo_title')->nullable()->comment('SEO başlığı');
            $table->text('seo_description')->nullable()->comment('Meta açıklama');
            $table->json('seo_keywords')->nullable()->comment('Anahtar kelimeler');
            
            // Durum
            $table->boolean('is_published')->default(false)->comment('Yayın durumu');
            $table->timestamp('published_at')->nullable()->comment('Yayınlanma tarihi');
            
            // Görseller
            $table->string('featured_image')->nullable()->comment('Öne çıkan görsel');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // İndeksler
            $table->index('slug');
            $table->index('is_published');
        });
    }

    /**
     * Migration'ı geri al.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
