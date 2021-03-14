<?php

namespace App\Models;

use App\Enums\ResourceSite;
use App\Events\ExternalResource\ExternalResourceCreated;
use App\Events\ExternalResource\ExternalResourceDeleted;
use App\Events\ExternalResource\ExternalResourceRestored;
use App\Events\ExternalResource\ExternalResourceUpdated;
use App\Pivots\AnimeResource;
use App\Pivots\ArtistResource;
use BenSampo\Enum\Traits\CastsEnums;

class ExternalResource extends BaseModel
{
    use CastsEnums;

    /**
     * @var array
     */
    protected $fillable = ['site', 'link', 'external_id'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ExternalResourceCreated::class,
        'deleted' => ExternalResourceDeleted::class,
        'restored' => ExternalResourceRestored::class,
        'updated' => ExternalResourceUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'resource';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'resource_id';

    /**
     * @var array
     */
    protected $enumCasts = [
        'site' => ResourceSite::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'site' => 'int',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->link;
    }

    /**
     * Get the anime that reference this resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function anime()
    {
        return $this->belongsToMany('App\Models\Anime', 'anime_resource', 'resource_id', 'anime_id')->using(AnimeResource::class)->withPivot('as');
    }

    /**
     * Get the artists that reference this resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function artists()
    {
        return $this->belongsToMany('App\Models\Artist', 'artist_resource', 'resource_id', 'artist_id')->using(ArtistResource::class)->withPivot('as');
    }
}
