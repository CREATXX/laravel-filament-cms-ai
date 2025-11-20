<?php

namespace App\Services;

use App\Models\Post;
use OpenAI\Client;
use Illuminate\Support\Str;

/**
 * AI Destekli SEO Analiz ve Optimizasyon Servisi
 * 
 * İçerikleri SEO açısından analiz eder ve iyileştirme önerileri sunar.
 */
class AiSeoService
{
    protected Client $client;
    
    public function __construct()
    {
        $this->client = \OpenAI::client(config('services.openai.api_key'));
    }
    
    /**
     * Post için kapsamlı SEO analizi yapar
     *
     * @param Post $post Analiz edilecek post
     * @return array ['score' => int, 'suggestions' => array, 'keywords' => array, 'meta' => array]
     */
    public function analyzePost(Post $post): array
    {
        $analysis = [
            'score' => 0,
            'suggestions' => [],
            'keywords' => [],
            'meta' => [],
            'readability' => [],
        ];
        
        // 1. Başlık Analizi
        $titleAnalysis = $this->analyzeTitle($post->title);
        $analysis['score'] += $titleAnalysis['score'];
        $analysis['suggestions'] = array_merge($analysis['suggestions'], $titleAnalysis['suggestions']);
        
        // 2. İçerik Analizi
        $contentAnalysis = $this->analyzeContent($post->content);
        $analysis['score'] += $contentAnalysis['score'];
        $analysis['suggestions'] = array_merge($analysis['suggestions'], $contentAnalysis['suggestions']);
        
        // 3. Anahtar Kelime Analizi
        $keywordAnalysis = $this->analyzeKeywords($post->title, $post->content);
        $analysis['keywords'] = $keywordAnalysis['keywords'];
        $analysis['score'] += $keywordAnalysis['score'];
        $analysis['suggestions'] = array_merge($analysis['suggestions'], $keywordAnalysis['suggestions']);
        
        // 4. Meta Açıklama Analizi
        $metaAnalysis = $this->analyzeMetaDescription($post->seo_description);
        $analysis['score'] += $metaAnalysis['score'];
        $analysis['suggestions'] = array_merge($analysis['suggestions'], $metaAnalysis['suggestions']);
        
        // 5. Okunabilirlik Analizi
        $analysis['readability'] = $this->analyzeReadability($post->content);
        
        // 6. AI ile detaylı öneriler
        $aiSuggestions = $this->getAiSuggestions($post);
        $analysis['suggestions'] = array_merge($analysis['suggestions'], $aiSuggestions);
        
        // Toplam skoru 100 üzerinden normalize et
        $analysis['score'] = min(100, max(0, (int) $analysis['score']));
        
        return $analysis;
    }
    
    /**
     * Başlık SEO analizi
     */
    protected function analyzeTitle(string $title): array
    {
        $score = 0;
        $suggestions = [];
        
        $titleLength = mb_strlen($title);
        
        // Uzunluk kontrolü (50-60 karakter ideal)
        if ($titleLength >= 50 && $titleLength <= 60) {
            $score += 15;
        } elseif ($titleLength < 40) {
            $suggestions[] = '❌ Başlık çok kısa (min 40 karakter önerilir)';
        } elseif ($titleLength > 70) {
            $suggestions[] = '⚠️ Başlık çok uzun (max 60 karakter önerilir, Google keser)';
            $score += 5;
        } else {
            $score += 10;
        }
        
        // Sayı içeriyorsa bonus
        if (preg_match('/\d+/', $title)) {
            $score += 5;
            $suggestions[] = '✅ Başlıkta sayı var (dikkat çekici)';
        }
        
        // Soru işareti varsa bonus
        if (str_contains($title, '?')) {
            $score += 3;
            $suggestions[] = '✅ Soru formatı kullanılmış';
        }
        
        return ['score' => $score, 'suggestions' => $suggestions];
    }
    
    /**
     * İçerik SEO analizi
     */
    protected function analyzeContent(string $content): array
    {
        $score = 0;
        $suggestions = [];
        
        $wordCount = str_word_count(strip_tags($content));
        
        // Kelime sayısı kontrolü (min 300 kelime)
        if ($wordCount >= 1000) {
            $score += 20;
            $suggestions[] = '✅ İçerik yeterince detaylı (' . $wordCount . ' kelime)';
        } elseif ($wordCount >= 500) {
            $score += 15;
            $suggestions[] = '✅ İçerik orta uzunlukta (' . $wordCount . ' kelime)';
        } elseif ($wordCount >= 300) {
            $score += 10;
            $suggestions[] = '⚠️ İçerik kısa (' . $wordCount . ' kelime), 500+ önerilir';
        } else {
            $suggestions[] = '❌ İçerik çok kısa (' . $wordCount . ' kelime), min 300 kelime gerekli';
        }
        
        // Alt başlık kontrolü (H2, H3)
        $h2Count = substr_count($content, '<h2>');
        $h3Count = substr_count($content, '<h3>');
        
        if ($h2Count >= 2) {
            $score += 10;
            $suggestions[] = '✅ Alt başlıklar kullanılmış (' . $h2Count . ' adet H2)';
        } else {
            $suggestions[] = '⚠️ Daha fazla alt başlık kullanın (H2, H3)';
        }
        
        // Liste kontrolü
        if (str_contains($content, '<ul>') || str_contains($content, '<ol>')) {
            $score += 5;
            $suggestions[] = '✅ Liste elemanları var';
        }
        
        // Görsel kontrolü
        $imageCount = substr_count($content, '<img');
        if ($imageCount >= 1) {
            $score += 5;
            $suggestions[] = '✅ Görseller eklenmiş (' . $imageCount . ' adet)';
        } else {
            $suggestions[] = '⚠️ Görsel eklenmemiş';
        }
        
        return ['score' => $score, 'suggestions' => $suggestions];
    }
    
    /**
     * Anahtar kelime yoğunluğu analizi
     */
    protected function analyzeKeywords(string $title, string $content): array
    {
        $score = 0;
        $suggestions = [];
        $keywords = [];
        
        // AI ile anahtar kelime çıkar
        $prompt = config('openai.prompts.keyword_generator');
        $prompt = str_replace('{content}', Str::limit($content, 500), $prompt);
        
        $response = $this->client->chat()->create([
            'model' => config('services.openai.model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sen SEO uzmanısın. İçeriklerden anahtar kelimeleri ve LSI kelimelerini tespit ediyorsun.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.3,
        ]);
        
        $keywordText = $response->choices[0]->message->content;
        $keywords = array_map('trim', explode(',', $keywordText));
        
        // Başlıkta anahtar kelime kontrolü
        $keywordInTitle = false;
        foreach ($keywords as $keyword) {
            if (stripos($title, $keyword) !== false) {
                $keywordInTitle = true;
                break;
            }
        }
        
        if ($keywordInTitle) {
            $score += 15;
            $suggestions[] = '✅ Başlıkta anahtar kelime var';
        } else {
            $suggestions[] = '❌ Başlığa anahtar kelime ekleyin';
        }
        
        return [
            'score' => $score,
            'suggestions' => $suggestions,
            'keywords' => array_slice($keywords, 0, 10),
        ];
    }
    
    /**
     * Meta description analizi
     */
    protected function analyzeMetaDescription(?string $metaDescription): array
    {
        $score = 0;
        $suggestions = [];
        
        if (empty($metaDescription)) {
            $suggestions[] = '❌ Meta description eksik';
            return ['score' => 0, 'suggestions' => $suggestions];
        }
        
        $length = mb_strlen($metaDescription);
        
        if ($length >= 120 && $length <= 155) {
            $score += 15;
            $suggestions[] = '✅ Meta description uzunluğu ideal (' . $length . ' karakter)';
        } elseif ($length < 120) {
            $suggestions[] = '⚠️ Meta description kısa (' . $length . ' karakter)';
            $score += 5;
        } else {
            $suggestions[] = '⚠️ Meta description uzun (' . $length . ' karakter, Google keser)';
            $score += 10;
        }
        
        return ['score' => $score, 'suggestions' => $suggestions];
    }
    
    /**
     * Okunabilirlik analizi
     */
    protected function analyzeReadability(string $content): array
    {
        $text = strip_tags($content);
        $sentences = preg_split('/[.!?]+/', $text);
        $words = str_word_count($text);
        
        $avgWordsPerSentence = count($sentences) > 0 ? $words / count($sentences) : 0;
        
        return [
            'words' => $words,
            'sentences' => count($sentences),
            'avg_sentence_length' => round($avgWordsPerSentence, 1),
            'reading_time' => ceil($words / 200), // dakika
            'difficulty' => $avgWordsPerSentence > 20 ? 'Zor' : ($avgWordsPerSentence > 15 ? 'Orta' : 'Kolay'),
        ];
    }
    
    /**
     * AI ile detaylı SEO önerileri
     */
    protected function getAiSuggestions(Post $post): array
    {
        $prompt = config('openai.prompts.seo_analyzer');
        $prompt = str_replace(
            ['{title}', '{content}', '{meta_description}'],
            [$post->title, Str::limit($post->content, 500), $post->seo_description ?? ''],
            $prompt
        );
        
        $response = $this->client->chat()->create([
            'model' => config('services.openai.model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sen SEO uzmanısın. İçerikleri analiz edip somut iyileştirme önerileri sunuyorsun.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.5,
        ]);
        
        $suggestions = explode("\n", trim($response->choices[0]->message->content));
        return array_filter(array_map('trim', $suggestions));
    }
    
    /**
     * Anahtar kelime önerileri üretir
     *
     * @param string $topic Konu
     * @param int $count Öneri sayısı
     * @return array Anahtar kelime önerileri
     */
    public function suggestKeywords(string $topic, int $count = 20): array
    {
        $response = $this->client->chat()->create([
            'model' => config('services.openai.model', 'gpt-4'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Sen SEO anahtar kelime uzmanısın. Long-tail ve LSI anahtar kelimeleri öneriyorsun.'
                ],
                [
                    'role' => 'user',
                    'content' => "'{$topic}' konusu için {$count} adet Türkçe SEO anahtar kelimesi öner (virgülle ayır). Long-tail ve LSI kelimeler içersin."
                ]
            ],
            'temperature' => 0.7,
        ]);
        
        $keywords = explode(',', $response->choices[0]->message->content);
        return array_map('trim', $keywords);
    }
}
