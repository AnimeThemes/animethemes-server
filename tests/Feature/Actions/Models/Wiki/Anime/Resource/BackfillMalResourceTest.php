<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Anime\Resource;

use App\Actions\Models\Wiki\Anime\Resource\BackfillMalResourceAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Class BackfillMalResourceTest.
 */
class BackfillMalResourceTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill MAL Action shall skip the Anime if the relation already exists.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testSkipped(): void
    {
        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::MAL,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillMalResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertNothingSent();
    }

    /**
     * The Backfill MAL Action shall fail if the Anime has no Resources.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoResource(): void
    {
        $anime = Anime::factory()->createOne();

        $action = new BackfillMalResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 0);
        Http::assertNothingSent();
    }

    /**
     * The Backfill MAL Action shall fail if the Kitsu API returns no MAL mapping.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoKitsuMatch(): void
    {
        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                $this->faker->word() => $this->faker->word(),
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::KITSU,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillMalResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill MAL Action shall pass if the Kitsu API returns an MAL mapping.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testKitsuPassed(): void
    {
        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                'data' => [
                    'findAnimeById' => [
                        'mappings' => [
                            'nodes' => [
                                [
                                    'externalSite' => 'MYANIMELIST_ANIME',
                                    'externalId' => $this->faker->randomDigitNotNull(),
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::KITSU,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillMalResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 2);
        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL)->exists());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill MAL Action shall fail if the Anilist API returns no MAL mapping.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoAnilistMatch(): void
    {
        Http::fake([
            'https://graphql.anilist.co' => Http::response([
                $this->faker->word() => $this->faker->word(),
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANILIST,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillMalResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill MAL Action shall pass if the Anilist API returns a match for a MAL ID.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testAnilistPassed(): void
    {
        Http::fake([
            'https://graphql.anilist.co' => Http::response([
                'data' => [
                    'Media' => [
                        'idMal' => $this->faker->randomDigitNotNull(),
                    ],
                ],
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANILIST,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillMalResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 2);
        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL)->exists());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill MAL Action shall fail if the Yuna API returns no MAL mapping for an AniDB ID.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoAnidbMatch(): void
    {
        Http::fake([
            'https://relations.yuna.moe/api/ids*' => Http::response(),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANIDB,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillMalResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill MAL Action shall pass if the Yuna API returns a MAL mapping for an AniDB ID.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testAnidbPassed(): void
    {
        Http::fake([
            'https://relations.yuna.moe/api/ids*' => Http::response([
                'myanimelist' => $this->faker->randomDigitNotNull(),
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANIDB,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillMalResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 2);
        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL)->exists());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill MAL Action shall get an existing resource if the site and id match.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testGetsExistingResource(): void
    {
        $malId = $this->faker->randomDigitNotNull();

        ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::MAL,
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => $malId,
            ExternalResource::ATTRIBUTE_LINK => ResourceSite::MAL()->formatAnimeResourceLink($malId),
        ]);

        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                'data' => [
                    'findAnimeById' => [
                        'mappings' => [
                            'nodes' => [
                                [
                                    'externalSite' => 'MYANIMELIST_ANIME',
                                    'externalId' => $malId,
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
            'https://graphql.anilist.co' => Http::response([
                'data' => [
                    'Media' => [
                        'idMal' => $malId,
                    ],
                ],
            ]),
            'https://relations.yuna.moe/api/ids*' => Http::response([
                'myanimelist' => $malId,
            ]),
        ]);

        $site = Arr::random([
            ResourceSite::KITSU,
            ResourceSite::ANILIST,
            ResourceSite::ANIDB,
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillMalResourceAction($anime);

        $action->handle();

        static::assertDatabaseCount(ExternalResource::class, 2);
        Http::assertSentCount(1);
    }
}
