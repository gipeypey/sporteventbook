<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Runner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'photo',
        'country',
        'gender',
        'utmb_index_20k',
        'utmb_index_50k',
        'utmb_index_100k',
        'utmb_index_100m',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
