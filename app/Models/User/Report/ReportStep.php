<?php

declare(strict_types=1);

namespace App\Models\User\Report;

use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\ReportActionType;
use App\Models\User\Report;
use Database\Factories\User\Report\ReportStepFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class ReportStep.
 *
 * @property ReportActionType|null $action
 * @property Model|null $actionable
 * @property string $actionable_type
 * @property int|null $actionable_id
 * @property array|null $fields
 * @property Carbon|null $finished_at
 * @property class-string<Model>|null $pivot_class
 * @property ApprovableStatus $status
 * @property Model|null $target
 * @property string|null $target_type
 * @property int|null $target_id
 *
 * @method static ReportStepFactory factory(...$parameters)
 */
class ReportStep extends Model
{
    use HasFactory;

    final public const TABLE = 'report_step';

    final public const ATTRIBUTE_ID = 'step_id';

    final public const ATTRIBUTE_ACTION = 'action';
    final public const ATTRIBUTE_ACTIONABLE = 'actionable';
    final public const ATTRIBUTE_ACTIONABLE_TYPE = 'actionable_type';
    final public const ATTRIBUTE_ACTIONABLE_ID = 'actionable_id';

    final public const ATTRIBUTE_TARGET = 'target';
    final public const ATTRIBUTE_TARGET_TYPE = 'target_type';
    final public const ATTRIBUTE_TARGET_ID = 'target_id';

    final public const ATTRIBUTE_PIVOT_CLASS = 'pivot_class';

    final public const ATTRIBUTE_REPORT = 'report_id';
    final public const ATTRIBUTE_FIELDS = 'fields';
    final public const ATTRIBUTE_FINISHED_AT = 'finished_at';
    final public const ATTRIBUTE_STATUS = 'status';

    final public const RELATION_REPORT = 'report';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        ReportStep::ATTRIBUTE_ACTION,
        ReportStep::ATTRIBUTE_ACTIONABLE_TYPE,
        ReportStep::ATTRIBUTE_ACTIONABLE_ID,
        ReportStep::ATTRIBUTE_FIELDS,
        ReportStep::ATTRIBUTE_PIVOT_CLASS,
        ReportStep::ATTRIBUTE_REPORT,
        ReportStep::ATTRIBUTE_STATUS,
        ReportStep::ATTRIBUTE_TARGET_TYPE,
        ReportStep::ATTRIBUTE_TARGET_ID,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ReportStep::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = ReportStep::ATTRIBUTE_ID;

    /**
     * Get the route key for the model.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return ReportStep::ATTRIBUTE_ID;
    }

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
     * Get subtitle.
     *
     * @return string
     */
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
            ReportStep::ATTRIBUTE_ACTION => ReportActionType::class,
            ReportStep::ATTRIBUTE_FIELDS => 'array',
            ReportStep::ATTRIBUTE_FINISHED_AT => 'datetime',
            ReportStep::ATTRIBUTE_STATUS => ApprovableStatus::class,
        ];
    }

    /**
     * Format the fields to display in the admin panel
     * TODO: Double check if this action can be refactored.
     *
     * @param  array|null  $fields
     * @return array
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

            if (is_object($casted) && enum_exists(get_class($casted))) {
                $newFields[$column] = $casted->localize();
                continue;
            }

            $newFields[$column] = $casted;
        }

        return $newFields;
    }

    /**
     * Get the actionable.
     *
     * @return MorphTo
     */
    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the target of the action.
     *
     * @return MorphTo
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the report the step belongs to.
     *
     * @return BelongsTo<Report, $this>
     */
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, ReportStep::ATTRIBUTE_REPORT);
    }
}
