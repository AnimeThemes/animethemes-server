<?php

declare(strict_types=1);

namespace App\Pivots\Morph;

use App\Contracts\Models\HasImages;
use App\Contracts\Models\Nameable;
use App\Events\Pivot\Morph\Imageable\ImageableCreated;
use App\Events\Pivot\Morph\Imageable\ImageableDeleted;
use App\Events\Pivot\Morph\Imageable\ImageableUpdated;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pivots\BaseMorphPivot;
use Database\Factories\Pivots\Morph\ImageableFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Model&HasImages&Nameable $imageable
 * @property string $imageable_type
 * @property int $imageable_id
 * @property int $depth
 * @property Image $image
 * @property int $image_id
 *
 * @method static ImageableFactory factory(...$parameters)
 */
class Imageable extends BaseMorphPivot
{
    final public const string TABLE = 'imageables';

    final public const string ATTRIBUTE_DEPTH = 'depth';
    final public const string ATTRIBUTE_IMAGE = 'image_id';
    final public const string ATTRIBUTE_IMAGEABLE_TYPE = 'imageable_type';
    final public const string ATTRIBUTE_IMAGEABLE_ID = 'imageable_id';

    final public const string RELATION_IMAGE = 'image';
    final public const string RELATION_IMAGEABLE = 'imageable';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Imageable::TABLE;

    /**
     * The models that have images.
     *
     * @var class-string<Model&HasImages>[]
     */
    public static $imageables = [
        Playlist::class,
        Anime::class,
        Artist::class,
        Studio::class,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => ImageableCreated::class,
        'deleted' => ImageableDeleted::class,
        'updated' => ImageableUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Imageable::ATTRIBUTE_DEPTH,
        Imageable::ATTRIBUTE_IMAGE,
        Imageable::ATTRIBUTE_IMAGEABLE_TYPE,
        Imageable::ATTRIBUTE_IMAGEABLE_ID,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Imageable::ATTRIBUTE_DEPTH => 'int',
        ];
    }

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    protected function getPrimaryKeys(): array
    {
        return [
            Imageable::ATTRIBUTE_IMAGE,
            Imageable::ATTRIBUTE_IMAGEABLE_TYPE,
            Imageable::ATTRIBUTE_IMAGEABLE_ID,
        ];
    }

    /**
     * Gets the image that owns the imageable.
     *
     * @return BelongsTo<Image, $this>
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, Imageable::ATTRIBUTE_IMAGE);
    }

    /**
     * Gets the model that owns the imageable.
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo(Imageable::RELATION_IMAGEABLE);
    }
}
