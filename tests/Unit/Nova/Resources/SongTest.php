<?php

namespace Tests\Unit\Nova\Resources;

use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Song;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class SongTest extends TestCase
{
    use NovaResourceTest, WithoutEvents;

    /**
     * The Song Resource shall contain Song Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $resource = $this->novaResource(Song::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.title'));
    }

    /**
     * The Song Resource shall contain an ID field.
     *
     * @return void
     */
    public function testIdField()
    {
        $resource = $this->novaResource(Song::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Song Resource shall contain a Created At field.
     *
     * @return void
     */
    public function testCreatedAtField()
    {
        $resource = $this->novaResource(Song::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Song Resource shall contain an Updated At field.
     *
     * @return void
     */
    public function testUpdatedAtField()
    {
        $resource = $this->novaResource(Song::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Song Resource shall contain a Deleted At field.
     *
     * @return void
     */
    public function testDeletedAtField()
    {
        $resource = $this->novaResource(Song::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Song Resource shall contain a Title field.
     *
     * @return void
     */
    public function testTitleField()
    {
        $resource = $this->novaResource(Song::class);

        $field = $resource->field(__('nova.title'));

        $field->assertHasRule('nullable');
        $field->assertHasRule('max:192');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNullable();
        $field->assertSortable();
    }

    /**
     * The Song Resource shall contain Song Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $resource = $this->novaResource(Song::class);

        $resource->assertHasFilter(RecentlyCreatedFilter::class);
        $resource->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Song Resource shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $resource = $this->novaResource(Song::class);

        $resource->assertHasNoActions();
    }
}
