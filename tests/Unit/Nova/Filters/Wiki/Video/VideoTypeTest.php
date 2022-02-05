<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Wiki\Video;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Wiki\Video;
use App\Nova\Filters\Wiki\Video\VideoTypeFilter;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class VideoTypeTest.
 */
class VideoTypeTest extends TestCase
{
    use NovaFilterTest;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Video Type Filter shall be a select filter.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter(): void
    {
        static::novaFilter(VideoTypeFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Video Type Filter shall have an Anime and Misc option.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testOptions(): void
    {
        $filter = static::novaFilter(VideoTypeFilter::class);

        $filter->assertHasOption(__('nova.anime'));
        $filter->assertHasOption(__('nova.misc'));
    }

    /**
     * The Video Type Filter shall filter Videos By Path.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testAnimeFilter(): void
    {
        Video::factory()->count($this->faker->randomDigitNotNull())->create();

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([
                'path' => 'misc'.$this->faker->word(),
            ]);

        $filter = static::novaFilter(VideoTypeFilter::class);

        $response = $filter->apply(Video::class, VideoTypeFilter::ANIME);

        $filteredVideos = Video::query()->where(Video::ATTRIBUTE_PATH, ComparisonOperator::NOTLIKE(), 'misc%')->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }

    /**
     * The Video Type Filter shall filter Videos By Path.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testMiscFilter(): void
    {
        Video::factory()->count($this->faker->randomDigitNotNull())->create();

        Video::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create([
                'path' => 'misc'.$this->faker->word(),
            ]);

        $filter = static::novaFilter(VideoTypeFilter::class);

        $response = $filter->apply(Video::class, VideoTypeFilter::MISC);

        $filteredVideos = Video::query()->where(Video::ATTRIBUTE_PATH, ComparisonOperator::LIKE(), 'misc%')->get();
        foreach ($filteredVideos as $filteredVideo) {
            $response->assertContains($filteredVideo);
        }
        $response->assertCount($filteredVideos->count());
    }
}
