<?php

declare(strict_types=1);

namespace App\Rules\Storage;

use Closure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\File;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class StorageFileDirectoryExistsRule implements ValidationRule
{
    public function __construct(protected Filesystem $fs) {}

    /**
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->fs->directoryExists(File::dirname($value))) {
            $fail(__('validation.directory_exists'));
        }
    }
}
