<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Resources\Wiki\Anime;

use App\Enums\Models\Wiki\ThemeType;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Filters\Wiki\Anime\Theme\ThemeTypeFilter;
use App\Nova\Resources\Wiki\Anime\Theme;
use BenSampo\Enum\Rules\EnumValue;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Resources\InvalidNovaResourceException;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

/**
 * Class ThemeTest.
 */
class ThemeTest extends TestCase
{
    use NovaResourceTest;

    /**
     * The Theme Resource shall contain Theme Fields.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testFields(): void
    {
        $resource = static::novaResource(Theme::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.type'));
        $resource->assertHasField(__('nova.sequence'));
        $resource->assertHasField(__('nova.group'));
        $resource->assertHasField(__('nova.slug'));
    }

    /**
     * The Theme Resource shall contain an ID field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testIdField(): void
    {
        $resource = static::novaResource(Theme::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Theme Resource shall contain a Created At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testCreatedAtField(): void
    {
        $resource = static::novaResource(Theme::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Theme Resource shall contain an Updated At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUpdatedAtField(): void
    {
        $resource = static::novaResource(Theme::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Theme Resource shall contain a Deleted At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDeletedAtField(): void
    {
        $resource = static::novaResource(Theme::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Theme Resource shall contain a Type field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testTypeField(): void
    {
        $resource = static::novaResource(Theme::class);

        $field = $resource->field(__('nova.type'));

        $field->assertHasRule('required');
        $field->assertHasRule((new EnumValue(ThemeType::class, false))->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Theme Resource shall contain a Sequence field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testSequenceField(): void
    {
        $resource = static::novaResource(Theme::class);

        $field = $resource->field(__('nova.sequence'));

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
     * The Theme Resource shall contain a Group field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testGroupField(): void
    {
        $resource = static::novaResource(Theme::class);

        $field = $resource->field(__('nova.group'));

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
     * The Theme Resource shall contain a Slug field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testSlugField(): void
    {
        $resource = static::novaResource(Theme::class);

        $field = $resource->field(__('nova.slug'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertHasRule('alpha_dash');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Theme Resource shall contain Theme Filters.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testFilters(): void
    {
        $resource = static::novaResource(Theme::class);

        $resource->assertHasFilter(ThemeTypeFilter::class);
        $resource->assertHasFilter(CreatedStartDateFilter::class);
        $resource->assertHasFilter(CreatedEndDateFilter::class);
        $resource->assertHasFilter(UpdatedStartDateFilter::class);
        $resource->assertHasFilter(UpdatedEndDateFilter::class);
        $resource->assertHasFilter(DeletedStartDateFilter::class);
        $resource->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Entry Resource shall contain no Actions.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testActions(): void
    {
        $resource = static::novaResource(Theme::class);

        $resource->assertHasNoActions();
    }
}
