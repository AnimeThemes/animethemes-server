<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Anime\Resource;

use App\Actions\Models\Wiki\Anime\Resource\BackfillAnilistResourceAction;
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
 * Class BackfillAnilistResourceActionTest.
 */
class BackfillAnilistResourceTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill Anilist Action shall skip the Anime if the relation already exists.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testSkipped(): void
    {
        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANILIST,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertNothingSent();
    }

    /**
     * The Backfill Anilist Action shall fail if the Anime has no Resources.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoResource(): void
    {
        $anime = Anime::factory()->createOne();

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 0);
        Http::assertNothingSent();
    }

    /**
     * The Backfill Anilist Action shall fail if the Anilist API returns no match for a MAL ID.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoMalMatch(): void
    {
        Http::fake([
            'https://graphql.anilist.co' => Http::response([
                $this->faker->word() => $this->faker->word(),
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::MAL,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill AniDB Action shall pass if the Anilist API returns a match for a MAL ID.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testMalPassed(): void
    {
        Http::fake([
            'https://graphql.anilist.co' => Http::response([
                'data' => [
                    'Media' => [
                        'id' => $this->faker->randomDigitNotNull(),
                    ],
                ],
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::MAL,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 2);
        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST)->exists());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Anilist Action shall fail if the Kitsu API returns no Anilist mapping.
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

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Anilist Action shall pass if the Kitsu API returns an Anilist mapping.
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
                                    'externalSite' => 'ANILIST_ANIME',
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

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 2);
        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST)->exists());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Anilist Action shall fail if the Yuna API returns no Anilist mapping for an AniDB ID.
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

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Anilist Action shall pass if the Yuna API returns an Anilist mapping for an AniDB ID.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testAnidbPassed(): void
    {
        Http::fake([
            'https://relations.yuna.moe/api/ids*' => Http::response([
                'anilist' => $this->faker->randomDigitNotNull(),
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANIDB,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 2);
        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANILIST)->exists());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Anilist Action shall get an existing resource if the site and id match.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testGetsExistingResource(): void
    {
        $anilistId = $this->faker->randomDigitNotNull();

        ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANILIST,
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => $anilistId,
            ExternalResource::ATTRIBUTE_LINK => ResourceSite::ANILIST()->formatAnimeResourceLink($anilistId),
        ]);

        Http::fake([
            'https://graphql.anilist.co' => Http::response([
                'data' => [
                    'Media' => [
                        'id' => $anilistId,
                    ],
                ],
            ]),
            'https://kitsu.io/api/graphql' => Http::response([
                'data' => [
                    'findAnimeById' => [
                        'mappings' => [
                            'nodes' => [
                                [
                                    'externalSite' => 'ANILIST_ANIME',
                                    'externalId' => $anilistId,
                                ],
                            ],
                        ],
                    ],
                ],
            ]),
            'https://relations.yuna.moe/api/ids*' => Http::response([
                'anilist' => $anilistId,
            ]),
        ]);

        $site = Arr::random([
            ResourceSite::MAL,
            ResourceSite::KITSU,
            ResourceSite::ANIDB,
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnilistResourceAction($anime);

        $action->handle();

        static::assertDatabaseCount(ExternalResource::class, 2);
        Http::assertSentCount(1);
    }
}
