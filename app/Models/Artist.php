<?php

namespace App\Models;

use App\ScoutElastic\ArtistIndexConfigurator;
use App\ScoutElastic\ArtistSearchRule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use ScoutElastic\Searchable;

class Artist extends Model implements Auditable
{
    use HasFactory, Searchable;
    use \OwenIt\Auditing\Auditable;

    /**
     * @var array
     */
    protected $fillable = ['alias', 'name'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'artist';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'artist_id';

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['songs'] = $this->songs->toArray();

        return $array;
    }

    /**
     * @var string
     */
    protected $indexConfigurator = ArtistIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        ArtistSearchRule::class,
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text',
            ],
            'songs' => [
                'type' => 'nested',
                'properties' => [
                    'pivot'  => [
                        'type' => 'nested',
                        'properties' => [
                            'as' => [
                                'type' => 'text',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'alias';
    }

    /**
     * Get the songs the artist has performed in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function songs()
    {
        return $this->belongsToMany('App\Models\Song', 'artist_song', 'artist_id', 'song_id')->withPivot('as');
    }

    /**
     * Get the resources for the artist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function externalResources()
    {
        return $this->belongsToMany('App\Models\ExternalResource', 'artist_resource', 'artist_id', 'resource_id')->withPivot('as');
    }

    /**
     * Get the members that comprise this group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany('App\Models\Artist', 'artist_member', 'artist_id', 'member_id')->withPivot('as');
    }

    /**
     * Get the groups the artist has performed in.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany('App\Models\Artist', 'artist_member', 'member_id', 'artist_id')->withPivot('as');
    }
}
