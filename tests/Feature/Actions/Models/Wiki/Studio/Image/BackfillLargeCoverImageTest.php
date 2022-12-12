<?php

declare(strict_types=1);

namespace Tests\Feature\Actions\Models\Wiki\Studio\Image;

use App\Actions\Models\Wiki\Studio\Image\BackfillLargeCoverImageAction;
use App\Enums\Actions\ActionStatus;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class BackfillLargeCoverImageTest.
 */
class BackfillLargeCoverImageTest extends TestCase
{
    use WithFaker;

    /**
     * The Backfill Large Cover Image Action shall skip the Studio if the relation already exists.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testSkipped(): void
    {
        Http::fake();

        Storage::fake(Config::get('image.disk'));

        $image = Image::factory()->createOne([
            Image::ATTRIBUTE_FACET => ImageFacet::COVER_LARGE,
        ]);

        $studio = Studio::factory()
            ->hasAttached($image)
            ->createOne();

        $action = new BackfillLargeCoverImageAction($studio);

        $result = $action->handle();

        static::assertTrue(ActionStatus::SKIPPED()->is($result->getStatus()));
        static::assertDatabaseCount(Image::class, 1);
        static::assertEmpty(Storage::disk(Config::get('image.disk'))->allFiles());
        Http::assertNothingSent();
    }

    /**
     * The Backfill Large Cover Image Action shall fail if the Studio has no Resources.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testFailedWhenNoResource(): void
    {
        Http::fake();

        Storage::fake(Config::get('image.disk'));

        $studio = Studio::factory()->createOne();

        $action = new BackfillLargeCoverImageAction($studio);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Image::class, 0);
        static::assertEmpty(Storage::disk(Config::get('image.disk'))->allFiles());
        Http::assertNothingSent();
    }

    /**
     * The Backfill Large Cover Image Action shall fail if the Studio has no MAL Resource.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testFailedWhenNoMalResource(): void
    {
        Http::fake();

        Storage::fake(Config::get('image.disk'));

        $site = null;

        while ($site === null) {
            $siteCandidate = ResourceSite::getRandomInstance();
            if (ResourceSite::MAL()->isNot($siteCandidate)) {
                $site = $siteCandidate;
            }
        }

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => $site->value,
        ]);

        $studio = Studio::factory()
            ->hasAttached($resource, [], Studio::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillLargeCoverImageAction($studio);

        $result = $action->handle();

        static::assertTrue($result->hasFailed());
        static::assertDatabaseCount(Image::class, 0);
        static::assertEmpty(Storage::disk(Config::get('image.disk'))->allFiles());
        Http::assertNothingSent();
    }

    /**
     * The Backfill Large Cover Image Action shall pass if the image can be retrieved.
     *
     * @return void
     *
     * @throws RequestException
     */
    public function testPassed(): void
    {
        Storage::fake(Config::get('image.disk'));

        $file = File::fake()->image($this->faker->word().'.jpg');

        Http::fake([
            'https://cdn.myanimelist.net/images/company/*' => Http::response($file->getContent()),
        ]);

        $resource = ExternalResource::factory()->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::MAL,
        ]);

        $studio = Studio::factory()
            ->hasAttached($resource, [], Studio::RELATION_RESOURCES)
            ->createOne();

        $action = new BackfillLargeCoverImageAction($studio);

        $result = $action->handle();

        static::assertTrue(ActionStatus::PASSED()->is($result->getStatus()));
        static::assertDatabaseCount(Image::class, 1);
        static::assertTrue($studio->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE)->exists());
        static::assertCount(1, Storage::disk(Config::get('image.disk'))->allFiles());
        Http::assertSentCount(1);
    }
}
