<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Studio;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class StudioResourceLinkFormatRule.
 */
readonly class StudioResourceLinkFormatRule implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  ResourceSite  $site
     */
    public function __construct(protected ResourceSite $site)
    {
    }

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
        $pattern = $this->site->getPattern(Studio::class);

        if ($pattern !== '/$.^/' && Str::match($pattern, $value) !== $value) {
            $fail(__('validation.regex'));
        }
    }
}
