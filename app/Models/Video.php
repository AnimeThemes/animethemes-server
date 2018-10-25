<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['basename', 'filename', 'path', 'theme_id', 'quality', 'isNC', 'isLyrics', 'source'];

    /**
    * Get the theme that owns the video.
    */
    public function theme()
    {
        return $this->belongsTo('App\Models\Theme');
    }
}
