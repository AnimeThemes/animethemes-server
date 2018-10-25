<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    protected $fillable = ['name', 'collection', 'season', 'mal_id', 'anilist_id', 'kitsu_id', 'anidb_id'];

    /**
     * Get the themes for the anime.
     */
    public function themes()
    {
        return $this->hasMany('App\Models\Theme');
    }

    /**
     * Get the names for the anime.
     */
    public function names()
    {
        return $this->hasMany('App\Models\AnimeName');
    }
}
