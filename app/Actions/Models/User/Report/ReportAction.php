<?php

declare(strict_types=1);

namespace App\Actions\Models\User\Report;

use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\ReportActionType;
use App\Models\User\Report;
use App\Models\User\Report\ReportStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ReportAction
{
    /**
     * Create a report with the given steps.
     *
     * @param  ReportStep|ReportStep[]  $steps
     */
    public static function makeReport(ReportStep|array $steps, ?string $notes = null): Report
    {
        $report = Report::query()->create([
            Report::ATTRIBUTE_USER => Auth::id(),
            Report::ATTRIBUTE_STATUS => ApprovableStatus::PENDING->value,
            Report::ATTRIBUTE_NOTES => $notes,
        ]);

        $report->steps()->saveMany(Arr::wrap($steps));

        return $report;
    }

    /**
     * Create a report step to create a model.
     *
     * @param  class-string<Model>  $model
     * @param  array  $fields
     */
    public static function makeForCreate(string $model, array $fields): ReportStep
    {
        return static::makeFor(ReportActionType::CREATE, $model, $fields);
    }

    /**
     * Create a report step to edit a model.
     *
     * @param  array  $fields
     */
    public static function makeForUpdate(Model $model, array $fields): ReportStep
    {
        return static::makeFor(ReportActionType::UPDATE, $model, $fields);
    }

    /**
     * Create a report step to delete a model.
     */
    public static function makeForDelete(Model $model): ReportStep
    {
        return static::makeFor(ReportActionType::DELETE, $model);
    }

    /**
     * Create a report step to attach a model to another in a many-to-many relationship.
     *
     * @param  class-string<Pivot>  $pivot
     * @param  array  $fields
     */
    public static function makeForAttach(Model $foreign, Model $related, string $pivot, array $fields): ReportStep
    {
        return static::makeFor(ReportActionType::ATTACH, $foreign, $fields, $related, $pivot);
    }

    /**
     * Create a report step to detach a model from another in a many-to-many relationship.
     *
     * @param  array  $fields
     */
    public static function makeForDetach(Model $foreign, Model $related, Pivot $pivot, array $fields): ReportStep
    {
        return static::makeFor(ReportActionType::DETACH, $foreign, $fields, $related, $pivot);
    }

    /**
     * Create a report step for given action.
     *
     * @param  class-string<Model>|Model  $model
     * @param  array|null  $fields
     */
    protected static function makeFor(ReportActionType $action, Model|string $model, ?array $fields = null, ?Model $related = null, Pivot|string|null $pivot = null): ReportStep
    {
        return new ReportStep([
            ReportStep::ATTRIBUTE_ACTION => $action->value,
            ReportStep::ATTRIBUTE_ACTIONABLE_TYPE => $model instanceof Model ? Relation::getMorphAlias($model->getMorphClass()) : $model,
            ReportStep::ATTRIBUTE_ACTIONABLE_ID => $model instanceof Model ? $model->getKey() : null,
            ReportStep::ATTRIBUTE_FIELDS => Arr::where($fields, fn ($value, $key) => $model->isFillable($key)),
            ReportStep::ATTRIBUTE_STATUS => ApprovableStatus::PENDING->value,
            ReportStep::ATTRIBUTE_TARGET_TYPE => $related instanceof Model ? Relation::getMorphAlias($related->getMorphClass()) : null,
            ReportStep::ATTRIBUTE_TARGET_ID => $related instanceof Model ? $related->getKey() : null,
            ReportStep::ATTRIBUTE_PIVOT => $pivot instanceof Model ?Relation::getMorphAlias($pivot->getMorphClass()) : $pivot,
        ]);
    }
}
