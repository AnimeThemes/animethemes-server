<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Artist;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

readonly class ArtistResourceLinkFormatRule implements ValidationRule
{
    public function __construct(protected ResourceSite $site) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = $this->site->getPattern(Artist::class);

        if ($pattern !== null && Str::match($pattern, $value) !== $value) {
            $fail(__('validation.regex'));
        }
    }
}
