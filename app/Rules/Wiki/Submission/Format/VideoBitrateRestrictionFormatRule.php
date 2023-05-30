<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

/**
 * Class VideoBitrateRestrictionFormatRule.
 */
class VideoBitrateRestrictionFormatRule extends SubmissionRule
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
        $format = $this->format();

        $video = Arr::first(
            $this->streams(),
            fn (array $stream) => Arr::get($stream, 'codec_type') === 'video'
        );

        $bitrate = intval(Arr::get($format, 'bit_rate'));
        $height = intval(Arr::get($video, 'height'));

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
