<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'hours',
        'type',
        'mode',
        'status',
        'author',
        'user_id',
        'image_urls',
        'location_id',
        'location_lat',
        'location_lng',
        'location_address',
    ];

    protected $casts = [
        'image_urls' => 'array',
        'location_lat' => 'float',
        'location_lng' => 'float',
    ];
}