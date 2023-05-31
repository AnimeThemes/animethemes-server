<?php

declare(strict_types=1);

namespace Tests\Unit\Enums\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
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
            ResourceSite::ANIDB(),
            ResourceSite::ANILIST(),
            ResourceSite::ANN(),
            ResourceSite::MAL(),
        ]);

        $link = $site->formatAnimeResourceLink($animeId);

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
            ResourceSite::ANIDB(),
            ResourceSite::ANILIST(),
            ResourceSite::ANN(),
            ResourceSite::MAL(),
        ]);

        $link = $site->formatStudioResourceLink($studioId);

        static::assertEquals(strval($studioId), ResourceSite::parseIdFromLink($link));
    }

    /**
     * The Resource Site shall fail to parse the ID from Anime-Planet for studio resources.
     *
     * @return void
     */
    public function testFailParseAnimePlanetIdFromStudioResource(): void
    {
        $link = ResourceSite::ANIME_PLANET()->formatStudioResourceLink(
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

        $link = ResourceSite::ANIME_PLANET()->formatAnimeResourceLink(
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

        $link = ResourceSite::ANIME_PLANET()->formatAnimeResourceLink(
            $id,
            $this->faker->slug()
        );

        static::assertEquals(strval($id), ResourceSite::parseIdFromLink($link));
        Http::assertSentCount(1);
    }

    /**
     * The Resource Site shall fail to parse the ID from Kitsu if the response is not expected.
     *
     * @return void
     */
    public function testFailParseKitsuIdFromAnimeResource(): void
    {
        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                $this->faker->word() => $this->faker->word(),
            ]),
        ]);

        $link = ResourceSite::KITSU()->formatAnimeResourceLink(
            $this->faker->randomDigitNotNull(),
            $this->faker->slug()
        );

        static::assertEmpty(ResourceSite::parseIdFromLink($link));
        Http::assertSentCount(1);
    }

    /**
     * The Resource Site shall parse the ID from Kitsu if the response is expected.
     *
     * @return void
     */
    public function testParseKitsuIdFromAnimeResource(): void
    {
        $id = $this->faker->randomDigitNotNull();

        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                'data' => [
                    'findAnimeBySlug' => [
                        'id' => strval($id),
                    ],
                ],
            ]),
        ]);

        $link = ResourceSite::KITSU()->formatAnimeResourceLink(
            $id,
            $this->faker->slug()
        );

        static::assertEquals(strval($id), ResourceSite::parseIdFromLink($link));
        Http::assertSentCount(1);
    }
}
