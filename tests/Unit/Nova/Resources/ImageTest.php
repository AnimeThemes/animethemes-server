<?php

namespace Tests\Unit\Nova\Resources;

use App\Enums\ImageFacet;
use App\Nova\Filters\ImageFacetFilter;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Image;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use NovaResourceTest, WithoutEvents;

    /**
     * The Image Resource shall contain Image Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $resource = $this->novaResource(Image::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.facet'));
        $resource->assertHasField(__('nova.image'));
    }

    /**
     * The Image Resource shall contain an ID field.
     *
     * @return void
     */
    public function testIdField()
    {
        $resource = $this->novaResource(Image::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Image Resource shall contain a Created At field.
     *
     * @return void
     */
    public function testCreatedAtField()
    {
        $resource = $this->novaResource(Image::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Image Resource shall contain an Updated At field.
     *
     * @return void
     */
    public function testUpdatedAtField()
    {
        $resource = $this->novaResource(Image::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Image Resource shall contain a Deleted At field.
     *
     * @return void
     */
    public function testDeletedAtField()
    {
        $resource = $this->novaResource(Image::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Image Resource shall contain a Facet field.
     *
     * @return void
     */
    public function testFacetField()
    {
        $resource = $this->novaResource(Image::class);

        $field = $resource->field(__('nova.facet'));

        $field->assertHasRule('required');
        $field->assertHasRule((new EnumValue(ImageFacet::class, false))->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Image Resource shall contain an Image field.
     *
     * @return void
     */
    public function testImageField()
    {
        $resource = $this->novaResource(Image::class);

        $field = $resource->field(__('nova.image'));

        $field->assertHasCreationRule('required');
        $field->assertHasCreationRule('image');
        $field->assertHasUpdateRule('image');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Image Resource shall contain Image Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $resource = $this->novaResource(Image::class);

        $resource->assertHasFilter(ImageFacetFilter::class);
        $resource->assertHasFilter(RecentlyCreatedFilter::class);
        $resource->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Image Resource shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $resource = $this->novaResource(Image::class);

        $resource->assertHasNoActions();
    }
}
