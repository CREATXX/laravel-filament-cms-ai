<?php

namespace App\Services;

use OpenAI\Client;
use Illuminate\Support\Str;

/**
 * AI Destekli İçerik Üretim Servisi
 * 
 * OpenAI API kullanarak blog yazıları, başlıklar ve özet metinler üretir.
 */
class AiContentService
{
    protected Client $client;
    
    public function __construct()
    {
        $this->client = \OpenAI::client(config('services.openai.api_key'));
    }
    
    /**
     * Belirtilen konu hakkında tam blog yazısı üretir
     *
     * @param string $topic Blog konusu
     * @param int $wordCount Hedef kelime sayısı (varsayılan: 800)
     * @param string $tone Yazı tonu (professional, casual, friendly)
     * @return array ['title' => string, 'content' => string, 'excerpt' => string, 'keywords' => array]
     */
    public function generateBlog(string $topic, int $wordCount = 800, string $tone = 'professional'): array
    {
        $prompt = config('openai.prompts.blog_writer');
        $prompt = str_replace(['{topic}', '{word_count}', '{tone}'], [$topic, $wordCount, $tone], $prompt);
        
        $response = $this->client->chat()->create([
            'model' => config('services.openai.model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sen profesyonel bir Türkçe içerik yazarısın. SEO uyumlu, akıcı ve ilgi çekici blog yazıları yazıyorsun.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => $wordCount * 2, // Türkçe için token buffer
        ]);
        
        $generatedContent = $response->choices[0]->message->content;
        
        // İçeriği parse et (başlık ve gövde ayrımı)
        $lines = explode("\n", trim($generatedContent));
        $title = $this->extractTitle($lines);
        $content = $this->cleanContent($generatedContent);
        
        return [
            'title' => $title,
            'content' => $content,
            'excerpt' => $this->generateExcerpt($content),
            'keywords' => $this->extractKeywords($content),
            'reading_time' => $this->calculateReadingTime($content),
        ];
    }
    
    /**
     * Mevcut içerik için SEO uyumlu başlık önerileri üretir
     *
     * @param string $content İçerik metni
     * @param int $count Üretilecek başlık sayısı
     * @return array Başlık önerileri
     */
    public function generateTitles(string $content, int $count = 5): array
    {
        $excerpt = Str::limit($content, 500);
        
        $response = $this->client->chat()->create([
            'model' => config('services.openai.model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sen SEO uzmanısın. İçerikler için dikkat çekici, anahtar kelime içeren Türkçe başlıklar öneriyorsun.'
                ],
                [
                    'role' => 'user',
                    'content' => "Aşağıdaki içerik için {$count} adet SEO uyumlu başlık öner. Her başlık 60 karakteri geçmemeli:\n\n{$excerpt}"
                ]
            ],
            'temperature' => 0.8,
        ]);
        
        $titles = explode("\n", trim($response->choices[0]->message->content));
        return array_map(fn($t) => trim($t, "0123456789.-) "), array_filter($titles));
    }
    
    /**
     * İçerik için özet (excerpt) üretir
     *
     * @param string $content Tam içerik
     * @param int $maxLength Maksimum karakter sayısı
     * @return string Özet metin
     */
    public function generateExcerpt(string $content, int $maxLength = 200): string
    {
        $response = $this->client->chat()->create([
            'model' => config('services.openai.model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sen içerik özetleme uzmanısın. Kısa, çekici ve bilgilendirici özetler yazıyorsun.'
                ],
                [
                    'role' => 'user',
                    'content' => "Aşağıdaki içeriği {$maxLength} karakter içinde özetle:\n\n" . Str::limit($content, 1000)
                ]
            ],
            'temperature' => 0.5,
            'max_tokens' => 100,
        ]);
        
        return trim($response->choices[0]->message->content);
    }
    
    /**
     * İçerik için meta description üretir
     *
     * @param string $content İçerik
     * @return string Meta description (155 karakter max)
     */
    public function generateMetaDescription(string $content): string
    {
        return $this->generateExcerpt($content, 155);
    }
    
    /**
     * İçeriği AI ile genişletir/iyileştirir
     *
     * @param string $content Mevcut içerik
     * @param string $instruction İyileştirme talimatı
     * @return string İyileştirilmiş içerik
     */
    public function improveContent(string $content, string $instruction = 'İçeriği daha detaylı ve ilgi çekici hale getir'): string
    {
        $response = $this->client->chat()->create([
            'model' => config('services.openai.model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sen içerik editörüsün. Metinleri iyileştirip daha profesyonel hale getiriyorsun.'
                ],
                [
                    'role' => 'user',
                    'content' => "{$instruction}\n\nMevcut İçerik:\n{$content}"
                ]
            ],
            'temperature' => 0.7,
        ]);
        
        return trim($response->choices[0]->message->content);
    }
    
    /**
     * İçerikten başlık çıkarır
     */
    protected function extractTitle(array $lines): string
    {
        foreach ($lines as $line) {
            $line = trim($line, "# \t\n\r\0\x0B");
            if (strlen($line) > 10 && strlen($line) < 100) {
                return $line;
            }
        }
        
        return 'Başlıksız İçerik';
    }
    
    /**
     * İçeriği temizler (markdown başlıkları kaldırır)
     */
    protected function cleanContent(string $content): string
    {
        // İlk başlığı kaldır
        $content = preg_replace('/^#+\s+.+\n/m', '', $content, 1);
        return trim($content);
    }
    
    /**
     * İçerikten anahtar kelimeleri çıkarır
     */
    protected function extractKeywords(string $content): array
    {
        $response = $this->client->chat()->create([
            'model' => config('services.openai.model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sen SEO uzmanısın. İçeriklerden anahtar kelimeleri tespit ediyorsun.'
                ],
                [
                    'role' => 'user',
                    'content' => "Aşağıdaki içerikten 5-10 adet önemli anahtar kelime çıkar (virgülle ayır):\n\n" . Str::limit($content, 500)
                ]
            ],
            'temperature' => 0.3,
            'max_tokens' => 100,
        ]);
        
        $keywords = explode(',', $response->choices[0]->message->content);
        return array_map('trim', $keywords);
    }
    
    /**
     * Okuma süresini hesaplar (dakika)
     */
    protected function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, (int) ceil($wordCount / 200)); // 200 kelime/dakika
    }
}
