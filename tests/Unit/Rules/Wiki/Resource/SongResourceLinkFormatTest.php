<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Rules\Wiki\Resource\SongResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SongResourceLinkFormatTest.
 */
class SongResourceLinkFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Song Resource Link Format Rule shall pass for sites with no expected pattern.
     *
     * @return void
     */
    public function testPassesForNoPattern(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->url()],
            [$attribute => new SongResourceLinkFormatRule(ResourceSite::OFFICIAL_SITE)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Song Resource Link Format Rule shall pass for URLs that match the expected pattern.
     *
     * @return void
     */
    public function testPassesForPattern(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::SPOTIFY,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::YOUTUBE,
            ResourceSite::APPLE_MUSIC,
            ResourceSite::AMAZON_MUSIC,
        ]);

        $url = $site->formatResourceLink(Song::class, $this->faker->randomDigitNotNull(), $this->faker->word(), 'null');

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new SongResourceLinkFormatRule($site)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Song Resource Link Format Rule shall fail for trailing slashes in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlash(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::SPOTIFY,
            ResourceSite::YOUTUBE_MUSIC,
            ResourceSite::YOUTUBE,
            ResourceSite::APPLE_MUSIC,
            ResourceSite::AMAZON_MUSIC,
        ]);

        $url = $site->formatResourceLink(Song::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new SongResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Song Resource Link Format Rule shall fail for trailing slugs in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlug(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::APPLE_MUSIC,
        ]);

        $url = $site->formatResourceLink(Song::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->append($this->faker->word())
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new SongResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Song Resource Link Format Rule shall fail for anime resources.
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
            ResourceSite::YOUTUBE,
        ]);

        $url = $site->formatResourceLink(Anime::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new SongResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Song Resource Link Format Rule shall fail for artist resources.
     *
     * @return void
     */
    public function testFailsForArtistResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::MAL,
            ResourceSite::SPOTIFY,
            ResourceSite::YOUTUBE,
        ]);

        $url = $site->formatResourceLink(Artist::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new SongResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Song Resource Link Format Rule shall fail for studio resources.
     *
     * @return void
     */
    public function testFailsForStudioResource(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANILIST,
            ResourceSite::ANIME_PLANET,
            ResourceSite::ANN,
            ResourceSite::MAL,
        ]);

        $url = $site->formatResourceLink(Studio::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new SongResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }
}
