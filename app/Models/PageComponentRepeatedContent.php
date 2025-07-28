<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageComponentRepeatedContent extends Model
{
    use HasFactory;

    protected $table = 'page_component_repeated_contents';

    protected $fillable = [
        'component_id',
        'content_data', // JSON structure for loop content item fields
    ];

    protected $casts = [
        'content_data' => 'array',
    ];

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |--------------------------------------------------------------------------
     */

    public function pageComponent()
    {
        return $this->belongsTo(PageComponent::class, 'component_id');
    }

    /*
     |--------------------------------------------------------------------------
     | Helper Methods
     |--------------------------------------------------------------------------
     */

    public function isLimitOver(): bool
    {
        $component = $this->pageComponent;

        if (! $component?->component_key) {
            return false;
        }

        $componentKey = strtolower($component->component_key);
        $cacheKey     = "component_definition_{$componentKey}";

        $definition = cache()->rememberForever($cacheKey, function () use ($componentKey) {
            $file = resource_path("structure/page_component/{$componentKey}.php");

            return file_exists($file) ? include $file : [];
        });

        $limit = $definition['repeated_content_limit'] ?? null;

        return is_numeric($limit) && $limit > 0 && $component->repeatedContents()->count() >= (int) $limit;
    }

    public function getField(string $key, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $data   = $this->content_data;

        if (isset($data[$key])) {
            $field = $data[$key];
            if (isset($field['translatable']) && $field['translatable'] === true) {
                return $field['value'][$locale] ?? '';
            }

            return $field['value'] ?? '';
        }

        return null;
    }

    /*
     |--------------------------------------------------------------------------
     | Model Events - Auto Cache Clear
     |--------------------------------------------------------------------------
     */

    protected static function booted()
    {
        static::saved(fn (self $item) => $item->flushRelatedPagesCache());
        static::deleted(fn (self $item) => $item->flushRelatedPagesCache());
    }

    /**
     * Flush cache of pages related to the parent component.
     */
    public function flushRelatedPagesCache(): void
    {
        $component = $this->pageComponent;

        if (! $component) {
            return;
        }

        $pages = Page::whereJsonContains('component_ids', (string) $component->id)->pluck('id', 'slug');
        foreach ($pages as $slug => $pageId) {
            Cache::forget("page_components_{$pageId}");
            Cache::forget('page_slug_'.md5($slug));
        }
    }
}
