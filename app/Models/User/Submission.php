<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Enums\Models\User\ApprovableStatus;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\User\Submission\SubmissionStep;
use Database\Factories\User\SubmissionFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property Carbon|null $finished_at
 * @property User|null $moderator
 * @property int|null $moderator_id
 * @property string|null $moderator_notes
 * @property string|null $notes
 * @property ApprovableStatus $status
 * @property Collection<int, SubmissionStep> $steps
 * @property User|null $user
 * @property int|null $user_id
 *
 * @method static Builder pending()
 * @method static SubmissionFactory factory(...$parameters)
 */
class Submission extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'submissions';

    final public const string ATTRIBUTE_ID = 'submission_id';
    final public const string ATTRIBUTE_FINISHED_AT = 'finished_at';
    final public const string ATTRIBUTE_MODERATOR = 'moderator_id';
    final public const string ATTRIBUTE_MODERATOR_NOTES = 'moderator_notes';
    final public const string ATTRIBUTE_NOTES = 'notes';
    final public const string ATTRIBUTE_STATUS = 'status';
    final public const string ATTRIBUTE_USER = 'user_id';

    final public const string RELATION_MODERATOR = 'moderator';
    final public const string RELATION_STEPS = 'steps';
    final public const string RELATION_USER = 'user';

    /**
     * Is auditing disabled?
     *
     * @var bool
     */
    public static $auditingDisabled = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Submission::ATTRIBUTE_FINISHED_AT,
        Submission::ATTRIBUTE_MODERATOR,
        Submission::ATTRIBUTE_MODERATOR_NOTES,
        Submission::ATTRIBUTE_NOTES,
        Submission::ATTRIBUTE_STATUS,
        Submission::ATTRIBUTE_USER,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Submission::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Submission::ATTRIBUTE_ID;

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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Submission::ATTRIBUTE_FINISHED_AT => 'datetime',
            Submission::ATTRIBUTE_STATUS => ApprovableStatus::class,
        ];
    }

    /**
     * Scope a query to only include pending submissions.
     */
    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where(Submission::ATTRIBUTE_STATUS, ApprovableStatus::PENDING->value);
    }

    /**
     * @return HasMany<SubmissionStep, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(SubmissionStep::class, SubmissionStep::ATTRIBUTE_SUBMISSION);
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
