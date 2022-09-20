<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/**
 * Class StudioResourceLinkFormatRule.
 */
class StudioResourceLinkFormatRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @param  ResourceSite  $site
     */
    public function __construct(protected readonly ResourceSite $site)
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

        return $pattern === null || Str::match($pattern, $value) === $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message(): string|array
    {
        return __('validation.regex');
    }
}
