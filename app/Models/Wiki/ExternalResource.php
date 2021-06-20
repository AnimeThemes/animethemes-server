<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Events\Wiki\ExternalResource\ExternalResourceCreated;
use App\Events\Wiki\ExternalResource\ExternalResourceDeleted;
use App\Events\Wiki\ExternalResource\ExternalResourceRestored;
use App\Events\Wiki\ExternalResource\ExternalResourceUpdated;
use App\Models\BaseModel;
use App\Pivots\AnimeResource;
use App\Pivots\ArtistResource;
use BenSampo\Enum\Traits\CastsEnums;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Resource.
 */
class ExternalResource extends BaseModel
{
    use CastsEnums;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['site', 'link', 'external_id'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, string>
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
     * The attributes that should be cast to enum types.
     *
     * @var array<string, string>
     */
    protected $enumCasts = [
        'site' => ResourceSite::class,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'site' => 'int',
        'external_id' => 'int',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->link;
    }

    /**
     * Get the anime that reference this resource.
     *
     * @return BelongsToMany
     */
    public function anime(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Wiki\Anime', 'anime_resource', 'resource_id', 'anime_id')
            ->using(AnimeResource::class)
            ->withPivot('as')
            ->withTimestamps();
    }

    /**
     * Get the artists that reference this resource.
     *
     * @return BelongsToMany
     */
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Wiki\Artist', 'artist_resource', 'resource_id', 'artist_id')
            ->using(ArtistResource::class)
            ->withPivot('as')
            ->withTimestamps();
    }
}
