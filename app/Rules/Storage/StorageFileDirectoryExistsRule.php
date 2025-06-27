<?php

declare(strict_types=1);

namespace App\Rules\Storage;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\File;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class StorageFileDirectoryExistsRule.
 */
readonly class StorageFileDirectoryExistsRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  Filesystem  $fs
     * @return void
     */
    public function __construct(protected Filesystem $fs) {}

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->fs->directoryExists(File::dirname($value))) {
            $fail(__('validation.directory_exists'));
        }
    }
}
