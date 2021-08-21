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
 * @property int $studio_id
 * @property string $slug
 * @property string $name
 * @property Collection $anime
 * @method static StudioFactory factory(...$parameters)
 */
class Studio extends BaseModel
{
    use QueryDsl;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['slug', 'name'];

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
    protected $table = 'studios';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'studio_id';

    /**
     * Get the route key for the model.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
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
        return $this->belongsToMany('App\Models\Wiki\Anime', 'anime_studio', 'studio_id', 'anime_id')
            ->using(AnimeStudio::class)
            ->withTimestamps();
    }
}
