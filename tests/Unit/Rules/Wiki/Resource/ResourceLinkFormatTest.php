<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Rules\Wiki\Resource\ResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
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
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->url()],
            [$attribute => new ResourceLinkFormatRule()],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Resource Link Format Rule shall pass for sites with no expected pattern.
     *
     * @return void
     */
    public function testPassesForNoPattern(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->url()],
            [$attribute => new ResourceLinkFormatRule(ResourceSite::OFFICIAL_SITE)],
        );

        static::assertTrue($validator->passes());
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
            ResourceSite::TWITTER,
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::KITSU,
            ResourceSite::MAL,
            ResourceSite::YOUTUBE,
        ]);

        $url = $site->formatAnimeResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new ResourceLinkFormatRule($site)],
        );

        static::assertTrue($validator->passes());
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
            ResourceSite::TWITTER,
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::MAL,
            ResourceSite::SPOTIFY,
            ResourceSite::YOUTUBE
        ]);

        $url = $site->formatArtistResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new ResourceLinkFormatRule($site)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Resource Link Format Rule shall pass for song resources.
     * 
     * @return void
     */
    public function testPassesForSongResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::SPOTIFY,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::YOUTUBE,
            ResourceSite::APPLE_MUSIC,
            ResourceSite::AMAZON_MUSIC,
        ]);

        $url = $site->formatSongResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new ResourceLinkFormatRule($site)],
        );

        static::assertTrue($validator->passes());
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
            ResourceSite::TWITTER,
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::MAL,
        ]);

        $url = $site->formatStudioResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new ResourceLinkFormatRule($site)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Resource Link Format Rule shall fail for trailing slashes in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlash(): void
    {
        $site = Arr::random([
            ResourceSite::TWITTER,
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::KITSU,
            ResourceSite::MAL,
            ResourceSite::SPOTIFY,
            ResourceSite::YOUTUBE,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::APPLE_MUSIC,
            ResourceSite::AMAZON_MUSIC,
        ]);

        $url = $site->formatAnimeResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new ResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }
}
