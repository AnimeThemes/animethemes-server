<?php

declare(strict_types=1);

namespace Nova\Resources;

use App\Enums\Billing\Frequency;
use App\Enums\Billing\Service;
use App\Nova\Balance;
use App\Nova\Filters\BillingServiceFilter;
use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Resources\InvalidNovaResourceException;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

/**
 * Class BalanceTest.
 */
class BalanceTest extends TestCase
{
    use NovaResourceTest;
    use WithoutEvents;

    /**
     * The Balance Resource shall contain Balance Fields.
     *
     * @return void
     * @throws InvalidNovaResourceException
     */
    public function testFields()
    {
        $balance = static::novaResource(Balance::class);

        $balance->assertHasField(__('nova.id'));
        $balance->assertHasField(__('nova.created_at'));
        $balance->assertHasField(__('nova.updated_at'));
        $balance->assertHasField(__('nova.deleted_at'));
        $balance->assertHasField(__('nova.date'));
        $balance->assertHasField(__('nova.service'));
        $balance->assertHasField(__('nova.frequency'));
        $balance->assertHasField(__('nova.usage'));
        $balance->assertHasField(__('nova.balance'));
    }

    /**
     * The Balance Resource shall contain an ID field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testIdField()
    {
        $balance = static::novaResource(Balance::class);

        $field = $balance->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Balance Resource shall contain a Created At field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testCreatedAtField()
    {
        $balance = static::novaResource(Balance::class);

        $field = $balance->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Balance Resource shall contain an Updated At field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUpdatedAtField()
    {
        $balance = static::novaResource(Balance::class);

        $field = $balance->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Balance Resource shall contain a Deleted At field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDeletedAtField()
    {
        $balance = static::novaResource(Balance::class);

        $field = $balance->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Balance Resource shall contain a Deleted At field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDateField()
    {
        $balance = static::novaResource(Balance::class);

        $field = $balance->field(__('nova.date'));

        $field->assertHasRule('required');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Balance Resource shall contain a Service field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testServiceField()
    {
        $balance = static::novaResource(Balance::class);

        $field = $balance->field(__('nova.service'));

        $field->assertHasRule('required');
        $field->assertHasRule((new EnumValue(Service::class, false))->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Balance Resource shall contain a Frequency field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testFrequencyField()
    {
        $balance = static::novaResource(Balance::class);

        $field = $balance->field(__('nova.frequency'));

        $field->assertHasRule('required');
        $field->assertHasRule((new EnumValue(Frequency::class, false))->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Balance Resource shall contain a Usage field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUsageField()
    {
        $balance = static::novaResource(Balance::class);

        $field = $balance->field(__('nova.usage'));

        $field->assertHasRule('required');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Balance Resource shall contain a Balance field.
     *
     * @return void
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testBalanceField()
    {
        $balance = static::novaResource(Balance::class);

        $field = $balance->field(__('nova.balance'));

        $field->assertHasRule('required');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Balance Resource shall contain Balance Filters.
     *
     * @return void
     * @throws InvalidNovaResourceException
     */
    public function testFilters()
    {
        $resource = static::novaResource(Balance::class);

        $resource->assertHasFilter(BillingServiceFilter::class);
        $resource->assertHasFilter(CreatedStartDateFilter::class);
        $resource->assertHasFilter(CreatedEndDateFilter::class);
        $resource->assertHasFilter(UpdatedStartDateFilter::class);
        $resource->assertHasFilter(UpdatedEndDateFilter::class);
        $resource->assertHasFilter(DeletedStartDateFilter::class);
        $resource->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Balance Resource shall contain no Actions.
     *
     * @return void
     * @throws InvalidNovaResourceException
     */
    public function testActions()
    {
        $resource = static::novaResource(Balance::class);

        $resource->assertHasNoActions();
    }
}
