<?php

declare(strict_types=1);

namespace Tests\Unit\Nova\Resources\Billing;

use App\Enums\Models\Billing\Service;
use App\Nova\Filters\Base\CreatedEndDateFilter;
use App\Nova\Filters\Base\CreatedStartDateFilter;
use App\Nova\Filters\Base\DeletedEndDateFilter;
use App\Nova\Filters\Base\DeletedStartDateFilter;
use App\Nova\Filters\Base\UpdatedEndDateFilter;
use App\Nova\Filters\Base\UpdatedStartDateFilter;
use App\Nova\Filters\Billing\ServiceFilter;
use App\Nova\Resources\Billing\Transaction;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Fields\FieldNotFoundException;
use JoshGaber\NovaUnit\Resources\InvalidNovaResourceException;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

/**
 * Class TransactionTest.
 */
class TransactionTest extends TestCase
{
    use NovaResourceTest;
    use WithoutEvents;

    /**
     * The Transaction Resource shall contain Transaction Fields.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testFields(): void
    {
        $transaction = static::novaResource(Transaction::class);

        $transaction->assertHasField(__('nova.id'));
        $transaction->assertHasField(__('nova.created_at'));
        $transaction->assertHasField(__('nova.updated_at'));
        $transaction->assertHasField(__('nova.deleted_at'));
        $transaction->assertHasField(__('nova.date'));
        $transaction->assertHasField(__('nova.service'));
        $transaction->assertHasField(__('nova.description'));
        $transaction->assertHasField(__('nova.amount'));
        $transaction->assertHasField(__('nova.external_id'));
    }

    /**
     * The Transaction Resource shall contain an ID field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testIdField(): void
    {
        $transaction = static::novaResource(Transaction::class);

        $field = $transaction->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Transaction Resource shall contain a Created At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testCreatedAtField(): void
    {
        $transaction = static::novaResource(Transaction::class);

        $field = $transaction->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Transaction Resource shall contain an Updated At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testUpdatedAtField(): void
    {
        $transaction = static::novaResource(Transaction::class);

        $field = $transaction->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Transaction Resource shall contain a Deleted At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDeletedAtField(): void
    {
        $transaction = static::novaResource(Transaction::class);

        $field = $transaction->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Transaction Resource shall contain a Deleted At field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDateField(): void
    {
        $transaction = static::novaResource(Transaction::class);

        $field = $transaction->field(__('nova.date'));

        $field->assertHasRule('required');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Transaction Resource shall contain a Service field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testServiceField(): void
    {
        $transaction = static::novaResource(Transaction::class);

        $field = $transaction->field(__('nova.service'));

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
     * The Transaction Resource shall contain a Description field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testDescriptionField(): void
    {
        $transaction = static::novaResource(Transaction::class);

        $field = $transaction->field(__('nova.description'));

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
     * The Transaction Resource shall contain an Amount field.
     *
     * @return void
     *
     * @throws FieldNotFoundException
     * @throws InvalidNovaResourceException
     */
    public function testAmountField(): void
    {
        $transaction = static::novaResource(Transaction::class);

        $field = $transaction->field(__('nova.amount'));

        $field->assertHasRule('required');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Transaction Resource shall contain Transaction Filters.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testFilters(): void
    {
        $resource = static::novaResource(Transaction::class);

        $resource->assertHasFilter(ServiceFilter::class);
        $resource->assertHasFilter(CreatedStartDateFilter::class);
        $resource->assertHasFilter(CreatedEndDateFilter::class);
        $resource->assertHasFilter(UpdatedStartDateFilter::class);
        $resource->assertHasFilter(UpdatedEndDateFilter::class);
        $resource->assertHasFilter(DeletedStartDateFilter::class);
        $resource->assertHasFilter(DeletedEndDateFilter::class);
    }

    /**
     * The Transaction Resource shall contain no Actions.
     *
     * @return void
     *
     * @throws InvalidNovaResourceException
     */
    public function testActions(): void
    {
        $resource = static::novaResource(Transaction::class);

        $resource->assertHasNoActions();
    }
}
