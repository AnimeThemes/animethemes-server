<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Video;

use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;

/**
 * Class VideoColorTransferStreamRule.
 */
class VideoColorTransferStreamRule extends SubmissionRule
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

        $colorTransfers = ['bt709', 'smpte170m', 'bt470bg'];

        return $video !== null && in_array($video->get('color_transfer'), $colorTransfers);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.video_color_transfer');
    }
}
