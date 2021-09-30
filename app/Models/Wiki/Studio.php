<?php

declare(strict_types=1);

namespace App\Models\Wiki;

use App\Events\Wiki\Studio\StudioCreated;
use App\Events\Wiki\Studio\StudioDeleted;
use App\Events\Wiki\Studio\StudioRestored;
use App\Events\Wiki\Studio\StudioUpdated;
use App\Models\BaseModel;
use App\Pivots\AnimeStudio;
use Database\Factories\Wiki\StudioFactory;
use ElasticScoutDriverPlus\QueryDsl;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;

/**
 * Class Studio.
 *
 * @property Collection $anime
 * @property string $name
 * @property string $slug
 * @property int $studio_id
 *
 * @method static StudioFactory factory(...$parameters)
 */
class Studio extends BaseModel
{
    use QueryDsl;
    use Searchable;

    public const TABLE = 'studios';

    public const ATTRIBUTE_ID = 'studio_id';
    public const ATTRIBUTE_NAME = 'name';
    public const ATTRIBUTE_SLUG = 'slug';

    public const RELATION_ANIME = 'anime';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
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
     * Get the anime that the studio produced.
     *
     * @return BelongsToMany
     */
    public function anime(): BelongsToMany
    {
        return $this->belongsToMany(Anime::class, AnimeStudio::TABLE, Studio::ATTRIBUTE_ID, Anime::ATTRIBUTE_ID)
            ->using(AnimeStudio::class)
            ->withTimestamps();
    }
}
