<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use Alchemy\BinaryDriver\Exception\ExecutionFailureException;
use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;

/**
 * Class ExtraneousChaptersRule.
 */
class ExtraneousChaptersFormatRule extends SubmissionRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  UploadedFile  $value
     * @return bool
     *
     * @throws ExecutionFailureException
     */
    public function passes($attribute, $value): bool
    {
        return empty($this->chapters());
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.format_extraneous_chapters');
    }
}
