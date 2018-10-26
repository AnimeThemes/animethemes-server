<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    protected $fillable = ['name', 'serie_id', 'collection', 'season', 'mal_id', 'anilist_id', 'kitsu_id', 'anidb_id'];

    /**
    * Get the serie that owns the anime.
    */
    public function serie()
    {
        return $this->belongsTo('App\Models\Serie');
    }

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
