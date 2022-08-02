<?php

declare(strict_types=1);

namespace App\Rules\Wiki;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class StorageDirectoryExistsRule.
 */
class StorageDirectoryExistsRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param  Filesystem  $fs
     * @return void
     */
    public function __construct(protected readonly Filesystem $fs)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $this->fs->directoryExists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.directory_exists');
    }
}
