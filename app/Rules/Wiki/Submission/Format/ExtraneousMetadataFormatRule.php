<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

/**
 * Class ExtraneousMetadataFormatRule.
 */
class ExtraneousMetadataFormatRule extends SubmissionRule
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

        return collect($tags)->keys()->diff(['encoder', 'duration'])->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.format_extraneous_metadata');
    }
}
