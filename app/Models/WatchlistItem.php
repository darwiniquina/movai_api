<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchlistItem extends Model
{
    protected $fillable = [
        'user_id',
        'media_item_id',
        'status',
        'started_at',
        'completed_at',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mediaItem()
    {
        return $this->belongsTo(MediaItem::class);
    }
}
