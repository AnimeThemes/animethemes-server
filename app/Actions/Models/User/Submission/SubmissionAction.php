<?php

declare(strict_types=1);

namespace App\Actions\Models\User\Submission;

use App\Enums\Models\User\SubmissionStatus;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionVirtual;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

abstract class SubmissionAction
{
    /**
     * Mark the submission as approved.
     */
    public static function markAsApproved(Submission $submission, string $modNotes): void
    {
        static::markAs(SubmissionStatus::APPROVED, $submission, $modNotes);
    }

    /**
     * Mark the submission as changes requested.
     */
    public static function markAsChangesRequested(Submission $submission, string $modNotes): void
    {
        static::markAs(SubmissionStatus::CHANGES_REQUESTED, $submission, $modNotes);
    }

    /**
     * Mark the submission as partially accepted.
     */
    public static function markAsPartiallyAccepted(Submission $submission, string $modNotes): void
    {
        static::markAs(SubmissionStatus::PARTIALLY_APPROVED, $submission, $modNotes);
    }

    /**
     * Mark the submission as partially rejected.
     */
    public static function markAsRejected(Submission $submission, string $modNotes): void
    {
        static::markAs(SubmissionStatus::REJECTED, $submission, $modNotes);
    }

    /**
     * Mark the submission as $status.
     */
    public static function markAs(SubmissionStatus $status, Submission $submission, string $modNotes): void
    {
        $submission->update([
            Submission::ATTRIBUTE_LOCKED => true,
            Submission::ATTRIBUTE_MODERATOR => Auth::id(),
            Submission::ATTRIBUTE_MODERATOR_NOTES => $modNotes,
            Submission::ATTRIBUTE_STATUS => $status,
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
