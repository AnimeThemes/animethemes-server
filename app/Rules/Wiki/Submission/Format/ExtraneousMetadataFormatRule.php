<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;
use Laravel\Pennant\Feature;

class ExtraneousMetadataFormatRule extends SubmissionRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Feature::for(null)->active(FeatureConstants::IGNORE_ALL_FILE_VALIDATIONS)) {
            return;
        }

        $tags = $this->tags();

        if (collect($tags)->keys()->diff(['encoder', 'duration'])->isNotEmpty()) {
            $fail(__('validation.submission.format_extraneous_metadata'));
        }
    }
}
