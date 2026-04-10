<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Enums\Models\User\SubmissionStatus;
use App\Models\Auth\User;
use App\Models\BaseModel;
use Database\Factories\User\SubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property string|null $actionable_type
 * @property string|null $actionable_id
 * @property array $fields
 * @property Carbon|null $finished_at
 * @property bool $locked
 * @property string|null $notes
 * @property User|null $moderator
 * @property int|null $moderator_id
 * @property string|null $moderator_notes
 * @property SubmissionStatus $status
 * @property User|null $user
 * @property int|null $user_id
 *
 * @method static Builder pending()
 * @method static SubmissionFactory factory(...$parameters)
 */
#[Table(Submission::TABLE, Submission::ATTRIBUTE_ID)]
class Submission extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'submissions';

    final public const string ATTRIBUTE_ID = 'id';
    final public const string ATTRIBUTE_ACTIONABLE_TYPE = 'actionable_type';
    final public const string ATTRIBUTE_ACTIONABLE_ID = 'actionable_id';
    final public const string ATTRIBUTE_FIELDS = 'fields';
    final public const string ATTRIBUTE_FINISHED_AT = 'finished_at';
    final public const string ATTRIBUTE_LOCKED = 'locked';
    final public const string ATTRIBUTE_NOTES = 'notes';
    final public const string ATTRIBUTE_MODERATOR = 'moderator_id';
    final public const string ATTRIBUTE_MODERATOR_NOTES = 'moderator_notes';
    final public const string ATTRIBUTE_STATUS = 'status';
    final public const string ATTRIBUTE_TYPE = 'type';
    final public const string ATTRIBUTE_USER = 'user_id';

    final public const string RELATION_ACTIONABLE = 'actionable';
    final public const string RELATION_MODERATOR = 'moderator';
    final public const string RELATION_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Submission::ATTRIBUTE_ACTIONABLE_TYPE,
        Submission::ATTRIBUTE_ACTIONABLE_ID,
        Submission::ATTRIBUTE_FIELDS,
        Submission::ATTRIBUTE_FINISHED_AT,
        Submission::ATTRIBUTE_LOCKED,
        Submission::ATTRIBUTE_NOTES,
        Submission::ATTRIBUTE_MODERATOR,
        Submission::ATTRIBUTE_MODERATOR_NOTES,
        Submission::ATTRIBUTE_STATUS,
        Submission::ATTRIBUTE_TYPE,
        Submission::ATTRIBUTE_USER,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Submission::ATTRIBUTE_ACTIONABLE_TYPE => 'string',
            Submission::ATTRIBUTE_ACTIONABLE_ID => 'int',
            Submission::ATTRIBUTE_FIELDS => 'string',
            Submission::ATTRIBUTE_FINISHED_AT => 'datetime',
            Submission::ATTRIBUTE_LOCKED => 'bool',
            Submission::ATTRIBUTE_NOTES => 'string',
            Submission::ATTRIBUTE_MODERATOR => 'int',
            Submission::ATTRIBUTE_MODERATOR_NOTES => 'string',
            Submission::ATTRIBUTE_STATUS => SubmissionStatus::class,
            Submission::ATTRIBUTE_TYPE => 'string',
            Submission::ATTRIBUTE_USER => 'int',
        ];
    }

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        if ($user = $this->user) {
            return $user->getName();
        }

        return strval($this->getKey());
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Scope a query to only include pending submissions.
     */
    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where(Submission::ATTRIBUTE_STATUS, SubmissionStatus::PENDING->value);
    }

    /**
     * Get the model that references the submission.
     */
    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the moderator that is working on the submission.
     *
     * @return BelongsTo<User, $this>
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, Submission::ATTRIBUTE_MODERATOR);
    }

    /**
     * Get the user that made the submission.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, Submission::ATTRIBUTE_USER);
    }
}
