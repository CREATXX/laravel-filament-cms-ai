<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pages';

    protected $fillable = [
        'title', 'slug', 'content', 'seo_title', 'seo_description',
        'seo_keywords', 'is_published', 'published_at', 'featured_image',
    ];

    protected $casts = [
        'content' => 'array',
        'seo_keywords' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
            if (empty($page->seo_title)) {
                $page->seo_title = $page->title;
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title') && empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    public function getUrlAttribute(): string
    {
        return route('page.show', $this->slug);
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    public function getBlocksByType(string $type): array
    {
        if (!$this->content) return [];
        return collect($this->content)->filter(fn($block) => ($block['type'] ?? '') === $type)->values()->all();
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
