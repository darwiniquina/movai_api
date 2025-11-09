<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['user_id', 'media_item_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mediaItem()
    {
        return $this->belongsTo(MediaItem::class);
    }
}
