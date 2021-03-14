<?php

namespace Tests\Unit\Nova\Resources;

use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Series;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class SeriesTest extends TestCase
{
    use NovaResourceTest, WithoutEvents;

    /**
     * The Series Resource shall contain Series Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $resource = $this->novaResource(Series::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.name'));
        $resource->assertHasField(__('nova.slug'));
    }

    /**
     * The Series Resource shall contain an ID field.
     *
     * @return void
     */
    public function testIdField()
    {
        $resource = $this->novaResource(Series::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Series Resource shall contain a Created At field.
     *
     * @return void
     */
    public function testCreatedAtField()
    {
        $resource = $this->novaResource(Series::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Series Resource shall contain an Updated At field.
     *
     * @return void
     */
    public function testUpdatedAtField()
    {
        $resource = $this->novaResource(Series::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Series Resource shall contain a Deleted At field.
     *
     * @return void
     */
    public function testDeletedAtField()
    {
        $resource = $this->novaResource(Series::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Series Resource shall contain a Name field.
     *
     * @return void
     */
    public function testNameField()
    {
        $resource = $this->novaResource(Series::class);

        $field = $resource->field(__('nova.name'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Series Resource shall contain a Slug field.
     *
     * @return void
     */
    public function testSlugField()
    {
        $resource = $this->novaResource(Series::class);

        $field = $resource->field(__('nova.slug'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertHasRule('alpha_dash');
        $field->assertHasUpdateRule('unique:series,slug,{{resourceId}},series_id');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Series Resource shall contain Series Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $resource = $this->novaResource(Series::class);

        $resource->assertHasFilter(RecentlyCreatedFilter::class);
        $resource->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Series Resource shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $resource = $this->novaResource(Series::class);

        $resource->assertHasNoActions();
    }
}
