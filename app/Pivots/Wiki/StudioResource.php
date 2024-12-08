<?php

declare(strict_types=1);

namespace App\Pivots\Wiki;

use App\Events\Pivot\Wiki\StudioResource\StudioResourceCreated;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceDeleted;
use App\Events\Pivot\Wiki\StudioResource\StudioResourceUpdated;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\BasePivot;
use Database\Factories\Pivots\Wiki\StudioResourceFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class StudioResource.
 *
 * @property string $as
 * @property ExternalResource $resource
 * @property int $resource_id
 * @property Studio $studio
 * @property int $studio_id
 *
 * @method static StudioResourceFactory factory(...$parameters)
 */
class StudioResource extends BasePivot
{
    final public const TABLE = 'studio_resource';

    final public const ATTRIBUTE_AS = 'as';
    final public const ATTRIBUTE_STUDIO = 'studio_id';
    final public const ATTRIBUTE_RESOURCE = 'resource_id';

    final public const RELATION_RESOURCE = 'resource';
    final public const RELATION_STUDIO = 'studio';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        StudioResource::ATTRIBUTE_AS,
        StudioResource::ATTRIBUTE_RESOURCE,
        StudioResource::ATTRIBUTE_STUDIO,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = StudioResource::TABLE;

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            StudioResource::ATTRIBUTE_STUDIO,
            StudioResource::ATTRIBUTE_RESOURCE,
        ];
    }

    /**
     * The event map for the model.
     *
     * Allows for object-based events for the native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => StudioResourceCreated::class,
        'deleted' => StudioResourceDeleted::class,
        'updated' => StudioResourceUpdated::class,
    ];

    /**
     * Gets the studio that owns the studio resource.
     *
     * @return BelongsTo<Studio, $this>
     */
    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class, StudioResource::ATTRIBUTE_STUDIO);
    }

    /**
     * Gets the resource that owns the studio resource.
     *
     * @return BelongsTo<ExternalResource, $this>
     */
    public function resource(): BelongsTo
    {
        return $this->belongsTo(ExternalResource::class, StudioResource::ATTRIBUTE_RESOURCE);
    }
}
