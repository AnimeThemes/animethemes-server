<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Enums\Models\User\EncodeType;
use App\Events\User\Encode\EncodeCreated;
use App\Events\User\Encode\EncodeUpdated;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Class Encode.
 *
 * @property EncodeType $type
 * @property User $user
 * @property int $user_id
 * @property Video $video
 * @property int $video_id
 */
class Encode extends BaseModel
{
    final public const TABLE = 'encodes';

    final public const ATTRIBUTE_ID = 'encode_id';
    final public const ATTRIBUTE_TYPE = 'type';
    final public const ATTRIBUTE_USER = 'user_id';
    final public const ATTRIBUTE_VIDEO = 'video_id';

    final public const RELATION_USER = 'user';
    final public const RELATION_VIDEO = 'video';

    /**
     * Is auditing disabled?
     *
     * @var bool
     */
    public static $auditingDisabled = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Encode::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Encode::ATTRIBUTE_ID;

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var class-string[]
     */
    protected $dispatchesEvents = [
        'created' => EncodeCreated::class,
        'updated' => EncodeUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Encode::ATTRIBUTE_TYPE,
        Encode::ATTRIBUTE_USER,
        Encode::ATTRIBUTE_VIDEO,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Encode::ATTRIBUTE_TYPE => EncodeType::class,
        ];
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return Str::of($this->user->getName())
            ->append(' ')
            ->append($this->video->getName())
            ->__toString();
    }

    /**
     * Get subtitle.
     */
    public function getSubtitle(): string
    {
        return '';
    }

    /**
     * Gets the user that owns the encode.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, Encode::ATTRIBUTE_USER);
    }

    /**
     * Gets the video that owns the encode.
     *
     * @return BelongsTo<Video, $this>
     */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, Encode::ATTRIBUTE_VIDEO);
    }
}
