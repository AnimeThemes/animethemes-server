<?php

declare(strict_types=1);

namespace App\Models\Admin\Report;

use App\Enums\Models\Admin\ApprovableStatus;
use App\Enums\Models\Admin\ReportActionType;
use App\Models\Admin\Report;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Arr;

/**
 * Class ReportStep.
 *
 * @property ReportActionType|null $action
 * @property Model|null $actionable
 * @property array|null $fields
 * @property Carbon|null $finished_at
 * @property class-string<Model>|null $pivot_class
 * @property ApprovableStatus $status
 * @property Model|null $target
 */
class ReportStep extends Model
{
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
     * Create a report step to create a model.
     *
     * @param  class-string<Model>  $model
     * @param  array  $fields
     * @return static
     */
    public static function makeForCreate(string $model, array $fields, Report $report): static
    {
        return static::makeFor(ReportActionType::CREATE, $model, $fields, report: $report);
    }

    /**
     * Create a report step to edit a model.
     *
     * @param  Model  $model
     * @param  array  $fields
     * @param  Report|null  $report
     * @return static
     */
    public static function makeForUpdate(Model $model, array $fields, ?Report $report = null): static
    {
        return static::makeFor(ReportActionType::UPDATE, $model, $fields, report: $report);
    }

    /**
     * Create a report step to delete a model.
     *
     * @param  Model  $model
     * @param  Report|null  $report
     * @return static
     */
    public static function makeForDelete(Model $model, ?Report $report = null): static
    {
        return static::makeFor(ReportActionType::DELETE, $model, report: $report);
    }

    /**
     * Create a report step to attach a model to another in a many-to-many relationship.
     *
     * @param  Model  $foreign
     * @param  Model  $related
     * @param  class-string<Pivot>  $pivot
     * @param  array  $fields
     * @return static
     */
    public static function makeForAttach(Model $foreign, Model $related, string $pivot, array $fields, Report $report): static
    {
        return static::makeFor(ReportActionType::ATTACH, $foreign, $fields, $related, $pivot, report: $report);
    }

    /**
     * Create a report step to detach a model from another in a many-to-many relationship.
     *
     * @param  Model  $foreign
     * @param  Model  $related
     * @param  Pivot  $pivot
     * @param  array  $fields
     * @return static
     */
    public static function makeForDetach(Model $foreign, Model $related, Pivot $pivot, array $fields): static
    {
        return static::makeFor(ReportActionType::DETACH, $foreign, $fields, $related, $pivot);
    }

    /**
     * Create a report step for given action.
     *
     * @param  ReportActionType  $action
     * @param  class-string<Model>|Model  $model
     * @param  array|null  $fields
     * @param  Model|null  $related
     * @param  Pivot|null  $pivot
     * @return static
     */
    protected static function makeFor(ReportActionType $action, Model|string $model, ?array $fields = null, ?Model $related = null, Pivot|string|null $pivot = null, ?Report $report = null): static
    {
        return ReportStep::query()
            ->create([
                ReportStep::ATTRIBUTE_REPORT => $report?->getKey(),
                ReportStep::ATTRIBUTE_ACTION => $action->value,
                ReportStep::ATTRIBUTE_ACTIONABLE_TYPE => $model instanceof Model ? $model->getMorphClass() : $model,
                ReportStep::ATTRIBUTE_ACTIONABLE_ID => $model instanceof Model ? $model->getKey() : null,
                ReportStep::ATTRIBUTE_FIELDS => Arr::where($fields, fn ($value, $key) => $model->isFillable($key)),
                ReportStep::ATTRIBUTE_STATUS => ApprovableStatus::PENDING->value,
                ReportStep::ATTRIBUTE_TARGET_TYPE => $related instanceof Model ? $related->getMorphClass() : null,
                ReportStep::ATTRIBUTE_TARGET_ID => $related instanceof Model ? $related->getKey() : null,
                ReportStep::ATTRIBUTE_PIVOT_CLASS => $pivot instanceof Model ? $pivot->getMorphClass() : $pivot,
            ]);
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
