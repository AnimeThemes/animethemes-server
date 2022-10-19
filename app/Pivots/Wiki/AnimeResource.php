<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\AnimeResource\AnimeResourceCreated;
use App\Events\Pivot\Wiki\AnimeResource\AnimeResourceDeleted;
use App\Events\Pivot\Wiki\AnimeResource\AnimeResourceUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\AnimeResourceFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class AnimeResource.
 *
 * @property Anime $anime
 * @property string $as
 * @property ExternalResource $resource
 *
 * @method static AnimeResourceFactory factory(...$parameters)
 */
class AnimeResource extends BasePivot
{
    final public const TABLE = 'anime_resource';

    final public const ATTRIBUTE_ANIME = 'anime_id';
    final public const ATTRIBUTE_AS = 'as';
    final public const ATTRIBUTE_RESOURCE = 'resource_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        AnimeResource::ATTRIBUTE_AS,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = AnimeResource::TABLE;

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            AnimeResource::ATTRIBUTE_ANIME,
            AnimeResource::ATTRIBUTE_RESOURCE,
        ];
    }

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
        return $this->belongsTo(Anime::class, AnimeResource::ATTRIBUTE_ANIME);
    }

    /**
     * Gets the resource that owns the anime resource.
     *
     * @return BelongsTo
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(ExternalResource::class, AnimeResource::ATTRIBUTE_RESOURCE);
    }
}
