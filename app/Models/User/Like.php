<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Models\Auth\User;
use App\Models\BaseModel;
use Database\Factories\User\LikeFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property User $user
 * @property int $user_id
 * @property Model $likeable
 * @property string $likeable_type
 * @property int $likeable_id
 *
 * @method static LikeFactory factory(...$parameters)
 */
#[Table(Like::TABLE, Like::ATTRIBUTE_ID)]
class Like extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'likes';

    final public const string ATTRIBUTE_ID = 'like_id';
    final public const string ATTRIBUTE_USER = 'user_id';

    final public const string ATTRIBUTE_LIKEABLE = 'likeable';
    final public const string ATTRIBUTE_LIKEABLE_TYPE = 'likeable_type';
    final public const string ATTRIBUTE_LIKEABLE_ID = 'likeable_id';

    final public const string RELATION_USER = 'user';
    final public const string RELATION_LIKEABLE = 'likeable';

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Like::ATTRIBUTE_LIKEABLE_TYPE => 'string',
            Like::ATTRIBUTE_LIKEABLE_ID => 'int',
            Like::ATTRIBUTE_USER => 'int',
        ];
    }

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        return '';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, Like::ATTRIBUTE_USER);
    }

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }
}
