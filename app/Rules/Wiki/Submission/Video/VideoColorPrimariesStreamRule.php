<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Video;

use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;

/**
 * Class VideoColorPrimariesStreamRule.
 */
class VideoColorPrimariesStreamRule extends SubmissionRule
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
        $video = $this->streams()
            ->videos()
            ->first();

        $colorPrimaries = ['bt709', 'smpte170m', 'bt470bg'];

        return $video !== null && in_array($video->get('color_primaries'), $colorPrimaries);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.video_color_primaries');
    }
}
