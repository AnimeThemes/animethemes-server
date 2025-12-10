<?php

declare(strict_types=1);

namespace App\Pivots\Morph;

use App\Contracts\Models\HasResources;
use App\Contracts\Models\Nameable;
use App\Events\Pivot\Morph\Resourceable\ResourceableCreated;
use App\Events\Pivot\Morph\Resourceable\ResourceableDeleted;
use App\Events\Pivot\Morph\Resourceable\ResourceableUpdated;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Pivots\BaseMorphPivot;
use Database\Factories\Pivots\Morph\ResourceableFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Model&HasResources&Nameable $resourceable
 * @property string $resourceable_type
 * @property int $resourceable_id
 * @property string|null $as
 * @property ExternalResource $resource
 * @property int $resource_id
 *
 * @method static ResourceableFactory factory(...$parameters)
 */
class Resourceable extends BaseMorphPivot
{
    final public const string TABLE = 'resourceables';

    final public const string ATTRIBUTE_AS = 'as';
    final public const string ATTRIBUTE_RESOURCE = 'resource_id';
    final public const string ATTRIBUTE_RESOURCEABLE_TYPE = 'resourceable_type';
    final public const string ATTRIBUTE_RESOURCEABLE_ID = 'resourceable_id';

    final public const string RELATION_RESOURCE = 'resource';
    final public const string RELATION_RESOURCEABLE = 'resourceable';

    /**
     * The models that have resources.
     *
     * @return class-string<Model&HasResources>
     */
    public static $resourceables = [
        Anime::class,
        AnimeThemeEntry::class,
        Artist::class,
        Song::class,
        Studio::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Resourceable::TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Resourceable::ATTRIBUTE_AS,
        Resourceable::ATTRIBUTE_RESOURCE,
        Resourceable::ATTRIBUTE_RESOURCEABLE_TYPE,
        Resourceable::ATTRIBUTE_RESOURCEABLE_ID,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => ResourceableCreated::class,
        'deleted' => ResourceableDeleted::class,
        'updated' => ResourceableUpdated::class,
    ];

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            Resourceable::ATTRIBUTE_RESOURCE,
            Resourceable::ATTRIBUTE_RESOURCEABLE_TYPE,
            Resourceable::ATTRIBUTE_RESOURCEABLE_ID,
        ];
    }

    /**
     * Gets the resource that owns the resourceable.
     *
     * @return BelongsTo<ExternalResource, $this>
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(ExternalResource::class, Resourceable::ATTRIBUTE_RESOURCE);
    }

    /**
     * Gets the model that owns the resourceable.
     */
    public function resourceable(): MorphTo
    {
        return $this->morphTo(Resourceable::RELATION_RESOURCEABLE);
    }
}
