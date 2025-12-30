<?php

declare(strict_types=1);

namespace App\Actions\Models\User\Submission;

use App\Enums\Models\User\SubmissionStatus;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStage;
use App\Models\User\Submission\SubmissionVirtual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

abstract class SubmissionAction
{
    /**
     * Mark the submission as approved.
     */
    public static function markAsApproved(SubmissionStage $stage, string $modNotes): void
    {
        static::markAs(SubmissionStatus::APPROVED, $stage, $modNotes);
    }

    /**
     * Mark the submission as changes requested.
     */
    public static function markAsChangesRequested(SubmissionStage $stage, string $modNotes): void
    {
        static::markAs(SubmissionStatus::CHANGES_REQUESTED, $stage, $modNotes);
    }

    /**
     * Mark the submission as partially accepted.
     */
    public static function markAsPartiallyAccepted(SubmissionStage $stage, string $modNotes): void
    {
        static::markAs(SubmissionStatus::PARTIALLY_APPROVED, $stage, $modNotes);
    }

    /**
     * Mark the submission as partially rejected.
     */
    public static function markAsRejected(SubmissionStage $stage, string $modNotes): void
    {
        static::markAs(SubmissionStatus::REJECTED, $stage, $modNotes);
    }

    /**
     * Mark the submission as $status.
     */
    public static function markAs(SubmissionStatus $status, SubmissionStage $stage, string $modNotes): void
    {
        $stage->update([
            SubmissionStage::ATTRIBUTE_MODERATOR => Auth::id(),
            SubmissionStage::ATTRIBUTE_MODERATOR_NOTES => $modNotes,
        ]);

        $stage->submission->update([
            Submission::ATTRIBUTE_LOCKED => true,
            Submission::ATTRIBUTE_STATUS => $status->value,
        ]);
    }

    /**
     * Use the virtual models to create real ones.
     *
     * @param  array<string, mixed>  $fields
     * @return array<string, mixed>
     */
    protected function createVirtuals(array $fields): array
    {
        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $fields[$key] = $this->createVirtuals($value);
                continue;
            }

            if (Str::endsWith($key, '_id') && is_numeric($value) && (int) ($id = $value) < 0) {
                /** @var SubmissionVirtual $virtual */
                $virtual = SubmissionVirtual::query()
                    ->whereKey($id * -1)
                    ->first();

                $virtual->update([SubmissionVirtual::ATTRIBUTE_EXISTS => true]);

                $fields[$key] = $virtual->model::query()
                    ->create($virtual->getAttribute(SubmissionVirtual::ATTRIBUTE_FIELDS))
                    ->getKey();
            }
        }

        return $fields;
    }
}
