<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Enums\Models\User\SubmissionComparisonAction;
use App\Models\User\Submission;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Table(SubmissionComparison::TABLE, SubmissionComparison::ATTRIBUTE_ID)]
#[WithoutTimestamps]
class SubmissionComparison extends Model
{
    final public const string TABLE = 'submissions_comparison';

    final public const string ATTRIBUTE_ID = 'id';
    final public const string ATTRIBUTE_ACTION = 'action';
    final public const string ATTRIBUTE_ACTIONABLE_TYPE = 'actionable_type';
    final public const string ATTRIBUTE_ACTIONABLE_ID = 'actionable_id';
    final public const string ATTRIBUTE_SUBMITTED_TYPE = 'submitted_type';
    final public const string ATTRIBUTE_SUBMITTED_ID = 'submitted_id';
    final public const string ATTRIBUTE_SUBMITTED_PIVOT_TYPE = 'submitted_pivot_type';
    final public const string ATTRIBUTE_SUBMITTED_PIVOT_ID = 'submitted_pivot_id';
    final public const string ATTRIBUTE_SUBMISSION = 'submission_id';

    final public const string RELATION_SUBMITTED = 'submitted';
    final public const string RELATION_SUBMITTED_PIVOT = 'submittedPivot';
    final public const string RELATION_SUBMISSION = 'submission';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            SubmissionComparison::ATTRIBUTE_ACTION => SubmissionComparisonAction::class,
            SubmissionComparison::ATTRIBUTE_ACTIONABLE_TYPE => 'string',
            SubmissionComparison::ATTRIBUTE_ACTIONABLE_ID => 'int',
            SubmissionComparison::ATTRIBUTE_SUBMITTED_TYPE => 'string',
            SubmissionComparison::ATTRIBUTE_SUBMITTED_ID => 'int',
            SubmissionComparison::ATTRIBUTE_SUBMITTED_PIVOT_TYPE => 'string',
            SubmissionComparison::ATTRIBUTE_SUBMITTED_PIVOT_ID => 'int',
            SubmissionComparison::ATTRIBUTE_SUBMISSION => 'int',
        ];
    }

    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function submitted(): MorphTo
    {
        return $this->morphTo();
    }

    public function submittedPivot(): MorphTo
    {
        return $this->morphTo('submitted_pivot');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, SubmissionComparison::ATTRIBUTE_SUBMISSION);
    }
}
