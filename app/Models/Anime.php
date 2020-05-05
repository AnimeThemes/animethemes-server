<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anime extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anime';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'anime_id';

    /**
     * Get the synonyms for the anime
     */
    public function synonyms() {
        return $this->hasMany('App\Models\Synonym');
    }

    /**
     * Get the series the anime is included in
     */
    public function series() {
        return $this->belongsToMany('App\Models\Series');
    }

    /**
     * Get the themes for the anime
     */
    public function themes() {
        return $this->hasMany('App\Models\Theme');
    }

    /**
     * Get the resources for the anime
     */
    public function resources() {
        return $this->belongsToMany('App\Models\Resource');
    }
}
