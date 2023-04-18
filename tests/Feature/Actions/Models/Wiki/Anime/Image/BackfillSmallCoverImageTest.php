<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Anime\Image;

use App\Actions\Models\Wiki\Anime\Image\BackfillSmallCoverImageAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class BackfillSmallCoverImageActionTest.
 */
class BackfillSmallCoverImageTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill Small Cover Image Action shall skip the Anime if the relation already exists.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testSkipped(): void
    {
        Storage::fake(Config::get('image.disk'));

        $image = Image::factory()->createOne([
            Image::ATTRIBUTE_FACET => ImageFacet::COVER_SMALL,
        ]);

        $anime = Anime::factory()
            ->hasAttached($image)
            ->createOne();

        $action = new BackfillSmallCoverImageAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED()->is($result->getStatus()));
        static::assertDatabaseCount(Image::class, 1);
        static::assertEmpty(Storage::disk(Config::get('image.disk'))->allFiles());
        Http::assertNothingSent();
    }

    /**
     * The Backfill Small Cover Image Action shall fail if the Anime has no Resources.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoResource(): void
    {
        Storage::fake(Config::get('image.disk'));

        $anime = Anime::factory()->createOne();

        $action = new BackfillSmallCoverImageAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Image::class, 0);
        static::assertEmpty(Storage::disk(Config::get('image.disk'))->allFiles());
        Http::assertNothingSent();
    }

    /**
     * The Backfill Small Cover Image Action shall fail if the Anime has no Anilist Resource.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenNoAnilistResource(): void
    {
        Storage::fake(Config::get('image.disk'));

        $site = null;

        while ($site === null) {
            $siteCandidate = ResourceSite::getRandomInstance();
            if (ResourceSite::ANILIST()->isNot($siteCandidate)) {
                $site = $siteCandidate;
            }
        }

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site->value,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillSmallCoverImageAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Image::class, 0);
        static::assertEmpty(Storage::disk(Config::get('image.disk'))->allFiles());
        Http::assertNothingSent();
    }

    /**
     * The Backfill Small Cover Image Action shall fail if the Anilist request is not of the expected structure.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testFailedWhenBadAnilistResponse(): void
    {
        Storage::fake(Config::get('image.disk'));

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

        $action = new BackfillSmallCoverImageAction($anime);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Image::class, 0);
        static::assertEmpty(Storage::disk(Config::get('image.disk'))->allFiles());
        Http::assertSentCount(1);
    }

    /**
     * The Backfill Small Cover Image Action shall pass if the image can be retrieved.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testPassed(): void
    {
        Storage::fake(Config::get('image.disk'));

        $file = File::fake()->image($this->faker->word().'.jpg');

        Http::fake([
            'https://graphql.anilist.co' => Http::response([
                'data' => [
                    'Media' => [
                        'coverImage' => [
                            'medium' => "https://s4.anilist.co/file/anilistcdn/media/anime/cover/medium/{$file->getBasename()}",
                        ],
                    ],
                ],
            ]),
            'https://s4.anilist.co/*' => Http::response($file->getContent()),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::ANILIST,
        ]);

        $anime = Anime::factory()
            ->hasAttached($resource, [], Anime::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillSmallCoverImageAction($anime);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Image::class, 1);
        static::assertTrue($anime->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_SMALL)->exists());
        static::assertCount(1, Storage::disk(Config::get('image.disk'))->allFiles());
        Http::assertSentCount(2);
    }
}
