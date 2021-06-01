<?php declare(strict_types=1);

namespace Nova\Resources;

use App\Nova\Entry;
use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\EntryNsfwFilter;
use App\Nova\Filters\EntrySpoilerFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Resources\InvalidNovaResourceException;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

/**
 * Class EntryTest
 * @package Nova\Resources
 */
class EntryTest extends TestCase
{
    use NovaResourceTest;

    /**
     * The Entry Resource shall contain Entry Fields.
     *
     * @return void
     * @throws InvalidNovaResourceException
     */
    public function testFields()
    {
        $resource = static::novaResource(Entry::class);

        $resource->assertHasField(__('nova.id'));
        $resource->assertHasField(__('nova.created_at'));
        $resource->assertHasField(__('nova.updated_at'));
        $resource->assertHasField(__('nova.deleted_at'));
        $resource->assertHasField(__('nova.version'));
        $resource->assertHasField(__('nova.episodes'));
        $resource->assertHasField(__('nova.nsfw'));
        $resource->assertHasField(__('nova.spoiler'));
        $resource->assertHasField(__('nova.notes'));
    }

    /**
     * The Entry Resource shall contain an ID field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testIdField()
    {
        $resource = static::novaResource(Entry::class);

        $field = $resource->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Entry Resource shall contain a Created At field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testCreatedAtField()
    {
        $resource = static::novaResource(Entry::class);

        $field = $resource->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Entry Resource shall contain an Updated At field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUpdatedAtField()
    {
        $resource = static::novaResource(Entry::class);

        $field = $resource->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Entry Resource shall contain a Deleted At field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDeletedAtField()
    {
        $resource = static::novaResource(Entry::class);

        $field = $resource->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Entry Resource shall contain a Version field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testVersionField()
    {
        $resource = static::novaResource(Entry::class);

        $field = $resource->field(__('nova.version'));

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
     * The Entry Resource shall contain a Episodes field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testEpisodesField()
    {
        $resource = static::novaResource(Entry::class);

        $field = $resource->field(__('nova.episodes'));

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
     * The Entry Resource shall contain a NSFW field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testNsfwField()
    {
        $resource = static::novaResource(Entry::class);

        $field = $resource->field(__('nova.nsfw'));

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
     * The Entry Resource shall contain a Spoiler field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testSpoilerField()
    {
        $resource = static::novaResource(Entry::class);

        $field = $resource->field(__('nova.spoiler'));

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
     * The Entry Resource shall contain a Notes field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testNotesField()
    {
        $resource = static::novaResource(Entry::class);

        $field = $resource->field(__('nova.notes'));

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
     * The Entry Resource shall contain Entry Filters.
     *
     * @return void
     * @throws InvalidNovaResourceException
     */
    public function testFilters()
    {
        $resource = static::novaResource(Entry::class);

        $resource->assertHasFilter(EntryNsfwFilter::class);
        $resource->assertHasFilter(EntrySpoilerFilter::class);
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
     * @throws InvalidNovaResourceException
     */
    public function testActions()
    {
        $resource = static::novaResource(Entry::class);

        $resource->assertHasNoActions();
    }
}
