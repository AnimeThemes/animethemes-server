<?php

namespace App\Models;

use App\ScoutElastic\SongIndexConfigurator;
use App\ScoutElastic\SongSearchRule;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Song extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable, Searchable;

    protected $fillable = ['title'];

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

    protected $indexConfigurator = SongIndexConfigurator::class;

    protected $searchRules = [
        SongSearchRule::class
    ];

    protected $mapping = [
        'properties' => [
            'title' => [
                'type' => 'text'
            ]
        ]
    ];

    /**
     * Get the themes that use this song
     */
    public function themes() {
        return $this->hasMany('App\Models\Theme', 'song_id', 'song_id');
    }

    /**
     * Get the artists included in the performance
     */
    public function artists() {
        return $this->belongsToMany('App\Models\Artist', 'artist_song', 'song_id', 'artist_id')->withPivot('as');
    }
}
