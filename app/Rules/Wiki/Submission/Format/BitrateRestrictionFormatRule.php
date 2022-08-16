<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

/**
 * Class BitrateRestrictionFormatRule.
 */
class BitrateRestrictionFormatRule extends SubmissionRule
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

        $video = $this->streams()
            ->videos()
            ->first();

        $bitrate = intval(Arr::get($format, 'bit_rate'));
        $height = $video->getDimensions()->getHeight();

        // Linear approximation of egregious bitrate by resolution
        return $bitrate < $height * 7100 + 475000;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.format_bitrate_restriction');
    }
}
