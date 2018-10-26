<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $fillable = ['name'];

    /**
     * Get the animes for the serie.
     */
    public function animes()
    {
        return $this->hasMany('App\Models\Anime');
    }
}
