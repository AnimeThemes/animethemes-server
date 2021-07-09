<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Filters\Wiki;

use App\Enums\Models\Wiki\ThemeType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Theme;
use App\Nova\Filters\Wiki\ThemeTypeFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    use RefreshDatabase;
    use WithFaker;

    /**
     * The Theme Status Filter shall be a select filter.
     *
     * @return void
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
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $type = ThemeType::getRandomInstance();

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = static::novaFilter(ThemeTypeFilter::class);

        $response = $filter->apply(Theme::class, $type->value);

        $filteredThemes = Theme::where('type', $type->value)->get();
        foreach ($filteredThemes as $filteredTheme) {
            $response->assertContains($filteredTheme);
        }
        $response->assertCount($filteredThemes->count());
    }
}