<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Concerns\Models\SoftDeletes;
use App\Concerns\Models\Submitable;
use App\Contracts\Models\HasImages;
use App\Contracts\Models\HasResources;
use App\Contracts\Models\SoftDeletable;
use App\Events\Wiki\Studio\StudioCreated;
use App\Events\Wiki\Studio\StudioDeleted;
use App\Events\Wiki\Studio\StudioRestored;
use App\Events\Wiki\Studio\StudioUpdated;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Models\BaseModel;
use App\Pivots\Morph\Imageable;
use App\Pivots\Morph\Resourceable;
use App\Pivots\Wiki\AnimeStudio;
use Database\Factories\Wiki\StudioFactory;
use Elastic\ScoutDriverPlus\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, Anime> $anime
 * @property Collection<int, Image> $images
 * @property string $name
 * @property string $slug
 * @property int $studio_id
 *
 * @method static StudioFactory factory(...$parameters)
 */
class Studio extends BaseModel implements HasImages, HasResources, SoftDeletable
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use Submitable;

    final public const string TABLE = 'studios';

    final public const string ATTRIBUTE_ID = 'studio_id';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_SLUG = 'slug';

    final public const string RELATION_ANIME = 'anime';
    final public const string RELATION_IMAGES = 'images';
    final public const string RELATION_RESOURCES = 'resources';

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
     * @var array<string, class-string>
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
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Studio::ATTRIBUTE_SLUG;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubtitle(): string
    {
        return $this->slug;
    }

    /**
     * @return BelongsToMany<Anime, $this, AnimeStudio>
     */
    public function anime(): BelongsToMany
    {
        return $this->belongsToMany(Anime::class, AnimeStudio::TABLE, AnimeStudio::ATTRIBUTE_STUDIO, AnimeStudio::ATTRIBUTE_ANIME)
            ->using(AnimeStudio::class)
            ->as(AnimeStudioResource::$wrap)
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<ExternalResource, $this, Resourceable, 'studioresource'>
     */
    public function resources(): MorphToMany
    {
        return $this->morphToMany(ExternalResource::class, Resourceable::RELATION_RESOURCEABLE, Resourceable::TABLE, Resourceable::ATTRIBUTE_RESOURCEABLE_ID, Resourceable::ATTRIBUTE_RESOURCE)
            ->using(Resourceable::class)
            ->withPivot(Resourceable::ATTRIBUTE_AS)
            ->as('studioresource')
            ->withTimestamps();
    }

    /**
     * @return MorphToMany<Image, $this, Imageable, 'studioimage'>
     */
    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, Imageable::RELATION_IMAGEABLE, Imageable::TABLE, Imageable::ATTRIBUTE_IMAGEABLE_ID, Imageable::ATTRIBUTE_IMAGE)
            ->using(Imageable::class)
            ->as('studioimage')
            ->withTimestamps();
    }
}
