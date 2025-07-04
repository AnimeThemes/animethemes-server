<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Contracts\Models\Nameable;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Like.
 *
 * @property User $user
 * @property int $user_id
 * @property Model $likeable
 * @property string $likeable_type
 * @property int $likeable_id
 */
class Like extends Model implements Nameable
{
    final public const TABLE = 'likes';

    final public const ATTRIBUTE_ID = 'like_id';
    final public const ATTRIBUTE_USER = 'user_id';

    final public const ATTRIBUTE_LIKEABLE = 'likeable';
    final public const ATTRIBUTE_LIKEABLE_TYPE = 'likeable_type';
    final public const ATTRIBUTE_LIKEABLE_ID = 'likeable_id';

    final public const RELATION_USER = 'user';
    final public const RELATION_LIKEABLE = 'likeable';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Like::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Like::ATTRIBUTE_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Like::ATTRIBUTE_USER,
        Like::ATTRIBUTE_LIKEABLE_TYPE,
        Like::ATTRIBUTE_LIKEABLE_ID,
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return strval($this->getKey());
    }

    /**
     * Gets the user that owns the like.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, Like::ATTRIBUTE_USER);
    }

    /**
     * Gets the video that owns the like.
     *
     * @return MorphTo
     */
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}
