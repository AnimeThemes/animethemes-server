<?php

declare(strict_types=1);

namespace App\Rules\Storage;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class StorageDirectoryExistsRule implements ValidationRule
{
    public function __construct(protected Filesystem $fs) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->fs->directoryExists($value)) {
            $fail(__('validation.directory_exists'));
        }
    }
}
