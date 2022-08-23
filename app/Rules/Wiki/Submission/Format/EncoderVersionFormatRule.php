<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Constants\Config\VideoConstants;
use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

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
        $format = $this->format()->all();

        $tags = Arr::get($format, 'tags');

        $tags = array_change_key_case($tags);

        $encoder = Arr::get($tags, 'encoder');

        return version_compare($encoder, Config::get(VideoConstants::ENCODER_VERSION_QUALIFIED), '>=');
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
