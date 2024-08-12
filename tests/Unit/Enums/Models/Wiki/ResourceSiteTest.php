<?php

declare(strict_types=1);

namespace Tests\Unit\Enums\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Class ResourceSiteTest.
 */
class ResourceSiteTest extends TestCase
{
    use WithFaker;

    /**
     * The Resource Site shall parse the ID from URLs that contain the Anime ID.
     *
     * @return void
     */
    public function testParseIdFromAnimeResource(): void
    {
        $animeId = $this->faker->randomDigitNotNull();

        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANN,
            ResourceSite::MAL,
            ResourceSite::NETFLIX,
        ]);

        $link = $site->formatResourceLink(Anime::class, $animeId);

        static::assertEquals(strval($animeId), ResourceSite::parseIdFromLink($link));
    }

    /**
     * The Resource Site shall parse the ID from URLs that contain the Studio ID.
     *
     * @return void
     */
    public function testParseIdFromStudioResource(): void
    {
        $studioId = $this->faker->randomDigitNotNull();

        /** @var ResourceSite $site */
        $site = Arr::random([
            ResourceSite::ANIDB,
            ResourceSite::ANILIST,
            ResourceSite::ANN,
            ResourceSite::MAL,
        ]);

        $link = $site->formatResourceLink(Studio::class, $studioId);

        static::assertEquals(strval($studioId), ResourceSite::parseIdFromLink($link));
    }

    /**
     * The Resource Site shall fail to parse the ID from Anime-Planet for studio resources.
     *
     * @return void
     */
    public function testFailParseAnimePlanetIdFromStudioResource(): void
    {
        $link = ResourceSite::ANIME_PLANET->formatResourceLink(Studio::class, 
            $this->faker->randomDigitNotNull(),
            $this->faker->slug()
        );

        static::assertEmpty(ResourceSite::parseIdFromLink($link));
        Http::assertNothingSent();
    }

    /**
     * The Resource Site shall fail to parse the ID from Anime-Planet if the response is not expected.
     *
     * @return void
     */
    public function testFailParseAnimePlanetIdFromAnimeResource(): void
    {
        Http::fake([
            'https://www.anime-planet.com/anime/*' => Http::response([
                $this->faker->word() => $this->faker->word(),
            ]),
        ]);

        $link = ResourceSite::ANIME_PLANET->formatResourceLink(Anime::class, 
            $this->faker->randomDigitNotNull(),
            $this->faker->slug()
        );

        static::assertEmpty(ResourceSite::parseIdFromLink($link));
        Http::assertSentCount(1);
    }

    /**
     * The Resource Site shall parse the ID from Anime-Planet if the response is expected.
     *
     * @return void
     */
    public function testParseAnimePlanetIdFromAnimeResource(): void
    {
        $id = $this->faker->randomDigitNotNull();

        Http::fake([
            'https://www.anime-planet.com/anime/*' => Http::response(
                "
                $(function() {
                    window.AP_VARS = $.extend(true, {}, window.AP_VARS, {
                        ENTRY_INFO: {
                            type: \"anime\",
                            id: \"$id\",
                            url: \"{$this->faker->word()}\"
                        }
                    }
                });
                "
            ),
        ]);

        $link = ResourceSite::ANIME_PLANET->formatResourceLink(Anime::class, $id, $this->faker->slug());

        static::assertEquals(strval($id), ResourceSite::parseIdFromLink($link));
        Http::assertSentCount(1);
    }

    /**
     * The Resource Site shall parse the ID from Kitsu if the link contains an integer.
     *
     * @return void
     */
    public function testParseKitsuIdForIdFromAnimeResource(): void
    {
        $id = $this->faker->randomDigitNotNull();

        $link = ResourceSite::KITSU->formatResourceLink(Anime::class, $id);

        static::assertEquals($id, ResourceSite::parseIdFromLink($link));
    }

    /**
     * The Resource Site shall parse the ID from Kitsu if the link contains a slug.
     *
     * @return void
     */
    public function testParseKitsuIdForSlugFromAnimeResource(): void
    {
        $id = $this->faker->randomDigitNotNull();
        $slug = $this->faker->slug();

        $linkWithSlug = Str::of(ResourceSite::KITSU->formatResourceLink(Anime::class, $id))
            ->replace(strval($id), $slug)
            ->__toString();

        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                'data' => [
                    'findAnimeBySlug' => [
                        'id' => strval($id),
                    ],
                ],
            ]),
        ]);

        static::assertEquals(strval($id), ResourceSite::parseIdFromLink($linkWithSlug));
        Http::assertSentCount(1);
    }
}
