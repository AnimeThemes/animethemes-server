<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Wiki\Video;

use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Video;
use App\Nova\Filters\Wiki\Video\VideoSourceFilter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class VideoSourceTest.
 */
class VideoSourceTest extends TestCase
{
    use NovaFilterTest;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Source Filter shall be a select filter.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter(): void
    {
        static::novaFilter(VideoSourceFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Source Filter shall have an option for each VideoSource instance.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testOptions(): void
    {
        $filter = static::novaFilter(VideoSourceFilter::class);

        foreach (VideoSource::getInstances() as $source) {
            $filter->assertHasOption($source->description);
        }
    }

    /**
     * The Video Source Filter shall filter Video By Source.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter(): void
    {
        $source = VideoSource::getRandomInstance();

        Video::factory()->count($this->faker->randomDigitNotNull())->create();

        $filter = static::novaFilter(VideoSourceFilter::class);

        $response = $filter->apply(Video::class, $source->value);

        $filteredVideos = Video::query()->where(Video::ATTRIBUTE_SOURCE, $source->value)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
