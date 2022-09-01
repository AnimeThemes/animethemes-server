<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

/**
 * Class ArtistResourceLinkFormatRule.
 */
class ArtistResourceLinkFormatRule implements Rule
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
            ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/creator\/\d+$/',
            ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/staff\/\d+$/',
            ResourceSite::ANIME_PLANET => '/^https:\/\/www\.anime-planet\.com\/people\/[a-zA-Z0-9-]+$/',
            ResourceSite::ANN => '/^https:\/\/www\.animenewsnetwork\.com\/encyclopedia\/people\.php\?id=\d+$/',
            ResourceSite::KITSU => '/$.^/',
            ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/people\/\d+$/',
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
