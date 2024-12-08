<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Events\Wiki\Studio\StudioCreated;
use App\Events\Wiki\Studio\StudioDeleted;
use App\Events\Wiki\Studio\StudioRestored;
use App\Events\Wiki\Studio\StudioUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Http\Resources\Pivot\Wiki\Resource\StudioImageResource;
use App\Http\Resources\Pivot\Wiki\Resource\StudioResourceResource;
use App\Models\BaseModel;
use App\Pivots\Wiki\AnimeStudio;
use App\Pivots\Wiki\StudioImage;
use App\Pivots\Wiki\StudioResource;
use Database\Factories\Wiki\StudioFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Class Studio.
 *
 * @property Collection<int, Anime> $anime
 * @property Collection<int, Image> $images
 * @property string $name
 * @property string $slug
 * @property int $studio_id
 *
 * @method static StudioFactory factory(...$parameters)
 */
class Studio extends BaseModel
{
    use Searchable;

    final public const TABLE = 'studios';

    final public const ATTRIBUTE_ID = 'studio_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_SLUG = 'slug';

    final public const RELATION_ANIME = 'anime';
    final public const RELATION_IMAGES = 'images';
    final public const RELATION_RESOURCES = 'resources';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Studio::ATTRIBUTE_NAME,
        Studio::ATTRIBUTE_SLUG,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => StudioCreated::class,
        'deleted' => StudioDeleted::class,
        'restored' => StudioRestored::class,
        'updated' => StudioUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Studio::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Studio::ATTRIBUTE_ID;

    /**
     * Get the route key for the model.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Studio::ATTRIBUTE_SLUG;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->slug;
    }

    /**
     * Get the anime that the studio produced.
     *
     * @return BelongsToMany<Anime, $this>
     */
    public function anime(): BelongsToMany
    {
        return $this->belongsToMany(Anime::class, AnimeStudio::TABLE, Studio::ATTRIBUTE_ID, Anime::ATTRIBUTE_ID)
            ->using(AnimeStudio::class)
            ->as(AnimeStudioResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the resources for the studio.
     *
     * @return BelongsToMany<ExternalResource, $this>
     */
    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(ExternalResource::class, StudioResource::TABLE, Studio::ATTRIBUTE_ID, ExternalResource::ATTRIBUTE_ID)
            ->using(StudioResource::class)
            ->withPivot(StudioResource::ATTRIBUTE_AS)
            ->as(StudioResourceResource::$wrap)
            ->withTimestamps();
    }

    /**
     * Get the images for the studio.
     *
     * @return BelongsToMany<Image, $this>
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, StudioImage::TABLE, Studio::ATTRIBUTE_ID, Image::ATTRIBUTE_ID)
            ->using(StudioImage::class)
            ->as(StudioImageResource::$wrap)
            ->withTimestamps();
    }
}
