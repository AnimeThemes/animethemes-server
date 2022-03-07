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
use App\Pivots\BasePivot;
use App\Pivots\StudioResource;
use BenSampo\Enum\Enum;
use Database\Factories\Wiki\ExternalResourceFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Class Resource.
 *
 * @property Collection $anime
 * @property Collection $artists
 * @property int|null $external_id
 * @property string|null $link
 * @property BasePivot $pivot
 * @property int $resource_id
 * @property Enum|null $site
 *
 * @method static ExternalResourceFactory factory(...$parameters)
 */
class ExternalResource extends BaseModel
{
    final public const TABLE = 'resources';

    final public const ATTRIBUTE_EXTERNAL_ID = 'external_id';
    final public const ATTRIBUTE_ID = 'resource_id';
    final public const ATTRIBUTE_LINK = 'link';
    final public const ATTRIBUTE_SITE = 'site';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_ARTISTS = 'artists';
    final public const RELATION_STUDIOS = 'studios';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        ExternalResource::ATTRIBUTE_EXTERNAL_ID,
        ExternalResource::ATTRIBUTE_LINK,
        ExternalResource::ATTRIBUTE_SITE,
    ];

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
    protected $table = ExternalResource::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = ExternalResource::ATTRIBUTE_ID;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        ExternalResource::ATTRIBUTE_EXTERNAL_ID => 'int',
        ExternalResource::ATTRIBUTE_SITE => ResourceSite::class,
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return strval($this->link);
    }

    /**
     * Get the anime that reference this resource.
     *
     * @return BelongsToMany
     */
    public function anime(): BelongsToMany
    {
        return $this->belongsToMany(Anime::class, AnimeResource::TABLE, ExternalResource::ATTRIBUTE_ID, Anime::ATTRIBUTE_ID)
            ->using(AnimeResource::class)
            ->withPivot(AnimeResource::ATTRIBUTE_AS)
            ->withTimestamps();
    }

    /**
     * Get the artists that reference this resource.
     *
     * @return BelongsToMany
     */
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, ArtistResource::TABLE, ExternalResource::ATTRIBUTE_ID, Artist::ATTRIBUTE_ID)
            ->using(ArtistResource::class)
            ->withPivot(ArtistResource::ATTRIBUTE_AS)
            ->withTimestamps();
    }

    /**
     * Get the studios that reference this resource.
     *
     * @return BelongsToMany
     */
    public function studios(): BelongsToMany
    {
        return $this->belongsToMany(Studio::class, StudioResource::TABLE, ExternalResource::ATTRIBUTE_ID, Studio::ATTRIBUTE_ID)
        ->using(StudioResource::class)
        ->withPivot(StudioResource::ATTRIBUTE_AS)
        ->withTimestamps();
    }
}
