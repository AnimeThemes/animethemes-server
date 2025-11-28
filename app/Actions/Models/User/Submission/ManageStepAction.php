<?php

declare(strict_types=1);

namespace App\Actions\Models\User\Submission;

use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\SubmissionActionType;
use App\Models\User\Submission\SubmissionStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Date;

class ManageStepAction
{
    /**
     * Execute the action and approve the step.
     */
    public static function approveStep(SubmissionStep $step): void
    {
        match ($step->action) {
            SubmissionActionType::CREATE => static::approveCreate($step),
            SubmissionActionType::DELETE => static::approveDelete($step),
            SubmissionActionType::UPDATE => static::approveUpdate($step),
            SubmissionActionType::ATTACH => static::approveAttach($step),
            SubmissionActionType::DETACH => static::approveDetach($step),
            default => null,
        };
    }

    protected static function approveCreate(SubmissionStep $step): void
    {
        /** @var Model $model */
        $model = new $step->actionable_type;

        $model::query()->create($step->fields);

        static::markAsApproved($step);
    }

    protected static function approveDelete(SubmissionStep $step): void
    {
        $step->actionable->delete();

        static::markAsApproved($step);
    }

    protected static function approveUpdate(SubmissionStep $step): void
    {
        $step->actionable->update($step->fields);

        static::markAsApproved($step);
    }

    protected static function approveAttach(SubmissionStep $step): void
    {
        /** @var Pivot $pivot */
        $pivot = new $step->pivot;

        $pivot::query()->create($step->fields);

        static::markAsApproved($step);
    }

    protected static function approveDetach(SubmissionStep $step): void
    {
        /** @var Pivot $pivot */
        $pivot = new $step->pivot;

        $pivot::query()->where($step->fields)->delete();

        static::markAsApproved($step);
    }

    protected static function markAsApproved(SubmissionStep $step): void
    {
        static::updateStatus($step, ApprovableStatus::APPROVED);
    }

    protected static function markAsRejected(SubmissionStep $step): void
    {
        static::updateStatus($step, ApprovableStatus::REJECTED);
    }

    protected static function markAsPartiallyApproved(SubmissionStep $step): void
    {
        static::updateStatus($step, ApprovableStatus::PARTIALLY_APPROVED);
    }

    protected static function updateStatus(SubmissionStep $step, ApprovableStatus $status): void
    {
        $step->update([
            SubmissionStep::ATTRIBUTE_STATUS => $status->value,
            SubmissionStep::ATTRIBUTE_FINISHED_AT => Date::now(),
        ]);
    }
}
