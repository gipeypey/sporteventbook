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
        'start_date',
        'status',
        'max_participants',
        'total_prize',
        'price',
        'winner_name',
        'winner_number'
    ];

    protected $casts = [
        'date' => 'datetime',
        'start_date' => 'datetime',
        'total_prize' => 'decimal:2',
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the start_date attribute (alias for date for compatibility)
     */
    public function getStartDateAttribute()
    {
        return $this->date ?? $this->attributes['start_date'] ?? null;
    }

    /**
     * Get the image URL attribute
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        // Check if it's a full URL
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // Check if path already includes assets/images
        if (str_contains($this->image, 'assets/images')) {
            $fullPath = public_path($this->image);
            if (file_exists($fullPath)) {
                return asset($this->image);
            }
        }

        // Check if it's just the filename (stored in events directory)
        $eventsPath = public_path('assets/images/events/' . $this->image);
        if (file_exists($eventsPath)) {
            return asset('assets/images/events/' . $this->image);
        }

        // Check with leading slash or direct path
        $directPath = public_path($this->image);
        if (file_exists($directPath)) {
            return asset($this->image);
        }

        // Fallback: try to find any matching file
        $possiblePaths = [
            'assets/images/events/' . $this->image,
            'assets/images/' . $this->image,
            'events/' . $this->image,
            $this->image,
            ltrim($this->image, '/'),
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return null;
    }

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

    public function eventCategory(): BelongsTo
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

    public function eventPrizes(): HasMany
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
