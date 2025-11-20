<?php

namespace App\Services;

use OpenAI\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

/**
 * AI Destekli Görsel Üretim Servisi
 * 
 * OpenAI DALL-E API kullanarak görsel üretir ve yönetir.
 */
class AiImageService
{
    protected Client $client;
    
    public function __construct()
    {
        $this->client = \OpenAI::client(config('services.openai.api_key'));
    }
    
    /**
     * Metin açıklamasından görsel üretir (DALL-E 3)
     *
     * @param string $prompt Görsel açıklaması
     * @param string $size Boyut: '1024x1024', '1792x1024', '1024x1792'
     * @param string $style Stil: 'vivid' (canlı) veya 'natural' (doğal)
     * @param string $quality Kalite: 'standard' veya 'hd'
     * @return array ['url' => string, 'path' => string, 'revised_prompt' => string]
     */
    public function generateImage(
        string $prompt, 
        string $size = '1024x1024',
        string $style = 'vivid',
        string $quality = 'standard'
    ): array {
        // Türkçe prompt'u İngilizce'ye çevir (DALL-E İngilizce daha iyi çalışır)
        $englishPrompt = $this->translatePrompt($prompt);
        
        $response = $this->client->images()->create([
            'model' => 'dall-e-3',
            'prompt' => $englishPrompt,
            'n' => 1,
            'size' => $size,
            'style' => $style,
            'quality' => $quality,
            'response_format' => 'url',
        ]);
        
        $imageData = $response->data[0];
        $imageUrl = $imageData->url;
        
        // Görseli indir ve storage'a kaydet
        $savedPath = $this->downloadAndStore($imageUrl, 'ai-generated');
        
        return [
            'url' => $imageUrl,
            'path' => $savedPath,
            'revised_prompt' => $imageData->revised_prompt ?? $englishPrompt,
            'original_prompt' => $prompt,
        ];
    }
    
    /**
     * Blog yazısı için featured image üretir
     *
     * @param string $title Blog başlığı
     * @param string $excerpt Blog özeti
     * @return array Görsel bilgileri
     */
    public function generateFeaturedImage(string $title, string $excerpt = ''): array
    {
        $prompt = "Professional blog featured image for: {$title}. ";
        
        if (!empty($excerpt)) {
            $prompt .= "Context: " . substr($excerpt, 0, 100);
        }
        
        $prompt .= " Modern, clean design, suitable for blog header. High quality, professional photography style.";
        
        return $this->generateImage($prompt, '1792x1024', 'vivid', 'hd');
    }
    
    /**
     * Görsel varyasyonları üretir (mevcut görselden)
     *
     * @param string $imagePath Orijinal görsel yolu
     * @param int $count Üretilecek varyasyon sayısı
     * @return array Varyasyon bilgileri
     */
    public function generateVariations(string $imagePath, int $count = 2): array
    {
        // DALL-E 2 ile varyasyon üretimi
        $response = $this->client->images()->createVariation([
            'image' => fopen(Storage::path($imagePath), 'r'),
            'n' => min($count, 4), // Max 4
            'size' => '1024x1024',
            'response_format' => 'url',
        ]);
        
        $variations = [];
        foreach ($response->data as $imageData) {
            $savedPath = $this->downloadAndStore($imageData->url, 'variations');
            $variations[] = [
                'url' => $imageData->url,
                'path' => $savedPath,
            ];
        }
        
        return $variations;
    }
    
    /**
     * Görseli düzenler/maskeler (DALL-E 2)
     *
     * @param string $imagePath Orijinal görsel
     * @param string $maskPath Maske görseli (şeffaf PNG)
     * @param string $prompt Düzenleme açıklaması
     * @return array Düzenlenmiş görsel bilgileri
     */
    public function editImage(string $imagePath, string $maskPath, string $prompt): array
    {
        $englishPrompt = $this->translatePrompt($prompt);
        
        $response = $this->client->images()->edit([
            'image' => fopen(Storage::path($imagePath), 'r'),
            'mask' => fopen(Storage::path($maskPath), 'r'),
            'prompt' => $englishPrompt,
            'n' => 1,
            'size' => '1024x1024',
            'response_format' => 'url',
        ]);
        
        $imageData = $response->data[0];
        $savedPath = $this->downloadAndStore($imageData->url, 'edited');
        
        return [
            'url' => $imageData->url,
            'path' => $savedPath,
        ];
    }
    
    /**
     * Kategori/konu için icon/illustration üretir
     *
     * @param string $category Kategori adı
     * @return array Icon bilgileri
     */
    public function generateCategoryIcon(string $category): array
    {
        $prompt = "Simple, minimalist icon for '{$category}' category. Flat design, single color, vector style, clean lines, white background.";
        
        return $this->generateImage($prompt, '1024x1024', 'natural', 'standard');
    }
    
    /**
     * Toplu görsel üretimi (batch)
     *
     * @param array $prompts Prompt dizisi ['prompt1', 'prompt2', ...]
     * @return array Üretilen görseller
     */
    public function batchGenerate(array $prompts): array
    {
        $results = [];
        
        foreach ($prompts as $prompt) {
            try {
                $results[] = $this->generateImage($prompt);
                
                // API rate limit için bekleme
                sleep(2);
            } catch (\Exception $e) {
                $results[] = [
                    'error' => $e->getMessage(),
                    'prompt' => $prompt,
                ];
            }
        }
        
        return $results;
    }
    
    /**
     * Türkçe prompt'u İngilizce'ye çevirir
     */
    protected function translatePrompt(string $turkishPrompt): string
    {
        // Zaten İngilizce ise direkt dön
        if (!$this->containsTurkish($turkishPrompt)) {
            return $turkishPrompt;
        }
        
        $response = $this->client->chat()->create([
            'model' => config('services.openai.model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional translator. Translate Turkish image prompts to English, maintaining the descriptive details.'
                ],
                [
                    'role' => 'user',
                    'content' => "Translate this image prompt to English:\n\n{$turkishPrompt}"
                ]
            ],
            'temperature' => 0.3,
            'max_tokens' => 200,
        ]);
        
        return trim($response->choices[0]->message->content);
    }
    
    /**
     * Türkçe karakter kontrolü
     */
    protected function containsTurkish(string $text): bool
    {
        return preg_match('/[ğĞıİöÖüÜşŞçÇ]/', $text) === 1;
    }
    
    /**
     * URL'den görseli indir ve storage'a kaydet
     *
     * @param string $url Görsel URL
     * @param string $folder Klasör adı
     * @return string Kaydedilen dosya yolu
     */
    protected function downloadAndStore(string $url, string $folder = 'images'): string
    {
        // Görseli indir
        $contents = file_get_contents($url);
        
        // Benzersiz dosya adı oluştur
        $filename = $folder . '/' . uniqid('ai_') . '.png';
        
        // Storage'a kaydet
        Storage::disk('public')->put($filename, $contents);
        
        return $filename;
    }
    
    /**
     * Önceden üretilmiş görseli siler
     *
     * @param string $path Görsel yolu
     * @return bool Başarılı mı
     */
    public function deleteImage(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
    
    /**
     * AI görsel maliyet hesaplayıcı
     *
     * @param string $quality 'standard' veya 'hd'
     * @param string $size Görsel boyutu
     * @return float Maliyet (USD)
     */
    public function estimateCost(string $quality = 'standard', string $size = '1024x1024'): float
    {
        // DALL-E 3 fiyatları (2024)
        $prices = [
            'standard' => [
                '1024x1024' => 0.040,
                '1792x1024' => 0.080,
                '1024x1792' => 0.080,
            ],
            'hd' => [
                '1024x1024' => 0.080,
                '1792x1024' => 0.120,
                '1024x1792' => 0.120,
            ],
        ];
        
        return $prices[$quality][$size] ?? 0.040;
    }
}
