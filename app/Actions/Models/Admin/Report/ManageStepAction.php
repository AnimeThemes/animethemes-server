<?php

declare(strict_types=1);

namespace App\Actions\Models\Admin\Report;

use App\Enums\Models\Admin\ApprovableStatus;
use App\Enums\Models\Admin\ReportActionType;
use App\Models\Admin\Report\ReportStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Date;

/**
 * Class ManageStepAction.
 */
class ManageStepAction
{
    /**
     * Execute the action and approve the step.
     *
     * @param  ReportStep  $step
     * @return void
     */
    public static function approveStep(ReportStep $step): void
    {
        match ($step->action) {
            ReportActionType::CREATE => static::approveCreate($step),
            ReportActionType::DELETE => static::approveDelete($step),
            ReportActionType::UPDATE => static::approveUpdate($step),
            ReportActionType::ATTACH => static::approveAttach($step),
            ReportActionType::DETACH => static::approveDetach($step),
            default => null,
        };
    }

    /**
     * Create the model according the step.
     *
     * @param  ReportStep  $step
     * @return void
     */
    protected static function approveCreate(ReportStep $step): void
    {
        /** @var Model $model */
        $model = new $step->actionable_type;

        $model::query()->create($step->fields);

        static::markAsApproved($step);
    }

    /**
     * Delete the model according the step.
     *
     * @param  ReportStep  $step
     * @return void
     */
    protected static function approveDelete(ReportStep $step): void
    {
        $step->actionable->delete();

        static::markAsApproved($step);
    }

    /**
     * Update the model according the step.
     *
     * @param  ReportStep  $step
     * @return void
     */
    protected static function approveUpdate(ReportStep $step): void
    {
        $step->actionable->update($step->fields);

        static::markAsApproved($step);
    }

    /**
     * Attach a model to another according the step.
     *
     * @param  ReportStep  $step
     * @return void
     */
    protected static function approveAttach(ReportStep $step): void
    {
        /** @var Pivot $pivot */
        $pivot = new $step->pivot_class;

        $pivot::query()->create($step->fields);

        static::markAsApproved($step);
    }

    /**
     * Det=tach a model from another according the step.
     *
     * @param  ReportStep  $step
     * @return void
     */
    protected static function approveDetach(ReportStep $step): void
    {
        /** @var Pivot $pivot */
        $pivot = new $step->pivot_class;

        $pivot::query()->where($step->fields)->delete();

        static::markAsApproved($step);
    }

    /**
     * Approve the step.
     *
     * @param  ReportStep  $step
     * @return void
     */
    protected static function markAsApproved(ReportStep $step): void
    {
        static::updateStatus($step, ApprovableStatus::APPROVED);
    }

    /**
     * Reject the step.
     *
     * @param  ReportStep  $step
     * @return void
     */
    protected static function markAsRejected(ReportStep $step): void
    {
        static::updateStatus($step, ApprovableStatus::REJECTED);
    }

    /**
     * Approve partially the step.
     *
     * @param  ReportStep  $step
     * @return void
     */
    protected static function markAsPartiallyApproved(ReportStep $step): void
    {
        static::updateStatus($step, ApprovableStatus::PARTIALLY_APPROVED);
    }

    /**
     * Update the status of the step.
     *
     * @param  ReportStep  $step
     * @param  ApprovableStatus  $status
     * @return void
     */
    protected static function updateStatus(ReportStep $step, ApprovableStatus $status): void
    {
        $step->query()->update([
            ReportStep::ATTRIBUTE_STATUS => $status->value,
            ReportStep::ATTRIBUTE_FINISHED_AT => Date::now(),
        ]);
    }
}
