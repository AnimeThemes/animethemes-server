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
use App\Nova\Filters\Wiki\Video\VideoLyricsFilter;
use App\Nova\Filters\Wiki\Video\VideoNcFilter;
use App\Nova\Filters\Wiki\Video\VideoOverlapFilter;
use App\Nova\Filters\Wiki\Video\VideoSourceFilter;
use App\Nova\Filters\Wiki\Video\VideoSubbedFilter;
use App\Nova\Filters\Wiki\Video\VideoTypeFilter;
use App\Nova\Filters\Wiki\Video\VideoUncenFilter;
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
     *
     * @throws InvalidNovaResourceException
     */
    public function testFields(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testIdField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testCreatedAtField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUpdatedAtField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDeletedAtField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testBasenameField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testFilenameField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testPathField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testSizeField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testResolutionField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testNcField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testSubbedField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testLyricsField(): void
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
     * The Video Resource shall contain an Uncen field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUncenField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testOverlapField(): void
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
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testSourceField(): void
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
     *
     * @throws InvalidNovaResourceException
     */
    public function testFilters(): void
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
     *
     * @throws InvalidNovaResourceException
     */
    public function testActions(): void
    {
        $resource = static::novaResource(Video::class);

        $resource->assertHasNoActions();
    }
}
