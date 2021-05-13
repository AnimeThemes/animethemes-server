<?php

namespace Tests\Unit\Nova\Resources;

use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use App\Nova\Synonym;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class SynonymTest extends TestCase
{
    use NovaResourceTest, WithoutEvents;

    /**
     * The Synonym Resource shall contain Synonym Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $resource = $this->novaResource(Synonym::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.text'));
    }

    /**
     * The Synonym Resource shall contain an ID field.
     *
     * @return void
     */
    public function testIdField()
    {
        $resource = $this->novaResource(Synonym::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Synonym Resource shall contain a Created At field.
     *
     * @return void
     */
    public function testCreatedAtField()
    {
        $resource = $this->novaResource(Synonym::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Synonym Resource shall contain an Updated At field.
     *
     * @return void
     */
    public function testUpdatedAtField()
    {
        $resource = $this->novaResource(Synonym::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Synonym Resource shall contain a Deleted At field.
     *
     * @return void
     */
    public function testDeletedAtField()
    {
        $resource = $this->novaResource(Synonym::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Synonym Resource shall contain a Text field.
     *
     * @return void
     */
    public function testTextField()
    {
        $resource = $this->novaResource(Synonym::class);

        $field = $resource->field(__('nova.text'));

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
     * The Synonym Resource shall contain Synonym Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $resource = $this->novaResource(Synonym::class);

        $resource->assertHasFilter(CreatedStartDateFilter::class);
        $resource->assertHasFilter(CreatedEndDateFilter::class);
        $resource->assertHasFilter(UpdatedStartDateFilter::class);
        $resource->assertHasFilter(UpdatedEndDateFilter::class);
        $resource->assertHasFilter(DeletedStartDateFilter::class);
        $resource->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Synonym Resource shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $resource = $this->novaResource(Synonym::class);

        $resource->assertHasNoActions();
    }
}
