<?php

declare(strict_types=1);

namespace Nova\Filters;

use App\Enums\ResourceSite;
use App\Models\ExternalResource;
use App\Nova\Filters\ExternalResourceSiteFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Exceptions\InvalidModelException;
use JoshGaber\NovaUnit\Filters\InvalidNovaFilterException;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

/**
 * Class ExternalResourceSiteTest
 * @package Nova\Filters
 */
class ExternalResourceSiteTest extends TestCase
{
    use NovaFilterTest;
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * The Resource Site Filter shall be a select filter.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testSelectFilter()
    {
        static::novaFilter(ExternalResourceSiteFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Resource Site Filter shall have an option for each ResourceSite instance.
     *
     * @return void
     * @throws InvalidNovaFilterException
     */
    public function testOptions()
    {
        $filter = static::novaFilter(ExternalResourceSiteFilter::class);

        foreach (ResourceSite::getInstances() as $site) {
            $filter->assertHasOption($site->description);
        }
    }

    /**
     * The Resource Site Filter shall filter Resources By Site.
     *
     * @return void
     * @throws InvalidModelException
     * @throws InvalidNovaFilterException
     */
    public function testFilter()
    {
        $site = ResourceSite::getRandomInstance();

        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = static::novaFilter(ExternalResourceSiteFilter::class);

        $response = $filter->apply(ExternalResource::class, $site->value);

        $filteredResources = ExternalResource::where('site', $site->value)->get();
        foreach ($filteredResources as $filteredResource) {
            $response->assertContains($filteredResource);
        }
        $response->assertCount($filteredResources->count());
    }
}
