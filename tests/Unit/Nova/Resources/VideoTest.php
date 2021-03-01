<?php

namespace Tests\Unit\Nova\Resources;

use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Filters\VideoLyricsFilter;
use App\Nova\Filters\VideoNcFilter;
use App\Nova\Filters\VideoOverlapFilter;
use App\Nova\Filters\VideoSourceFilter;
use App\Nova\Filters\VideoSubbedFilter;
use App\Nova\Filters\VideoTypeFilter;
use App\Nova\Filters\VideoUncenFilter;
use App\Nova\Video;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use NovaResourceTest, RefreshDatabase, WithFaker;

    /**
     * The Video Resource shall contain Video Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $resource = $this->novaResource(Video::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.basename'));
        $resource->assertHasField(__('nova.filename'));
        $resource->assertHasField(__('nova.path'));
        $resource->assertHasField(__('nova.size'));
        $resource->assertHasField(__('nova.resolution'));
        $resource->assertHasField(__('nova.nc'));
        $resource->assertHasField(__('nova.subbed'));
        $resource->assertHasField(__('nova.lyrics'));
        $resource->assertHasField(__('nova.uncen'));
        $resource->assertHasField(__('nova.overlap'));
        $resource->assertHasField(__('nova.source'));
    }

    /**
     * The Video Resource shall contain an ID field.
     *
     * @return void
     */
    public function testIdField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Video Resource shall contain a Created At field.
     *
     * @return void
     */
    public function testCreatedAtField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Video Resource shall contain an Updated At field.
     *
     * @return void
     */
    public function testUpdatedAtField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Video Resource shall contain a Basename field.
     *
     * @return void
     */
    public function testBasenameField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.basename'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Video Resource shall contain a Filename field.
     *
     * @return void
     */
    public function testFilenameField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.filename'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Video Resource shall contain a Path field.
     *
     * @return void
     */
    public function testPathField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.path'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Video Resource shall contain a Size field.
     *
     * @return void
     */
    public function testSizeField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.size'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Video Resource shall contain a Resolution field.
     *
     * @return void
     */
    public function testResolutionField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.resolution'));

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
     * The Video Resource shall contain an NC field.
     *
     * @return void
     */
    public function testNcField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.nc'));

        $field->assertHasRule('nullable');
        $field->assertHasRule('boolean');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNullable();
        $field->assertSortable();
    }

    /**
     * The Video Resource shall contain a Subbed field.
     *
     * @return void
     */
    public function testSubbedField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.subbed'));

        $field->assertHasRule('nullable');
        $field->assertHasRule('boolean');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNullable();
        $field->assertSortable();
    }

    /**
     * The Video Resource shall contain a Lyrics field.
     *
     * @return void
     */
    public function testLyricsField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.lyrics'));

        $field->assertHasRule('nullable');
        $field->assertHasRule('boolean');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNullable();
        $field->assertSortable();
    }

    /**
     * The Video Resource shall contain a Uncen field.
     *
     * @return void
     */
    public function testUncenField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.uncen'));

        $field->assertHasRule('nullable');
        $field->assertHasRule('boolean');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNullable();
        $field->assertSortable();
    }

    /**
     * The Video Resource shall contain an Overlap field.
     *
     * @return void
     */
    public function testOverlapField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.overlap'));

        $field->assertHasRule('nullable');
        $field->assertHasRule((new EnumValue(VideoOverlap::class, false))->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNullable();
        $field->assertSortable();
    }

    /**
     * The Video Resource shall contain a Source field.
     *
     * @return void
     */
    public function testSourceField()
    {
        $resource = $this->novaResource(Video::class);

        $field = $resource->field(__('nova.source'));

        $field->assertHasRule('nullable');
        $field->assertHasRule((new EnumValue(VideoSource::class, false))->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNullable();
        $field->assertSortable();
    }

    /**
     * The Video Resource shall contain Video Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $resource = $this->novaResource(Video::class);

        $resource->assertHasFilter(VideoNcFilter::class);
        $resource->assertHasFilter(VideoSubbedFilter::class);
        $resource->assertHasFilter(VideoLyricsFilter::class);
        $resource->assertHasFilter(VideoUncenFilter::class);
        $resource->assertHasFilter(VideoOverlapFilter::class);
        $resource->assertHasFilter(VideoSourceFilter::class);
        $resource->assertHasFilter(VideoTypeFilter::class);
        $resource->assertHasFilter(RecentlyCreatedFilter::class);
        $resource->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Video Resource shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $resource = $this->novaResource(Video::class);

        $resource->assertHasNoActions();
    }
}
