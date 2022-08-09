<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Anime\Resource;

use App\Actions\Models\Wiki\Anime\Resource\BackfillAnidbResourceAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Class BackfillAnidbResourceActionTest.
 */
class BackfillAnidbResourceActionTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill AniDB Action shall skip the Anime if the relation already exists.
     *
     * @return void
     *
     * @throws RequestException
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
    }

    /**
     * The Backfill AniDB Action shall fail if the Anime has no Resources.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testFailedWhenNoResource(): void
    {
        $anime = Anime::factory()->createOne();

        $action = new BackfillAnidbResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Backfill AniDB Action shall fail if the Yuna API does not return a match.
     *
     * @return void
     *
     * @throws RequestException
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
    }

    /**
     * The Backfill AniDB Action shall attach an AniDB resource to the Anime.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testAttachesAniDbResource(): void
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

        $action->handle();

        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANIDB)->exists());
    }

    /**
     * The Backfill AniDB Action shall pass if the Yuna API returns a match.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testPassedWhenMatched(): void
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
    }

    /**
     * The Backfill AniDB Action shall get an existing resource if the site and id match.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testGetsExistingResource(): void
    {
        $anidbId = $this->faker->randomDigitNotNull();

        ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANIDB,
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => $anidbId,
            ExternalResource::ATTRIBUTE_LINK => ResourceSite::formatAnimeResourceLink(ResourceSite::ANIDB(), $anidbId),

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
    }
}
