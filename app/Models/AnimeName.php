<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimeName extends Model
{
    protected $fillable = ['anime_id', 'title', 'language'];

    /**
    * Get the anime that owns Name.
    */
    public function anime()
    {
        return $this->belongsTo('App\Models\Theme');
    }
}
