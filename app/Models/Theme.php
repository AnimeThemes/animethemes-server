<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = ['anime_id', 'artist_id', 'song_name',  'isNSFW', 'isSpoiler', 
            'theme', 'ver_major', 'ver_minor', 'episodes', 'notes'];

    /**
    * Get the anime that owns the theme.
    */
    public function anime() 
    {
        return $this->belongsTo('App\Models\Anime');
    }

    /**
    * Get theme artist
    */
    public function artist() 
    {
        return $this->belongsTo('App\Models\Artist');
    }

    /**
     * Get the videos for the theme.
     */
    public function videos()
    {
        return $this->hasMany('App\Models\Video');
    }
}
