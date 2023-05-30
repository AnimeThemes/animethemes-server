<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Rules\Wiki\Resource\AnimeResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class AnimeResourceLinkFormatRule.
 */
class AnimeResourceLinkFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Anime Resource Link Format Rule shall pass for sites with no expected pattern.
     *
     * @return void
     */
    public function testPassesForNoPattern(): void
    {
        $rule = new AnimeResourceLinkFormatRule(ResourceSite::OFFICIAL_SITE());

        static::assertTrue($rule->passes($this->faker->word(), $this->faker->url()));
    }

    /**
     * The Anime Resource Link Format Rule shall pass for URLs that match the expected pattern.
     *
     * @return void
     */
    public function testPassesForPattern(): void
    {
        $site = Arr::random([
            ResourceSite::TWITTER(),
            ResourceSite::ANIDB(),
            ResourceSite::ANILIST(),
            ResourceSite::ANIME_PLANET(),
            ResourceSite::ANN(),
            ResourceSite::KITSU(),
            ResourceSite::MAL(),
        ]);

        $url = ResourceSite::formatAnimeResourceLink($site, $this->faker->randomDigitNotNull(), $this->faker->word());

        $rule = new AnimeResourceLinkFormatRule($site);

        static::assertTrue($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Anime Resource Link Format Rule shall fail for trailing slashes in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlash(): void
    {
        $site = Arr::random([
            ResourceSite::TWITTER(),
            ResourceSite::ANIDB(),
            ResourceSite::ANILIST(),
            ResourceSite::ANIME_PLANET(),
            ResourceSite::ANN(),
            ResourceSite::KITSU(),
            ResourceSite::MAL(),
        ]);

        $url = ResourceSite::formatAnimeResourceLink($site, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->__toString();

        $rule = new AnimeResourceLinkFormatRule($site);

        static::assertFalse($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Anime Resource Link Format Rule shall fail for trailing slugs in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlug(): void
    {
        $site = Arr::random([
            ResourceSite::ANILIST(),
            ResourceSite::MAL(),
        ]);

        $url = ResourceSite::formatAnimeResourceLink($site, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->append($this->faker->word())
            ->__toString();

        $rule = new AnimeResourceLinkFormatRule($site);

        static::assertFalse($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Anime Resource Link Format Rule shall fail for artist resources.
     *
     * @return void
     */
    public function testFailsForArtistResource(): void
    {
        $site = Arr::random([
            ResourceSite::ANIDB(),
            ResourceSite::ANILIST(),
            ResourceSite::ANIME_PLANET(),
            ResourceSite::ANN(),
            ResourceSite::MAL(),
        ]);

        $url = ResourceSite::formatArtistResourceLink($site, $this->faker->randomDigitNotNull(), $this->faker->word());

        $rule = new AnimeResourceLinkFormatRule($site);

        static::assertFalse($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Anime Resource Link Format Rule shall fail for studio resources.
     *
     * @return void
     */
    public function testFailsForStudioResource(): void
    {
        $site = Arr::random([
            ResourceSite::ANIDB(),
            ResourceSite::ANILIST(),
            ResourceSite::ANIME_PLANET(),
            ResourceSite::ANN(),
            ResourceSite::MAL(),
        ]);

        $url = ResourceSite::formatStudioResourceLink($site, $this->faker->randomDigitNotNull(), $this->faker->word());

        $rule = new AnimeResourceLinkFormatRule($site);

        static::assertFalse($rule->passes($this->faker->word(), $url));
    }
}
