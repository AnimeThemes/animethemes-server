<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Rules\Wiki\Resource\StudioResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class StudioResourceLinkFormatTest.
 */
class StudioResourceLinkFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Studio Resource Link Format Rule shall pass for sites with no expected pattern.
     *
     * @return void
     */
    public function testPassesForNoPattern(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->url()],
            [$attribute => new StudioResourceLinkFormatRule(ResourceSite::OFFICIAL_SITE)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall pass for URLs that match the expected pattern.
     *
     * @return void
     */
    public function testPassesForPattern(): void
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

        $url = $site->formatStudioResourceLink($this->faker->randomDigitNotNull(), $this->faker->word(), 'null');

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule($site)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall fail for kitsu resources.
     *
     * @return void
     */
    public function testFailsForKitsu(): void
    {
        $url = ResourceSite::KITSU->formatAnimeResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule(ResourceSite::KITSU)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall fail for trailing slashes in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlash(): void
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

        $url = Str::of($url)
            ->append('/')
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall fail for trailing slugs in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlug(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANILIST,
            ResourceSite::MAL,
        ]);

        $url = $site->formatStudioResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->append($this->faker->word())
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall fail for anime resources.
     *
     * @return void
     */
    public function testFailsForAnimeResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::MAL,
        ]);

        $url = $site->formatAnimeResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall fail for artist resources.
     *
     * @return void
     */
    public function testFailsForArtistResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::MAL,
        ]);

        $url = $site->formatArtistResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall fail for song resources.
     *
     * @return void
     */
    public function testFailsForSongResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::MAL,
        ]);

        $url = $site->formatSongResourceLink($this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }
}
