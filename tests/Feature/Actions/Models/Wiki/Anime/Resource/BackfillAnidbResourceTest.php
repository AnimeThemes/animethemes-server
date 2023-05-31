<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Anime\Resource;

use App\Actions\Models\Wiki\Anime\Resource\BackfillAnidbResourceAction;
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
 * Class BackfillAnidbResourceActionTest.
 */
class BackfillAnidbResourceTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill AniDB Action shall skip the Anime if the relation already exists.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testSkipped(): void
    {
        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANIDB,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnidbResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertNothingSent();
    }

    /**
     * The Backfill AniDB Action shall fail if the Anime has no Resources.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoResource(): void
    {
        $anime = Anime::factory()->createOne();

        $action = new BackfillAnidbResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 0);
        Http::assertNothingSent();
    }

    /**
     * The Backfill AniDB Action shall fail if the Yuna API does not return a match.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoMatch(): void
    {
        Http::fake([
            'https://relations.yuna.moe/api/ids*' => Http::response(),
        ]);

        $site = Arr::random([
            ResourceSite::MAL,
            ResourceSite::ANILIST,
            ResourceSite::KITSU,
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnidbResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill AniDB Action shall pass if the Yuna API returns a match.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testPassed(): void
    {
        Http::fake([
            'https://relations.yuna.moe/api/ids*' => Http::response([
                'anidb' => $this->faker->randomDigitNotNull(),
            ]),
        ]);

        $site = Arr::random([
            ResourceSite::MAL,
            ResourceSite::ANILIST,
            ResourceSite::KITSU,
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnidbResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(ExternalResource::class, 2);
        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB)->exists());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill AniDB Action shall get an existing resource if the site and id match.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testGetsExistingResource(): void
    {
        $anidbId = $this->faker->randomDigitNotNull();

        ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANIDB,
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => $anidbId,
            ExternalResource::ATTRIBUTE_LINK => ResourceSite::ANIDB()->formatAnimeResourceLink($anidbId),

        ]);

        Http::fake([
            'https://relations.yuna.moe/api/ids*' => Http::response([
                'anidb' => $anidbId,
            ]),
        ]);

        $site = Arr::random([
            ResourceSite::MAL,
            ResourceSite::ANILIST,
            ResourceSite::KITSU,
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnidbResourceAction($anime);

        $action->handle();

        static::assertDatabaseCount(ExternalResource::class, 2);
        Http::assertSentCount(1);
    }
}
