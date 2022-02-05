<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Wiki\Video;

use App\Models\Wiki\Video;
use App\Nova\Filters\Wiki\Video\VideoNcFilter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class VideoNcTest.
 */
class VideoNcTest extends TestCase
{
    use NovaFilterTest;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Nc Filter shall be a select filter.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter(): void
    {
        static::novaFilter(VideoNcFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Nc Filter shall have Yes and No options.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testOptions(): void
    {
        $filter = static::novaFilter(VideoNcFilter::class);

        $filter->assertHasOption(__('nova.no'));
        $filter->assertHasOption(__('nova.yes'));
    }

    /**
     * The Video Nc Filter shall filter Videos By Nc.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter(): void
    {
        $ncFilter = $this->faker->boolean();

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $filter = static::novaFilter(VideoNcFilter::class);

        $response = $filter->apply(Video::class, $ncFilter);

        $filteredVideos = Video::query()->where(Video::ATTRIBUTE_NC, $ncFilter)->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
