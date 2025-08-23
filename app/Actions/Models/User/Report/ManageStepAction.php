<?php

declare(strict_types=1);

namespace App\Actions\Models\User\Report;

use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\ReportActionType;
use App\Models\User\Report\ReportStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Date;

class ManageStepAction
{
    /**
     * Execute the action and approve the step.
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
     */
    protected static function approveDelete(ReportStep $step): void
    {
        $step->actionable->delete();

        static::markAsApproved($step);
    }

    /**
     * Update the model according the step.
     */
    protected static function approveUpdate(ReportStep $step): void
    {
        $step->actionable->update($step->fields);

        static::markAsApproved($step);
    }

    /**
     * Attach a model to another according the step.
     */
    protected static function approveAttach(ReportStep $step): void
    {
        /** @var Pivot $pivot */
        $pivot = new $step->pivot;

        $pivot::query()->create($step->fields);

        static::markAsApproved($step);
    }

    /**
     * Detach a model from another according the step.
     */
    protected static function approveDetach(ReportStep $step): void
    {
        /** @var Pivot $pivot */
        $pivot = new $step->pivot;

        $pivot::query()->where($step->fields)->delete();

        static::markAsApproved($step);
    }

    /**
     * Approve the step.
     */
    protected static function markAsApproved(ReportStep $step): void
    {
        static::updateStatus($step, ApprovableStatus::APPROVED);
    }

    /**
     * Reject the step.
     */
    protected static function markAsRejected(ReportStep $step): void
    {
        static::updateStatus($step, ApprovableStatus::REJECTED);
    }

    /**
     * Approve partially the step.
     */
    protected static function markAsPartiallyApproved(ReportStep $step): void
    {
        static::updateStatus($step, ApprovableStatus::PARTIALLY_APPROVED);
    }

    /**
     * Update the status of the step.
     */
    protected static function updateStatus(ReportStep $step, ApprovableStatus $status): void
    {
        $step->update([
            ReportStep::ATTRIBUTE_STATUS => $status->value,
            ReportStep::ATTRIBUTE_FINISHED_AT => Date::now(),
        ]);
    }
}
