<?php

namespace Tests\Unit\Nova\Filters;

use App\Enums\ResourceSite;
use App\Models\ExternalResource;
use App\Nova\Filters\ExternalResourceSiteFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Filters\NovaFilterTest;
use Tests\TestCase;

class ExternalResourceSiteTest extends TestCase
{
    use NovaFilterTest, RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The Resource Site Filter shall be a select filter.
     *
     * @return void
     */
    public function testSelectFilter()
    {
        $this->novaFilter(ExternalResourceSiteFilter::class)
            ->assertSelectFilter();
    }

    /**
     * The Resource Site Filter shall have an option for each ResourceSite instance.
     *
     * @return void
     */
    public function testOptions()
    {
        $filter = $this->novaFilter(ExternalResourceSiteFilter::class);

        foreach (ResourceSite::getInstances() as $site) {
            $filter->assertHasOption($site->description);
        }
    }

    /**
     * The Resource Site Filter shall filter Resources By Site.
     *
     * @return void
     */
    public function testFilter()
    {
        $site = ResourceSite::getRandomInstance();

        ExternalResource::factory()->count($this->faker->randomDigitNotNull)->create();

        $filter = $this->novaFilter(ExternalResourceSiteFilter::class);

        $response = $filter->apply(ExternalResource::class, $site->value);

        $filtered_resources = ExternalResource::where('site', $site->value)->get();
        foreach ($filtered_resources as $filtered_resource) {
            $response->assertContains($filtered_resource);
        }
        $response->assertCount($filtered_resources->count());
    }
}
