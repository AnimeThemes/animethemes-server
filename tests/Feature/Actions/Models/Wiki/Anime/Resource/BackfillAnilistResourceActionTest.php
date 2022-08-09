<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Anime\Resource;

use App\Actions\Models\Wiki\Anime\Resource\BackfillAnilistResourceAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Class BackfillAnilistResourceActionTest.
 */
class BackfillAnilistResourceActionTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill Anilist Action shall skip the Anime if the relation already exists.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testSkipped(): void
    {
        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANILIST,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [],Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED()->is($result->getStatus()));
    }

    /**
     * The Backfill Anilist Action shall fail if the Anime has no Resources.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testFailedWhenNoResource(): void
    {
        $anime = Anime::factory()->createOne();

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
    }

    /**
     * The Backfill Anilist Action shall fail if the Anilist API returns no match for a MAL ID.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testFailedWhenNoMalMatch(): void
    {
        Http::fake([
            'https://graphql.anilist.co' => Http::response([
                $this->faker->word() => $this->faker->word()
            ]),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::MAL,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [],Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnilistResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
    }
}
