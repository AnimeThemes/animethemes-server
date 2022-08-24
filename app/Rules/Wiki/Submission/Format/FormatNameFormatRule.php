<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Submission\Format;

use App\Rules\Wiki\Submission\SubmissionRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

/**
 * Class FormatNameFormatRule.
 */
class FormatNameFormatRule extends SubmissionRule
{
    /**
     * Create new rule instance.
     *
     * @param  string  $expected
     */
    public function __construct(protected readonly string $expected)
    {
    }

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

        $formatName = Arr::get($format, 'format_name');

        return $formatName === $this->expected;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.submission.format_format_name');
    }
}
