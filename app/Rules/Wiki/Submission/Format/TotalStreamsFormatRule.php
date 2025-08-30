<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;
use Laravel\Pennant\Feature;

class TotalStreamsFormatRule extends SubmissionRule
{
    public function __construct(protected readonly int $expected) {}

    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Feature::for(null)->active(FeatureConstants::IGNORE_ALL_FILE_VALIDATIONS)) {
            return;
        }

        $streams = $this->streams();

        if (count($streams) !== $this->expected) {
            $fail(__('validation.submission.format_total_streams'));
        }
    }
}
