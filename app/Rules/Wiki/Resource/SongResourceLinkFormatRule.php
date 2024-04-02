<?php

declare(strict_types=1);

namespace App\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Class SongResourceLinkFormatRule.
 */
readonly class SongResourceLinkFormatRule implements ValidationRule
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
            ResourceSite::TWITTER => '/$.^/',
            ResourceSite::ANIDB => '/^https:\/\/anidb\.net\/song\/\d+$/',
            ResourceSite::ANILIST => '/$.^/',
            ResourceSite::ANIME_PLANET => '/$.^/',
            ResourceSite::ANN => '/$.^/',
            ResourceSite::KITSU => '/$.^/',
            ResourceSite::MAL => '/$.^/',
            ResourceSite::SPOTIFY => '/^https:\/\/open\.spotify\.com\/track\/\w+$/',
            ResourceSite::YOUTUBE_MUSIC => '/^https:\/\/music\.youtube\.com\/watch\?v=[\w-]+$/',
            ResourceSite::YOUTUBE => '/^https:\/\/www\.youtube\.com\/watch\?v=[\w-]+$/',
            ResourceSite::APPLE_MUSIC => '/^https:\/\/music\.apple\.com\/jp\/album\/\d+$/',
            ResourceSite::AMAZON_MUSIC => '/^https:\/\/music\.amazon\.co\.jp\/albums\/\w+$/',
            default => null,
        };

        if ($pattern !== null && Str::match($pattern, $value) !== $value) {
            $fail(__('validation.regex'));
        }
    }
}
