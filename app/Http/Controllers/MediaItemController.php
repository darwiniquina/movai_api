<?php

namespace App\Http\Controllers;

use App\Models\MediaItem;

class MediaItemController extends Controller
{
    public function index()
    {
        return MediaItem::paginate(20);
    }

    public function show(MediaItem $mediaItem)
    {
        return $mediaItem;
    }
}
