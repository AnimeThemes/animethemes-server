<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Anime\Resource;

use App\Actions\Models\Wiki\Anime\Resource\BackfillKitsuResourceAction;
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
 * Class BackfillKitsuResourceActionTest.
 */
class BackfillKitsuResourceTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill Kitsu Action shall skip the Anime if the relation already exists.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testSkipped(): void
    {
        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::KITSU,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillKitsuResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertNothingSent();
    }

    /**
     * The Backfill Kitsu Action shall fail if the Anime has no Resources.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoResource(): void
    {
        $anime = Anime::factory()->createOne();

        $action = new BackfillKitsuResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 0);

        Http::assertNothingSent();
    }

    /**
     * The Backfill Kitsu Action shall fail if the Kitsu API does not return a match.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoMatch(): void
    {
        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                $this->faker->word() => $this->faker->word(),
            ]),
        ]);

        $site = Arr::random([
            ResourceSite::MAL,
            ResourceSite::ANILIST,
            ResourceSite::ANIDB,
            ResourceSite::ANN,
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillKitsuResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Kitsu Action shall pass if the Kitsu API returns a match.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testPassed(): void
    {
        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                'data' => [
                    'lookupMapping' => [
                        'id' => $this->faker->randomDigitNotNull(),
                        'slug' => $this->faker->slug(),
                    ],
                ],
            ]),
        ]);

        $site = Arr::random([
            ResourceSite::MAL,
            ResourceSite::ANILIST,
            ResourceSite::ANIDB,
            ResourceSite::ANN,
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillKitsuResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 2);
        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU)->exists());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Kitsu Action shall get an existing resource if the site and id match.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testGetsExistingResource(): void
    {
        $kitsuId = $this->faker->randomDigitNotNull();
        $kitsuSlug = $this->faker->slug();

        ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::KITSU,
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => $kitsuId,
            ExternalResource::ATTRIBUTE_LINK => ResourceSite::formatAnimeResourceLink(ResourceSite::KITSU(), $kitsuId, $kitsuSlug),

        ]);

        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                'data' => [
                    'lookupMapping' => [
                        'id' => $kitsuId,
                        'slug' => $kitsuSlug,
                    ],
                ],
            ]),
        ]);

        $site = Arr::random([
            ResourceSite::MAL,
            ResourceSite::ANILIST,
            ResourceSite::ANIDB,
            ResourceSite::ANN,
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillKitsuResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 2);
        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::KITSU)->exists());
        Http::assertSentCount(1);
    }
}
