<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $fillable = ['name'];

    /**
     * Get the themes singed from artist.
     */
    public function themes()
    {
        return $this->hasMany('App\Models\Theme');
    }
}
