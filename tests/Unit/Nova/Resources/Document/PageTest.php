<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Resources\Document;

use App\Models\Document\Page as PageModel;
use App\Nova\Resources\Document\Page;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Validation\Rule;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Resources\InvalidNovaResourceException;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

/**
 * Class PageTest.
 */
class PageTest extends TestCase
{
    use NovaResourceTest;
    use WithoutEvents;

    /**
     * The Page Resource shall contain Page Fields.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testFields(): void
    {
        $resource = static::novaResource(Page::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.name'));
        $resource->assertHasField(__('nova.slug'));
        $resource->assertHasField(__('nova.body'));
    }

    /**
     * The Page Resource shall contain an ID field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testIdField(): void
    {
        $resource = static::novaResource(Page::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Page Resource shall contain a Created At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testCreatedAtField(): void
    {
        $resource = static::novaResource(Page::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Page Resource shall contain an Updated At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUpdatedAtField(): void
    {
        $resource = static::novaResource(Page::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Page Resource shall contain a Deleted At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDeletedAtField(): void
    {
        $resource = static::novaResource(Page::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Page Resource shall contain a Name field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testNameField(): void
    {
        $resource = static::novaResource(Page::class);

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
     * The Page Resource shall contain a Slug field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testSlugField(): void
    {
        $resource = static::novaResource(Page::class);

        $field = $resource->field(__('nova.slug'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertHasRule('regex:/^[\pL\pM\pN\/_-]+$/u');
        $field->assertHasUpdateRule(Rule::unique(PageModel::TABLE)->ignore(null, PageModel::ATTRIBUTE_ID)->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Page Resource shall contain a Body field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testBodyField(): void
    {
        $resource = static::novaResource(Page::class);

        $field = $resource->field(__('nova.body'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:16777215');
        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
    }
}
