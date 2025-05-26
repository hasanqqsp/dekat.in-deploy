<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'category',
        'coords',
        'image',
        'open_hour',
        'close_hour',
        'start_price',
        'end_price',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'alamat_lengkap',
    ];
    protected $casts = [
        'coords' => 'array',
    ];

    public function contributor()
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
