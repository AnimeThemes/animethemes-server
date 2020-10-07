<?php

namespace App\Models;

use App\ScoutElastic\SongIndexConfigurator;
use App\ScoutElastic\SongSearchRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Song extends Model implements Auditable
{

    use HasFactory, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
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

    /**
     * @var string
     */
    protected $indexConfigurator = SongIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        SongSearchRule::class
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'title' => [
                'type' => 'text'
            ]
        ]
    ];

    /**
     * Get the themes that use this song
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function themes() {
        return $this->hasMany('App\Models\Theme', 'song_id', 'song_id');
    }

    /**
     * Get the artists included in the performance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function artists() {
        return $this->belongsToMany('App\Models\Artist', 'artist_song', 'song_id', 'artist_id')->withPivot('as');
    }
}
