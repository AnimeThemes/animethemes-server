<?php

declare(strict_types=1);

namespace App\Actions\Submission;

use App\Enums\Models\User\SubmissionStatus;
use App\Models\User\Submission;
use Illuminate\Support\Facades\Auth;

abstract class SubmissionAction
{
    /**
     * Mark the submission as approved.
     */
    public static function markAsApproved(Submission $submission, string $notes): void
    {
        static::markAs(SubmissionStatus::APPROVED, $submission, $notes);
    }

    /**
     * Mark the submission as changes requested.
     */
    public static function markAsChangesRequested(Submission $submission, string $notes): void
    {
        static::markAs(SubmissionStatus::CHANGES_REQUESTED, $submission, $notes);
    }

    /**
     * Mark the submission as partially accepted.
     */
    public static function markAsPartiallyAccepted(Submission $submission, string $notes): void
    {
        static::markAs(SubmissionStatus::PARTIALLY_APPROVED, $submission, $notes);
    }

    /**
     * Mark the submission as partially rejected.
     */
    public static function markAsRejected(Submission $submission, string $notes): void
    {
        static::markAs(SubmissionStatus::REJECTED, $submission, $notes);
    }

    /**
     * Mark the submission as $status.
     */
    public static function markAs(SubmissionStatus $status, Submission $submission, string $notes): void
    {
        $submission->update([
            Submission::ATTRIBUTE_LOCKED => true,
            Submission::ATTRIBUTE_ASSIGNEE => Auth::id(),
            Submission::ATTRIBUTE_NOTES => $notes,
            Submission::ATTRIBUTE_STATUS => $status,
        ]);
    }
}
