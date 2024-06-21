<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Anime\Resource;

use App\Actions\Models\Wiki\Anime\Resource\BackfillAnnResourceAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Class BackfillAnnResourceActionTest.
 */
class BackfillAnnResourceTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill ANN Action shall skip the Anime if the relation already exists.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testSkipped(): void
    {
        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANN,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillAnnResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED === $result->getStatus());
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertNothingSent();
    }

    /**
     * The Backfill ANN Action shall fail if the Anime has no Resources.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoResource(): void
    {
        $anime = Anime::factory()->createOne();

        $action = new BackfillAnnResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 0);
        Http::assertNothingSent();
    }

    /**
     * The Backfill ANN Action shall fail if the Kitsu API returns no ANN mapping.
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

        $action = new BackfillAnnResourceAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(ExternalResource::class, 1);
        Http::assertSentCount(1);
    }

    /**
     * The Backfill ANN Action shall pass if the Kitsu API returns an ANN mapping.
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
                                    'externalSite' => 'ANIMENEWSNETWORK',
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

        $action = new BackfillAnnResourceAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED === $result->getStatus());
        static::assertDatabaseCount(ExternalResource::class, 2);
        static::assertTrue($anime->resources()->where(ExternalResource::ATTRIBUTE_SITE, ResourceSite::ANN)->exists());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill ANN Action shall get an existing resource if the site and id match.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testGetsExistingResource(): void
    {
        $annId = $this->faker->randomDigitNotNull();

        ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANN,
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => $annId,
            ExternalResource::ATTRIBUTE_LINK => ResourceSite::ANN->formatResourceLink(Anime::class, $annId),
        ]);

        Http::fake([
            'https://kitsu.io/api/graphql' => Http::response([
                'data' => [
                    'findAnimeById' => [
                        'mappings' => [
                            'nodes' => [
                                [
                                    'externalSite' => 'ANIMENEWSNETWORK',
                                    'externalId' => $annId,
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

        $action = new BackfillAnnResourceAction($anime);

        $action->handle();

        static::assertDatabaseCount(ExternalResource::class, 2);
        Http::assertSentCount(1);
    }
}
