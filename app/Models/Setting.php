<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key', 'value', 'type', 'group', 'label', 'description', 'is_public', 'sort_order',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group)->orderBy('sort_order')->orderBy('label');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function getTypedValue()
    {
        return match($this->type) {
            'json' => json_decode($this->value, true),
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            default => $this->value,
        };
    }

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->getTypedValue() : $default;
    }

    public static function set(string $key, $value, string $type = 'text', ?string $group = null): Setting
    {
        if ($type === 'json' && is_array($value)) {
            $value = json_encode($value);
        }
        if ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }

        return static::updateOrCreate(['key' => $key], [
            'value' => $value,
            'type' => $type,
            'group' => $group,
        ]);
    }

    public static function getAll(?string $group = null): array
    {
        $query = static::query();
        if ($group) {
            $query->where('group', $group);
        }
        return $query->get()->mapWithKeys(fn($setting) => [
            $setting->key => $setting->getTypedValue()
        ])->all();
    }

    public static function getSiteName(): string
    {
        return static::get('site_name', config('app.name'));
    }

    public static function getSiteLogo(): ?string
    {
        $logo = static::get('site_logo');
        return $logo ? asset('storage/' . $logo) : null;
    }

    public static function getOpenAiKey(): ?string
    {
        return static::get('openai_api_key') ?? config('openai.api_key');
    }

    public static function getGoogleAnalyticsId(): ?string
    {
        return static::get('google_analytics_view_id');
    }
}
