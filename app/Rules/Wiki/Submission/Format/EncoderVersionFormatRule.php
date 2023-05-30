<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Constants\FeatureConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Laravel\Pennant\Feature;

/**
 * Class EncoderVersionFormatRule.
 */
class EncoderVersionFormatRule extends SubmissionRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  UploadedFile  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $tags = $this->tags();

        $encoder = Arr::get($tags, 'encoder');

        return version_compare($encoder, Feature::for(null)->value(FeatureConstants::REQUIRED_ENCODER_VERSION), '>=');
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.format_encoder_version');
    }
}
