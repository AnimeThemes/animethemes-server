<?php

namespace Tests\Unit\Nova\Resources;

use App\Enums\ThemeType;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Filters\ThemeTypeFilter;
use App\Nova\Theme;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use NovaResourceTest, RefreshDatabase, WithFaker;

    /**
     * The Theme Resource shall contain Theme Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $resource = $this->novaResource(Theme::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.type'));
        $resource->assertHasField(__('nova.sequence'));
        $resource->assertHasField(__('nova.group'));
        $resource->assertHasField(__('nova.slug'));
    }

    /**
     * The Theme Resource shall contain an ID field.
     *
     * @return void
     */
    public function testIdField()
    {
        $resource = $this->novaResource(Theme::class);

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
     */
    public function testCreatedAtField()
    {
        $resource = $this->novaResource(Theme::class);

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
     */
    public function testUpdatedAtField()
    {
        $resource = $this->novaResource(Theme::class);

        $field = $resource->field(__('nova.updated_at'));

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
     */
    public function testTypeField()
    {
        $resource = $this->novaResource(Theme::class);

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
     */
    public function testSequenceField()
    {
        $resource = $this->novaResource(Theme::class);

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
     */
    public function testGroupField()
    {
        $resource = $this->novaResource(Theme::class);

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
     */
    public function testSlugField()
    {
        $resource = $this->novaResource(Theme::class);

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
     */
    public function testFilters()
    {
        $resource = $this->novaResource(Theme::class);

        $resource->assertHasFilter(ThemeTypeFilter::class);
        $resource->assertHasFilter(RecentlyCreatedFilter::class);
        $resource->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Entry Resource shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $resource = $this->novaResource(Theme::class);

        $resource->assertHasNoActions();
    }
}
