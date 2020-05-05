<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'song';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'song_id';

    /**
     * Get the themes that use this song
     */
    public function themes() {
        return $this->hasMany('App\Models\Theme');
    }

    /**
     * Get the artists included in the performance
     */
    public function artists() {
        return $this->belongsToMany('App\Models\Artist');
    }
}
