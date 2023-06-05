<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
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
        $pattern = match ($this->site->value) {
            ResourceSite::TWITTER => '/^https:\/\/twitter\.com\/\w+$/',
            ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/creator\/(?:virtual\/)?\d+$/',
            ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/studio\/\d+$/',
            ResourceSite::ANIME_PLANET => '/^https:\/\/www\.anime-planet\.com\/anime\/studios\/[a-zA-Z0-9-]+$/',
            ResourceSite::ANN => '/^https:\/\/www\.animenewsnetwork\.com\/encyclopedia\/company\.php\?id=\d+$/',
            ResourceSite::KITSU => '/$.^/',
            ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/anime\/producer\/\d+$/',
            default => null,
        };

        if ($pattern !== null && Str::match($pattern, $value) !== $value) {
            $fail(__('validation.regex'));
        }
    }
}
