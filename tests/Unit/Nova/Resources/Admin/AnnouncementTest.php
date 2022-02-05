<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Resources\Admin;

use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Resources\Admin\Announcement;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Resources\InvalidNovaResourceException;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

/**
 * Class AnnouncementTest.
 */
class AnnouncementTest extends TestCase
{
    use NovaResourceTest;
    use WithoutEvents;

    /**
     * The Announcement Resource shall contain Announcement Fields.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testFields(): void
    {
        $resource = static::novaResource(Announcement::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.content'));
    }

    /**
     * The Announcement Resource shall contain an ID field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testIdField(): void
    {
        $resource = static::novaResource(Announcement::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Announcement Resource shall contain a Created At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testCreatedAtField(): void
    {
        $resource = static::novaResource(Announcement::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Announcement Resource shall contain a Deleted At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUpdatedAtField(): void
    {
        $resource = static::novaResource(Announcement::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Announcement Resource shall contain an Updated At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDeletedAtField(): void
    {
        $resource = static::novaResource(Announcement::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Announcement Resource shall contain a Synopsis field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testContentField(): void
    {
        $resource = static::novaResource(Announcement::class);

        $field = $resource->field(__('nova.content'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:65535');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
    }

    /**
     * The Announcement Resource shall contain Announcement Filters.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testFilters(): void
    {
        $resource = static::novaResource(Announcement::class);

        $resource->assertHasFilter(CreatedStartDateFilter::class);
        $resource->assertHasFilter(CreatedEndDateFilter::class);
        $resource->assertHasFilter(UpdatedStartDateFilter::class);
        $resource->assertHasFilter(UpdatedEndDateFilter::class);
        $resource->assertHasFilter(DeletedStartDateFilter::class);
        $resource->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Announcement Resource shall contain no Actions.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testActions(): void
    {
        $resource = static::novaResource(Announcement::class);

        $resource->assertHasNoActions();
    }
}
