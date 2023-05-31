<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Rules\Wiki\Resource\ArtistResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class ArtistResourceLinkFormatTest.
 */
class ArtistResourceLinkFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Artist Resource Link Format Rule shall pass for sites with no expected pattern.
     *
     * @return void
     */
    public function testPassesForNoPattern(): void
    {
        $rule = new ArtistResourceLinkFormatRule(ResourceSite::OFFICIAL_SITE());

        static::assertTrue($rule->passes($this->faker->word(), $this->faker->url()));
    }

    /**
     * The Artist Resource Link Format Rule shall pass for URLs that match the expected pattern.
     *
     * @return void
     */
    public function testPassesForPattern(): void
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

        $rule = new ArtistResourceLinkFormatRule($site);

        static::assertTrue($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Artist Resource Link Format Rule shall fail for kitsu resources.
     *
     * @return void
     */
    public function testFailsForKitsu(): void
    {
        $url = ResourceSite::KITSU()->formatAnimeResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $rule = new ArtistResourceLinkFormatRule(ResourceSite::KITSU());

        static::assertFalse($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Artist Resource Link Format Rule shall fail for trailing slashes in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlash(): void
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

        $url = Str::of($url)
            ->append('/')
            ->__toString();

        $rule = new ArtistResourceLinkFormatRule($site);

        static::assertFalse($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Artist Resource Link Format Rule shall fail for trailing slugs in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlug(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANILIST(),
            ResourceSite::MAL(),
        ]);

        $url = $site->formatArtistResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->append($this->faker->word())
            ->__toString();

        $rule = new ArtistResourceLinkFormatRule($site);

        static::assertFalse($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Artist Resource Link Format Rule shall fail for anime resources.
     *
     * @return void
     */
    public function testFailsForAnimeResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANIDB(),
            ResourceSite::ANILIST(),
            ResourceSite::ANIME_PLANET(),
            ResourceSite::ANN(),
            ResourceSite::MAL(),
        ]);

        $url = $site->formatAnimeResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $rule = new ArtistResourceLinkFormatRule($site);

        static::assertFalse($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Artist Resource Link Format Rule shall fail for studio resources.
     *
     * @return void
     */
    public function testFailsForStudioResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANILIST(),
            ResourceSite::ANIME_PLANET(),
            ResourceSite::ANN(),
            ResourceSite::MAL(),
        ]);

        $url = $site->formatStudioResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $rule = new ArtistResourceLinkFormatRule($site);

        static::assertFalse($rule->passes($this->faker->word(), $url));
    }
}
