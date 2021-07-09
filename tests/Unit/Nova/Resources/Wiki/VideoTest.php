<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Resources\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Filters\Wiki\VideoLyricsFilter;
use App\Nova\Filters\Wiki\VideoNcFilter;
use App\Nova\Filters\Wiki\VideoOverlapFilter;
use App\Nova\Filters\Wiki\VideoSourceFilter;
use App\Nova\Filters\Wiki\VideoSubbedFilter;
use App\Nova\Filters\Wiki\VideoTypeFilter;
use App\Nova\Filters\Wiki\VideoUncenFilter;
use App\Nova\Resources\Wiki\Video;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Resources\InvalidNovaResourceException;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

/**
 * Class VideoTest.
 */
class VideoTest extends TestCase
{
    use NovaResourceTest;
    use WithoutEvents;

    /**
     * The Video Resource shall contain Video Fields.
     *
     * @return void
     * @throws InvalidNovaResourceException
     */
    public function testFields()
    {
        $resource = static::novaResource(Video::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testIdField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testCreatedAtField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUpdatedAtField()
    {
        $resource = static::novaResource(Video::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Video Resource shall contain a Deleted At field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDeletedAtField()
    {
        $resource = static::novaResource(Video::class);

        $field = $resource->field(__('nova.deleted_at'));

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testBasenameField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testFilenameField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testPathField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testSizeField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testResolutionField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testNcField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testSubbedField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testLyricsField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUncenField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testOverlapField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testSourceField()
    {
        $resource = static::novaResource(Video::class);

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
     * @throws InvalidNovaResourceException
     */
    public function testFilters()
    {
        $resource = static::novaResource(Video::class);

        $resource->assertHasFilter(VideoNcFilter::class);
        $resource->assertHasFilter(VideoSubbedFilter::class);
        $resource->assertHasFilter(VideoLyricsFilter::class);
        $resource->assertHasFilter(VideoUncenFilter::class);
        $resource->assertHasFilter(VideoOverlapFilter::class);
        $resource->assertHasFilter(VideoSourceFilter::class);
        $resource->assertHasFilter(VideoTypeFilter::class);
        $resource->assertHasFilter(CreatedStartDateFilter::class);
        $resource->assertHasFilter(CreatedEndDateFilter::class);
        $resource->assertHasFilter(UpdatedStartDateFilter::class);
        $resource->assertHasFilter(UpdatedEndDateFilter::class);
        $resource->assertHasFilter(DeletedStartDateFilter::class);
        $resource->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Video Resource shall contain no Actions.
     *
     * @return void
     * @throws InvalidNovaResourceException
     */
    public function testActions()
    {
        $resource = static::novaResource(Video::class);

        $resource->assertHasNoActions();
    }
}