<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Video;

use App\Rules\Wiki\Submission\SubmissionRule;
use FFMpeg\FFProbe\DataMapping\Stream;
use Illuminate\Http\UploadedFile;

/**
 * Class VideoIndexStreamRule.
 */
class VideoIndexStreamRule extends SubmissionRule
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
        $streams = $this->streams()->all();

        return collect($streams)->contains(fn (Stream $stream) => $stream->isVideo() && $stream->get('index') === 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.video_index');
    }
}
