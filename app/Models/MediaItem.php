<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaItem extends Model
{
    protected $fillable = [
        'tmdb_id',
        'type',
        'title',
        'poster_path',
        'release_date',
        'overview',
    ];

    public function watchlistItems()
    {
        return $this->hasMany(WatchlistItem::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
