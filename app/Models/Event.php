<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'category_id',
        'venue_id',
        'title',
        'slug',
        'description',
        'image',
        'is_featured',
        'date',
        'status',
        'max_participants',
        'total_prize',
        'price',
        'winner_name',
        'winner_number'
    ];

    protected $casts = [
        'date' => 'datetime',
        'total_prize' => 'decimal:2',
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function prizes(): HasMany
    {
        return $this->hasMany(EventPrize::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Event $event) {
            if (empty($event->slug) && !empty($event->title)) {
                $base = \Illuminate\Support\Str::slug($event->title);
                $slug = $base;
                $counter = 1;
                while (static::where('slug', $slug)->where('id', '!=', $event->id)->exists()) {
                    $slug = $base . '-' . $counter;
                    $counter++;
                }
                $event->slug = $slug;
            }
        });
    }
}
