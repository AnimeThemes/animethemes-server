<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Anime\Studio;

use App\Actions\Models\Wiki\Anime\Studio\BackfillAnimeStudiosAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class BackfillAnimeStudiosTest.
 */
class BackfillAnimeStudiosTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill Studios Action shall skip the Anime if the relation already exists.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testSkipped(): void
    {
        $studiosCount = $this->faker->randomDigitNotNull();

        $anime = Anime::factory()
            ->has(Studio::factory()->count($studiosCount))
            ->createOne();

        $action = new BackfillAnimeStudiosAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED()->is($result->getStatus()));
        static::assertDatabaseCount(Studio::class, $studiosCount);
        Http::assertNothingSent();
    }

    /**
     * The Backfill Studios Action shall fail if the Anime has no Resources.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoResource(): void
    {
        $anime = Anime::factory()->createOne();

        $action = new BackfillAnimeStudiosAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Studio::class, 0);
        Http::assertNothingSent();
    }

    /**
     * The Backfill Studios Action shall fail if the MAL API returns no studios.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoMalMatch(): void
    {
        Http::fake([
            'https://api.myanimelist.net/v2/anime/*' => Http::response([
                $this->faker->word() => $this->faker->word(),
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::MAL,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnimeStudiosAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Studio::class, 0);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Studios Action shall pass if the MAL API returns studios.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testMalPassed(): void
    {
        $studioCount = $this->faker->randomDigitNotNull();

        $studios = Collection::times(
            $studioCount,
            fn (int $time) => ['id' => $time, 'name' => $this->faker->unique()->word()]
        );

        Http::fake([
            'https://api.myanimelist.net/v2/anime/*' => Http::response([
                'studios' => $studios->toArray(),
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::MAL,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnimeStudiosAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Studio::class, $studioCount);
        static::assertEquals($studioCount, $anime->studios()->count());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Studios Action shall fail if the Anilist API returns no studios.
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

        $action = new BackfillAnimeStudiosAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Studio::class, 0);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Studios Action shall pass if the Anilist API returns studios.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testAnilistPassed(): void
    {
        $studioCount = $this->faker->randomDigitNotNull();

        $studios = Collection::times(
            $studioCount,
            fn (int $time) => ['id' => $time, 'name' => $this->faker->unique()->word()]
        );

        Http::fake([
            'https://graphql.anilist.co' => Http::response([
                'data' => [
                    'Media' => [
                        'studios' => [
                            'nodes' => $studios,
                        ],
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

        $action = new BackfillAnimeStudiosAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Studio::class, $studioCount);
        static::assertEquals($studioCount, $anime->studios()->count());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Studios Action shall fail if the Kitsu API returns no studios.
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

        $action = new BackfillAnimeStudiosAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Studio::class, 0);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Studios Action shall pass if the Kitsu API returns studios.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testKitsuPassed(): void
    {
        $studioCount = $this->faker->randomDigitNotNull();

        $studios = Collection::times(
            $studioCount,
            fn () => [
                'role' => 'STUDIO',
                'company' => [
                    'name' => $this->faker->unique()->word(),
                ],
            ],
        );

        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                'data' => [
                    'findAnimeById' => [
                        'productions' => [
                            'nodes' => $studios,
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

        $action = new BackfillAnimeStudiosAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Studio::class, $studioCount);
        static::assertEquals($studioCount, $anime->studios()->count());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Studios Action shall create a resource if the site provides an ID.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testCreatesStudioResource(): void
    {
        $studioCount = $this->faker->randomDigitNotNull();

        $studios = Collection::times(
            $studioCount,
            fn (int $time) => ['id' => $time, 'name' => $this->faker->unique()->word()]
        );

        Http::fake([
            'https://api.myanimelist.net/v2/anime/*' => Http::response([
                'studios' => $studios->toArray(),
            ]),
            'https://graphql.anilist.co' => Http::response([
                'data' => [
                    'Media' => [
                        'studios' => [
                            'nodes' => $studios,
                        ],
                    ],
                ],
            ]),
        ]);

        $site = Arr::random([
            ResourceSite::MAL,
            ResourceSite::ANILIST,
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnimeStudiosAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, $studioCount + 1);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Studios Action shall create a resource if the site provides an ID.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testGetsExistingResource(): void
    {
        $studioCount = $this->faker->randomDigitNotNull();

        $studios = Collection::times(
            $studioCount,
            fn (int $time) => ['id' => $time, 'name' => $this->faker->unique()->word()]
        );

        Http::fake([
            'https://api.myanimelist.net/v2/anime/*' => Http::response([
                'studios' => $studios->toArray(),
            ]),
            'https://graphql.anilist.co' => Http::response([
                'data' => [
                    'Media' => [
                        'studios' => [
                            'nodes' => $studios,
                        ],
                    ],
                ],
            ]),
        ]);

        $site = Arr::random([
            ResourceSite::MAL,
            ResourceSite::ANILIST,
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site,
        ]);

        foreach ($studios as $studio) {
            $id = Arr::get($studio, 'id');
            $slug = Str::slug(Arr::get($studio, 'name'));
            $link = ResourceSite::fromValue($site)->formatStudioResourceLink($id, $slug);

            ExternalResource::factory()->createOne([
                ExternalResource::ATTRIBUTE_SITE => $site,
                ExternalResource::ATTRIBUTE_EXTERNAL_ID => $id,
                ExternalResource::ATTRIBUTE_LINK => $link,
            ]);
        }

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnimeStudiosAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, $studioCount + 1);
        Http::assertSentCount(1);
    }
}
