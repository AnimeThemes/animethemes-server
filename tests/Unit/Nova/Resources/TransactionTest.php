<?php

namespace Tests\Unit\Nova\Resources;

use App\Enums\Billing\Service;
use App\Nova\Filters\BillingServiceFilter;
use App\Nova\Filters\CreatedEndDateFilter;
use App\Nova\Filters\CreatedStartDateFilter;
use App\Nova\Filters\DeletedEndDateFilter;
use App\Nova\Filters\DeletedStartDateFilter;
use App\Nova\Filters\UpdatedEndDateFilter;
use App\Nova\Filters\UpdatedStartDateFilter;
use App\Nova\Transaction;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use NovaResourceTest, WithoutEvents;

    /**
     * The Transaction Resource shall contain Transaction Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $transaction = $this->novaResource(Transaction::class);

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
     */
    public function testIdField()
    {
        $transaction = $this->novaResource(Transaction::class);

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
     */
    public function testCreatedAtField()
    {
        $transaction = $this->novaResource(Transaction::class);

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
     */
    public function testUpdatedAtField()
    {
        $transaction = $this->novaResource(Transaction::class);

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
     */
    public function testDeletedAtField()
    {
        $transaction = $this->novaResource(Transaction::class);

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
     */
    public function testDateField()
    {
        $transaction = $this->novaResource(Transaction::class);

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
     */
    public function testServiceField()
    {
        $transaction = $this->novaResource(Transaction::class);

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
     */
    public function testDescriptionField()
    {
        $transaction = $this->novaResource(Transaction::class);

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
     */
    public function testAmountField()
    {
        $transaction = $this->novaResource(Transaction::class);

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
     */
    public function testFilters()
    {
        $resource = $this->novaResource(Transaction::class);

        $resource->assertHasFilter(BillingServiceFilter::class);
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
     */
    public function testActions()
    {
        $resource = $this->novaResource(Transaction::class);

        $resource->assertHasNoActions();
    }
}
