<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Wiki\Anime\Theme;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Nova\Filters\Wiki\Anime\Theme\ThemeTypeFilter;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class ThemeTypeTest.
 */
class ThemeTypeTest extends TestCase
{
    use NovaFilterTest;
    use WithFaker;

    /**
     * The Theme Status Filter shall be a select filter.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(ThemeTypeFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Theme Status Filter shall have an option for each ThemeType instance.
     *
     * @return void
     *
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(ThemeTypeFilter::class);

        foreach (ThemeType::getInstances() as $type) {
            $filter->assertHasOption($type->description);
        }
    }

    /**
     * The Theme Status Filter shall filter Themes By Status.
     *
     * @return void
     *
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $type = ThemeType::getRandomInstance();

        AnimeTheme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $filter = static::novaFilter(ThemeTypeFilter::class);

        $response = $filter->apply(AnimeTheme::class, $type->value);

        $filteredThemes = AnimeTheme::query()->where(AnimeTheme::ATTRIBUTE_TYPE, $type->value)->get();
        foreach ($filteredThemes as $filteredTheme) {
            $response->assertContains($filteredTheme);
        }
        $response->assertCount($filteredThemes->count());
    }
}
