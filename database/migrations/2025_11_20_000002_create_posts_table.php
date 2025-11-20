<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Blog yazıları tablosunu oluştur.
     * AI destekli içerik üretimi ve SEO analizi için gerekli alanları içerir.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            
            // Yazı bilgileri
            $table->string('title')->comment('Yazı başlığı');
            $table->string('slug')->unique()->comment('URL slug');
            
            // İçerik
            $table->longText('content')->comment('Ana içerik (Rich Editor)');
            $table->text('excerpt')->nullable()->comment('Özet (AI tarafından oluşturulabilir)');
            
            // Görseller
            $table->string('featured_image')->nullable()->comment('Öne çıkan görsel');
            
            // SEO ve AI alanları
            $table->string('seo_title')->nullable()->comment('SEO başlığı');
            $table->text('seo_description')->nullable()->comment('Meta açıklama');
            $table->integer('seo_score')->nullable()->comment('AI SEO puanı (1-100)');
            $table->json('ai_keywords')->nullable()->comment('AI tarafından önerilen anahtar kelimeler');
            $table->json('ai_suggestions')->nullable()->comment('AI iyileştirme önerileri');
            
            // AI üretim bilgisi
            $table->boolean('is_ai_generated')->default(false)->comment('AI tarafından oluşturuldu mu?');
            $table->timestamp('ai_generated_at')->nullable()->comment('AI oluşturma tarihi');
            
            // Kategori ve etiketler (opsiyonel - ileride eklenebilir)
            $table->string('category')->nullable()->comment('Kategori');
            $table->json('tags')->nullable()->comment('Etiketler');
            
            // Durum
            $table->boolean('is_published')->default(false)->comment('Yayın durumu');
            $table->timestamp('published_at')->nullable()->comment('Yayınlanma tarihi');
            
            // İstatistikler
            $table->integer('view_count')->default(0)->comment('Görüntülenme sayısı');
            $table->integer('reading_time')->nullable()->comment('Okuma süresi (dakika)');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // İndeksler
            $table->index('slug');
            $table->index('is_published');
            $table->index('category');
            $table->index('published_at');
            $table->index('seo_score');
        });
    }

    /**
     * Migration'ı geri al.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
