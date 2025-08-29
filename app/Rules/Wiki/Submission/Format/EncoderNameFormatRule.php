<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;
use Laravel\Pennant\Feature;

class EncoderNameFormatRule extends SubmissionRule
{
    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (Feature::for(null)->active(FeatureConstants::IGNORE_ALL_FILE_VALIDATIONS)) {
            return;
        }

        $tags = $this->tags();

        $encoder = Arr::get($tags, 'encoder');

        if (! Str::startsWith($encoder, 'Lavf')) {
            $fail(__('validation.submission.format_encoder_name'));
        }
    }
}
