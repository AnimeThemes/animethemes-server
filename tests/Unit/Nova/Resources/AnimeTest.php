<?php

namespace Tests\Unit\Nova\Resources;

use App\Enums\AnimeSeason;
use App\Nova\Anime;
use App\Nova\Filters\AnimeSeasonFilter;
use App\Nova\Filters\AnimeYearFilter;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class AnimeTest extends TestCase
{
    use NovaResourceTest, RefreshDatabase, WithFaker;

    /**
     * The Anime Resource shall contain Anime Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $resource = $this->novaResource(Anime::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.name'));
        $resource->assertHasField(__('nova.slug'));
        $resource->assertHasField(__('nova.year'));
        $resource->assertHasField(__('nova.season'));
    }

    /**
     * The Anime Resource shall contain an ID field.
     *
     * @return void
     */
    public function testIdField()
    {
        $resource = $this->novaResource(Anime::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Anime Resource shall contain a Created At field.
     *
     * @return void
     */
    public function testCreatedAtField()
    {
        $resource = $this->novaResource(Anime::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Anime Resource shall contain an Updated At field.
     *
     * @return void
     */
    public function testUpdatedAtField()
    {
        $resource = $this->novaResource(Anime::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Anime Resource shall contain a Deleted At field.
     *
     * @return void
     */
    public function testDeletedAtField()
    {
        $resource = $this->novaResource(Anime::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Anime Resource shall contain a Name field.
     *
     * @return void
     */
    public function testNameField()
    {
        $resource = $this->novaResource(Anime::class);

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
     * The Anime Resource shall contain a Slug field.
     *
     * @return void
     */
    public function testSlugField()
    {
        $resource = $this->novaResource(Anime::class);

        $field = $resource->field(__('nova.slug'));

        $field->assertHasRule('required');
        $field->assertHasRule('max:192');
        $field->assertHasRule('alpha_dash');
        $field->assertHasUpdateRule('unique:anime,slug,{{resourceId}},anime_id');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Anime Resource shall contain a Year field.
     *
     * @return void
     */
    public function testYearField()
    {
        $resource = $this->novaResource(Anime::class);

        $field = $resource->field(__('nova.year'));

        $field->assertHasRule('required');
        $field->assertHasRule('digits:4');
        $field->assertHasRule('integer');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Anime Resource shall contain a Season field.
     *
     * @return void
     */
    public function testSeasonField()
    {
        $resource = $this->novaResource(Anime::class);

        $field = $resource->field(__('nova.season'));

        $field->assertHasRule('required');
        $field->assertHasRule(new EnumValue(AnimeSeason::class, false));
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Anime Resource shall contain a Synopsis field.
     *
     * @return void
     */
    public function testSynopsisField()
    {
        $resource = $this->novaResource(Anime::class);

        $field = $resource->field(__('nova.synopsis'));

        $field->assertHasRule('max:65535');
        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNullable();
    }

    /**
     * The Anime Resource shall contain Anime Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $resource = $this->novaResource(Anime::class);

        $resource->assertHasFilter(AnimeSeasonFilter::class);
        $resource->assertHasFilter(AnimeYearFilter::class);
        $resource->assertHasFilter(RecentlyCreatedFilter::class);
        $resource->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Anime Resource shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $resource = $this->novaResource(Anime::class);

        $resource->assertHasNoActions();
    }
}
