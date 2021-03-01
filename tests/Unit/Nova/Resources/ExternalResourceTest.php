<?php

namespace Tests\Unit\Nova\Resources;

use App\Enums\ResourceSite;
use App\Nova\ExternalResource;
use App\Nova\Filters\ExternalResourceSiteFilter;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class ExternalResourceTest extends TestCase
{
    use NovaResourceTest, RefreshDatabase, WithFaker;

    /**
     * The External Resource shall contain Resource Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $resource = $this->novaResource(ExternalResource::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.link'));
        $resource->assertHasField(__('nova.external_id'));
    }

    /**
     * The External Resource shall contain an ID field.
     *
     * @return void
     */
    public function testIdField()
    {
        $resource = $this->novaResource(ExternalResource::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The External Resource shall contain a Created At field.
     *
     * @return void
     */
    public function testCreatedAtField()
    {
        $resource = $this->novaResource(ExternalResource::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The External Resource shall contain an Updated At field.
     *
     * @return void
     */
    public function testUpdatedAtField()
    {
        $resource = $this->novaResource(ExternalResource::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The External Resource shall contain a Site field.
     *
     * @return void
     */
    public function testSiteField()
    {
        $resource = $this->novaResource(ExternalResource::class);

        $field = $resource->field(__('nova.site'));

        $field->assertHasRule('required');
        $field->assertHasRule((new EnumValue(ResourceSite::class, false))->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The External Resource shall contain a Link field.
     *
     * @return void
     */
    public function testLinkField()
    {
        $resource = $this->novaResource(ExternalResource::class);

        $field = $resource->field(__('nova.link'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertHasRule('url');
        $field->assertHasCreationRule('unique:resource,link');
        $field->assertHasUpdateRule('unique:resource,link,{{resourceId}},resource_id');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The External Resource shall contain an External Id field.
     *
     * @return void
     */
    public function testExternalIdField()
    {
        $resource = $this->novaResource(ExternalResource::class);

        $field = $resource->field(__('nova.external_id'));

        $field->assertHasRule('nullable');
        $field->assertHasRule('integer');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNullable();
        $field->assertSortable();
    }

    /**
     * The Anime Resource shall contain Anime Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $resource = $this->novaResource(ExternalResource::class);

        $resource->assertHasFilter(ExternalResourceSiteFilter::class);
        $resource->assertHasFilter(RecentlyCreatedFilter::class);
        $resource->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The External Resource shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $resource = $this->novaResource(ExternalResource::class);

        $resource->assertHasNoActions();
    }
}
