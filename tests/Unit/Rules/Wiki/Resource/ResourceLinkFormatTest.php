<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Rules\Wiki\Resource\ResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class ResourceLinkFormatTest.
 */
class ResourceLinkFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Resource Link Format Rule shall pass if the site cannot be resolved.
     *
     * @return void
     */
    public function testPassesForNoSite(): void
    {
        $rule = new ResourceLinkFormatRule();

        static::assertTrue($rule->passes($this->faker->word(), $this->faker->url()));
    }

    /**
     * The Resource Link Format Rule shall pass for sites with no expected pattern.
     *
     * @return void
     */
    public function testPassesForNoPattern(): void
    {
        $rule = new ResourceLinkFormatRule(ResourceSite::OFFICIAL_SITE());

        static::assertTrue($rule->passes($this->faker->word(), $this->faker->url()));
    }

    /**
     * The Resource Link Format Rule shall pass for anime resources.
     *
     * @return void
     */
    public function testPassesForAnimeResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::TWITTER(),
            ResourceSite::ANIDB(),
            ResourceSite::ANILIST(),
            ResourceSite::ANIME_PLANET(),
            ResourceSite::ANN(),
            ResourceSite::KITSU(),
            ResourceSite::MAL(),
        ]);

        $url = $site->formatAnimeResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $rule = new ResourceLinkFormatRule($site);

        static::assertTrue($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Resource Link Format Rule shall pass for artist resources.
     *
     * @return void
     */
    public function testPassesForArtistResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::TWITTER(),
            ResourceSite::ANIDB(),
            ResourceSite::ANILIST(),
            ResourceSite::ANIME_PLANET(),
            ResourceSite::ANN(),
            ResourceSite::MAL(),
        ]);

        $url = $site->formatArtistResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $rule = new ResourceLinkFormatRule($site);

        static::assertTrue($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Resource Link Format Rule shall pass for studio resources.
     *
     * @return void
     */
    public function testPassesForStudioResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::TWITTER(),
            ResourceSite::ANIDB(),
            ResourceSite::ANILIST(),
            ResourceSite::ANIME_PLANET(),
            ResourceSite::ANN(),
            ResourceSite::MAL(),
        ]);

        $url = $site->formatStudioResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $rule = new ResourceLinkFormatRule($site);

        static::assertTrue($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Resource Link Format Rule shall fail for trailing slashes in URLs with defined patterns.
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

        $url = $site->formatAnimeResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->__toString();

        $rule = new ResourceLinkFormatRule($site);

        static::assertFalse($rule->passes($this->faker->word(), $url));
    }
}
