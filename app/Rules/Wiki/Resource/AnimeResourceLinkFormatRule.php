<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class AnimeResourceLinkFormatRule.
 */
readonly class AnimeResourceLinkFormatRule implements ValidationRule
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
        $pattern = match ($this->site) {
            ResourceSite::TWITTER => '/^https:\/\/twitter\.com\/\w+$/',
            ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/anime\/\d+$/',
            ResourceSite::ANILIST => '/^https:\/\/anilist\.co\/anime\/\d+$/',
            ResourceSite::ANIME_PLANET => '/^https:\/\/www\.anime-planet\.com\/anime\/[a-zA-Z0-9-]+$/',
            ResourceSite::ANN => '/^https:\/\/www\.animenewsnetwork\.com\/encyclopedia\/anime\.php\?id=\d+$/',
            ResourceSite::KITSU => '/^https:\/\/kitsu\.io\/anime\/[a-zA-Z0-9-]+$/',
            ResourceSite::MAL => '/^https:\/\/myanimelist\.net\/anime\/\d+$/',
            ResourceSite::SPOTIFY => '/$.^/',
            ResourceSite::YOUTUBE_MUSIC => '/$.^/',
            ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/\@\w+$/',
            ResourceSite::APPLE_MUSIC => '/$.^/',
            ResourceSite::AMAZON_MUSIC => '/$.^/',
            ResourceSite::CRUNCHYROLL => '/^https:\/\/www\.crunchyroll\.com\/series\/\w+$/',
            ResourceSite::HIDIVE => '/^https:\/\/www\.hidive\.com\/tv\/[\w-]+$/',
            ResourceSite::NETFLIX => '/^https:\/\/www\.netflix\.com\/title\/\d+$/',
            ResourceSite::DISNEY_PLUS => '/^https:\/\/www\.disneyplus\.com\/series\/[\w-]+\/\w+$/',
            ResourceSite::HULU => '/^https:\/\/www\.hulu\.com\/series\/[\w-]+$/',
            ResourceSite::AMAZON_PRIME_VIDEO => '/^https:\/\/www\.primevideo\.com\/detail\/\w+$/',
            default => null,
        };

        if ($pattern !== null && Str::match($pattern, $value) !== $value) {
            $fail(__('validation.regex'));
        }
    }
}
