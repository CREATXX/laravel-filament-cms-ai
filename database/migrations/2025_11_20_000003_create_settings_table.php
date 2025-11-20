<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Genel ayarlar tablosunu oluştur.
     * Site genelindeki ayarları key-value formatında saklar.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            
            // Ayar bilgileri
            $table->string('key')->unique()->comment('Ayar anahtarı (benzersiz)');
            $table->text('value')->nullable()->comment('Ayar değeri');
            $table->string('type')->default('text')->comment('Değer tipi: text, json, boolean, file');
            
            // Açıklama ve gruplama
            $table->string('group')->nullable()->comment('Ayar grubu (general, api, seo, etc.)');
            $table->string('label')->nullable()->comment('Türkçe etiket');
            $table->text('description')->nullable()->comment('Açıklama');
            
            // Meta bilgiler
            $table->boolean('is_public')->default(false)->comment('Public API\'de gösterilsin mi?');
            $table->integer('sort_order')->default(0)->comment('Sıralama');
            
            // Timestamps
            $table->timestamps();
            
            // İndeksler
            $table->index('key');
            $table->index('group');
        });
    }

    /**
     * Migration'ı geri al.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
