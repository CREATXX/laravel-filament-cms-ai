<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'title', 'slug', 'content', 'excerpt', 'featured_image',
        'seo_title', 'seo_description', 'seo_score', 'ai_keywords', 'ai_suggestions',
        'is_ai_generated', 'ai_generated_at', 'category', 'tags',
        'is_published', 'published_at', 'view_count', 'reading_time',
    ];

    protected $casts = [
        'ai_keywords' => 'array',
        'ai_suggestions' => 'array',
        'tags' => 'array',
        'is_published' => 'boolean',
        'is_ai_generated' => 'boolean',
        'published_at' => 'datetime',
        'ai_generated_at' => 'datetime',
        'seo_score' => 'integer',
        'view_count' => 'integer',
        'reading_time' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->seo_title)) {
                $post->seo_title = $post->title;
            }
            if (empty($post->reading_time) && !empty($post->content)) {
                $post->reading_time = static::calculateReadingTime($post->content);
            }
            if (empty($post->excerpt) && !empty($post->content)) {
                $post->excerpt = Str::limit(strip_tags($post->content), 200);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('content')) {
                $post->reading_time = static::calculateReadingTime($post->content);
            }
        });
    }

    protected static function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $minutes = ceil($wordCount / 200);
        return max(1, $minutes);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now())
                    ->orderBy('published_at', 'desc');
    }

    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    public function scopeAiGenerated($query)
    {
        return $query->where('is_ai_generated', true);
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeHighSeoScore($query, int $minScore = 70)
    {
        return $query->where('seo_score', '>=', $minScore);
    }

    public function getUrlAttribute(): string
    {
        return route('blog.show', $this->slug);
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    public function getSeoStatusAttribute(): string
    {
        if (!$this->seo_score) return 'Analiz Edilmedi';
        if ($this->seo_score >= 80) return 'Mükemmel';
        if ($this->seo_score >= 60) return 'İyi';
        if ($this->seo_score >= 40) return 'Orta';
        return 'Geliştirilmeli';
    }

    public function incrementViewCount(): bool
    {
        return $this->increment('view_count');
    }

    public function publish(): bool
    {
        return $this->update(['is_published' => true, 'published_at' => $this->published_at ?? now()]);
    }

    public function unpublish(): bool
    {
        return $this->update(['is_published' => false]);
    }
}
