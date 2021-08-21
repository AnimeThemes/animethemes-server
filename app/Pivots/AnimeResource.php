<?php

declare(strict_types=1);

namespace App\Pivots;

use App\Events\Pivot\AnimeResource\AnimeResourceCreated;
use App\Events\Pivot\AnimeResource\AnimeResourceDeleted;
use App\Events\Pivot\AnimeResource\AnimeResourceUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Database\Factories\Pivots\AnimeResourceFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeResource.
 *
 * @property string $as
 * @property Anime $anime
 * @property ExternalResource $resource
 * @method static AnimeResourceFactory factory(...$parameters)
 */
class AnimeResource extends BasePivot
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['as'];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anime_resource';

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => AnimeResourceCreated::class,
        'deleted' => AnimeResourceDeleted::class,
        'updated' => AnimeResourceUpdated::class,
    ];

    /**
     * Gets the anime that owns the anime resource.
     *
     * @return BelongsTo
     */
    public function anime(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\Anime', 'anime_id', 'anime_id');
    }

    /**
     * Gets the resource that owns the anime resource.
     *
     * @return BelongsTo
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo('App\Models\Wiki\ExternalResource', 'resource_id', 'resource_id');
    }
}
