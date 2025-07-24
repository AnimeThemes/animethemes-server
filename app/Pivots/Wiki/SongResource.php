<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\SongResource\SongResourceCreated;
use App\Events\Pivot\Wiki\SongResource\SongResourceDeleted;
use App\Events\Pivot\Wiki\SongResource\SongResourceUpdated;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\SongResourceFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SongResource.
 *
 * @property Song $song
 * @property int $song_id
 * @property string $as
 * @property ExternalResource $resource
 * @property int $resource_id
 *
 * @method static SongResourceFactory factory(...$parameters)
 */
class SongResource extends BasePivot
{
    final public const TABLE = 'song_resource';

    final public const ATTRIBUTE_SONG = 'song_id';
    final public const ATTRIBUTE_AS = 'as';
    final public const ATTRIBUTE_RESOURCE = 'resource_id';

    final public const RELATION_SONG = 'song';
    final public const RELATION_RESOURCE = 'resource';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        SongResource::ATTRIBUTE_SONG,
        SongResource::ATTRIBUTE_AS,
        SongResource::ATTRIBUTE_RESOURCE,
    ];

    /**
     * The table associated with the model.
     */
    protected $table = SongResource::TABLE;

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            SongResource::ATTRIBUTE_SONG,
            SongResource::ATTRIBUTE_RESOURCE,
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
        'created' => SongResourceCreated::class,
        'deleted' => SongResourceDeleted::class,
        'updated' => SongResourceUpdated::class,
    ];

    /**
     * Gets the song that owns the anime resource.
     *
     * @return BelongsTo<Song, $this>
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class, SongResource::ATTRIBUTE_SONG);
    }

    /**
     * Gets the resource that owns the anime resource.
     *
     * @return BelongsTo<ExternalResource, $this>
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(ExternalResource::class, AnimeResource::ATTRIBUTE_RESOURCE);
    }
}
