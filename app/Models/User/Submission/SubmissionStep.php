<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\SubmissionActionType;
use App\Models\BaseModel;
use App\Models\User\Submission;
use Database\Factories\User\Submission\SubmissionStepFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property SubmissionActionType|null $action
 * @property Model|null $actionable
 * @property string $actionable_type
 * @property int|null $actionable_id
 * @property array|null $fields
 * @property Carbon|null $finished_at
 * @property class-string<Model>|null $pivot
 * @property Submission $submission
 * @property ApprovableStatus $status
 * @property Model|null $target
 * @property string|null $target_type
 * @property int|null $target_id
 *
 * @method static SubmissionStepFactory factory(...$parameters)
 */
class SubmissionStep extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'submission_step';

    final public const string ATTRIBUTE_ID = 'step_id';

    final public const string ATTRIBUTE_ACTION = 'action';
    final public const string ATTRIBUTE_ACTIONABLE_TYPE = 'actionable_type';
    final public const string ATTRIBUTE_ACTIONABLE_ID = 'actionable_id';

    final public const string ATTRIBUTE_TARGET_TYPE = 'target_type';
    final public const string ATTRIBUTE_TARGET_ID = 'target_id';

    final public const string ATTRIBUTE_PIVOT = 'pivot';

    final public const string ATTRIBUTE_SUBMISSION = 'submission_id';
    final public const string ATTRIBUTE_FIELDS = 'fields';
    final public const string ATTRIBUTE_FINISHED_AT = 'finished_at';
    final public const string ATTRIBUTE_STATUS = 'status';

    final public const string RELATION_ACTIONABLE = 'actionable';
    final public const string RELATION_TARGET = 'target';
    final public const string RELATION_SUBMISSION = 'submission';

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
        SubmissionStep::ATTRIBUTE_ACTION,
        SubmissionStep::ATTRIBUTE_ACTIONABLE_TYPE,
        SubmissionStep::ATTRIBUTE_ACTIONABLE_ID,
        SubmissionStep::ATTRIBUTE_FIELDS,
        SubmissionStep::ATTRIBUTE_PIVOT,
        SubmissionStep::ATTRIBUTE_SUBMISSION,
        SubmissionStep::ATTRIBUTE_STATUS,
        SubmissionStep::ATTRIBUTE_TARGET_TYPE,
        SubmissionStep::ATTRIBUTE_TARGET_ID,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = SubmissionStep::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = SubmissionStep::ATTRIBUTE_ID;

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
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
            SubmissionStep::ATTRIBUTE_ACTION => SubmissionActionType::class,
            SubmissionStep::ATTRIBUTE_FIELDS => 'array',
            SubmissionStep::ATTRIBUTE_FINISHED_AT => 'datetime',
            SubmissionStep::ATTRIBUTE_STATUS => ApprovableStatus::class,
        ];
    }

    /**
     * Format the fields to display in the admin panel
     * TODO: Double check if this action can be refactored.
     */
    public function formatFields(?array $fields = null): array
    {
        $newFields = [];
        $actionable = $this->actionable;

        if ($fields === null) {
            $fields = $this->fields;
        }

        foreach ($fields as $column => $value) {
            $casted = $actionable->hasCast($column)
                ? $actionable->castAttribute($column, $value)
                : $value;

            if (is_object($casted) && enum_exists($casted::class)) {
                $newFields[$column] = $casted->localize();
                continue;
            }

            $newFields[$column] = $casted;
        }

        return $newFields;
    }

    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<Submission, $this>
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, SubmissionStep::ATTRIBUTE_SUBMISSION);
    }
}
