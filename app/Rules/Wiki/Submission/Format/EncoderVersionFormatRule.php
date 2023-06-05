<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Translation\PotentiallyTranslatedString;
use Laravel\Pennant\Feature;

/**
 * Class EncoderVersionFormatRule.
 */
class EncoderVersionFormatRule extends SubmissionRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $tags = $this->tags();

        $encoder = Arr::get($tags, 'encoder');

        if (version_compare($encoder, Feature::for(null)->value(FeatureConstants::REQUIRED_ENCODER_VERSION), '<')) {
            $fail(__('validation.submission.format_encoder_version'));
        }
    }
}
