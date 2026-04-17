<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Enums\Models\User\SubmissionStatus;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\User\Submission\SubmissionComparison;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property string|null $actionable_type
 * @property string|null $actionable_id
 * @property User|null $assignee
 * @property int|null $assignee_id
 * @property string[] $changes
 * @property Carbon|null $finished_at
 * @property bool $locked
 * @property string|null $notes
 * @property string|null $source
 * @property SubmissionStatus $status
 * @property string|null $submitted_type
 * @property string|null $submitted_id
 * @property User|null $user
 * @property int|null $user_id
 *
 * @method static Builder pending()
 */
#[Guarded([])]
#[Table(Submission::TABLE, Submission::ATTRIBUTE_ID)]
class Submission extends BaseModel
{
    final public const string TABLE = 'submissions';

    final public const string ATTRIBUTE_ID = 'id';
    final public const string ATTRIBUTE_ACTIONABLE_TYPE = 'actionable_type';
    final public const string ATTRIBUTE_ACTIONABLE_ID = 'actionable_id';
    final public const string ATTRIBUTE_ASSIGNEE = 'assignee_id';
    final public const string ATTRIBUTE_CHANGES = 'changes';
    final public const string ATTRIBUTE_FINISHED_AT = 'finished_at';
    final public const string ATTRIBUTE_LOCKED = 'locked';
    final public const string ATTRIBUTE_NOTES = 'notes';
    final public const string ATTRIBUTE_SOURCE = 'source';
    final public const string ATTRIBUTE_STATUS = 'status';
    final public const string ATTRIBUTE_SUBMITTED_TYPE = 'submitted_type';
    final public const string ATTRIBUTE_SUBMITTED_ID = 'submitted_id';
    final public const string ATTRIBUTE_USER = 'user_id';

    final public const string RELATION_ACTIONABLE = 'actionable';
    final public const string RELATION_ASSIGNEE = 'assignee';
    final public const string RELATION_SUBMITTED = 'submitted';
    final public const string RELATION_USER = 'user';

    // Anime
    final public const string RELATION_SUBMISSION_SYNONYMS = 'submissionSynonyms';
    final public const string RELATION_SUBMISSION_THEMES = 'submissionThemes';
    final public const string RELATION_SUBMISSION_RESOURCES = 'submissionResources';
    final public const string RELATION_SUBMISSION_SERIES = 'submissionSeries';
    final public const string RELATION_SUBMISSION_STUDIOS = 'submissionStudios';

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
            Submission::ATTRIBUTE_ASSIGNEE => 'int',
            Submission::ATTRIBUTE_CHANGES => 'array',
            Submission::ATTRIBUTE_FINISHED_AT => 'datetime',
            Submission::ATTRIBUTE_LOCKED => 'bool',
            Submission::ATTRIBUTE_NOTES => 'string',
            Submission::ATTRIBUTE_SOURCE => 'string',
            Submission::ATTRIBUTE_STATUS => SubmissionStatus::class,
            Submission::ATTRIBUTE_SUBMITTED_TYPE => 'string',
            Submission::ATTRIBUTE_SUBMITTED_ID => 'int',
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
     *
     * @return MorphTo<Model, $this>
     */
    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the submitted model that references the submission.
     *
     * @return MorphTo<Model, $this>
     */
    public function submitted(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the moderator that is working on the submission.
     *
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, Submission::ATTRIBUTE_ASSIGNEE);
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

    public function submissionSynonyms(): HasMany
    {
        return $this->hasMany(SubmissionComparison::class)
            ->where(SubmissionComparison::ATTRIBUTE_SUBMITTED_TYPE, 'submission_synonym');
    }

    public function submissionResources(): HasMany
    {
        return $this->hasMany(SubmissionComparison::class)
            ->where(SubmissionComparison::ATTRIBUTE_SUBMITTED_TYPE, 'submission_resource');
    }

    public function submissionSeries(): HasMany
    {
        return $this->hasMany(SubmissionComparison::class)
            ->where(SubmissionComparison::ATTRIBUTE_SUBMITTED_TYPE, 'submission_series');
    }

    public function submissionStudios(): HasMany
    {
        return $this->hasMany(SubmissionComparison::class)
            ->where(SubmissionComparison::ATTRIBUTE_SUBMITTED_TYPE, 'submission_studio');
    }
}
