<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPrize extends Model
{
    protected $fillable = [
        'event_id',
        'image',
        'name',
        'given_by'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
