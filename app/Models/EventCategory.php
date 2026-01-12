<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class EventCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon'
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'category_id');
    }

    public function scopeSearch(Builder $query, ?string $search): void
    {
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
    }

    public function scopeWithEventCount(Builder $query): void
    {
        $query->withCount('events')->orderBy('events_count', 'desc');
    }

    public function scopeBySlug(Builder $query, ?string $slug): void
    {
        if ($slug) {
            $query->where('slug', $slug);
        }
    }
}
