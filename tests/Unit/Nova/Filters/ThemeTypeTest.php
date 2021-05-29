<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\ThemeType;
use App\Models\Anime;
use App\Models\Theme;
use App\Nova\Filters\ThemeTypeFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class ThemeTypeTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker;

    /**
     * The Theme Status Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(ThemeTypeFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Theme Status Filter shall have an option for each ThemeType instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(ThemeTypeFilter::class);

        foreach (ThemeType::getInstances() as $type) {
            $filter->assertHasOption($type->description);
        }
    }

    /**
     * The Theme Status Filter shall filter Themes By Status.
     *
     * @return void
     */
    public function testFilter()
    {
        $type = ThemeType::getRandomInstance();

        Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $filter = $this->novaFilter(ThemeTypeFilter::class);

        $response = $filter->apply(Theme::class, $type->value);

        $filteredThemes = Theme::where('type', $type->value)->get();
        foreach ($filteredThemes as $filteredTheme) {
            $response->assertContains($filteredTheme);
        }
        $response->assertCount($filteredThemes->count());
    }
}
