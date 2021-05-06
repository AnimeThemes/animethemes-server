<?php

namespace Tests\Unit\Nova\Resources;

use App\Enums\InvoiceVendor;
use App\Nova\Filters\InvoiceVendorFilter;
use App\Nova\Filters\RecentlyCreatedFilter;
use App\Nova\Filters\RecentlyUpdatedFilter;
use App\Nova\Invoice;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Testing\WithoutEvents;
use JoshGaber\NovaUnit\Resources\NovaResourceTest;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use NovaResourceTest, WithoutEvents;

    /**
     * The Invoice Resource shall contain Invoice Fields.
     *
     * @return void
     */
    public function testFields()
    {
        $invoice = $this->novaResource(Invoice::class);

        $invoice->assertHasField(__('nova.id'));
        $invoice->assertHasField(__('nova.created_at'));
        $invoice->assertHasField(__('nova.updated_at'));
        $invoice->assertHasField(__('nova.deleted_at'));
        $invoice->assertHasField(__('nova.vendor'));
        $invoice->assertHasField(__('nova.description'));
        $invoice->assertHasField(__('nova.amount'));
        $invoice->assertHasField(__('nova.external_id'));
    }

    /**
     * The Invoice Resource shall contain an ID field.
     *
     * @return void
     */
    public function testIdField()
    {
        $invoice = $this->novaResource(Invoice::class);

        $field = $invoice->field(__('nova.id'));

        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertHiddenWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The External Resource shall contain a Created At field.
     *
     * @return void
     */
    public function testCreatedAtField()
    {
        $invoice = $this->novaResource(Invoice::class);

        $field = $invoice->field(__('nova.created_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The External Resource shall contain an Updated At field.
     *
     * @return void
     */
    public function testUpdatedAtField()
    {
        $invoice = $this->novaResource(Invoice::class);

        $field = $invoice->field(__('nova.updated_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The External Resource shall contain a Deleted At field.
     *
     * @return void
     */
    public function testDeletedAtField()
    {
        $invoice = $this->novaResource(Invoice::class);

        $field = $invoice->field(__('nova.deleted_at'));

        $field->assertHiddenFromIndex();
        $field->assertShownOnDetail();
        $field->assertHiddenWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertNotSortable();
    }

    /**
     * The Invoice Resource shall contain a Vendor field.
     *
     * @return void
     */
    public function testVendorField()
    {
        $invoice = $this->novaResource(Invoice::class);

        $field = $invoice->field(__('nova.vendor'));

        $field->assertHasRule('required');
        $field->assertHasRule((new EnumValue(InvoiceVendor::class, false))->__toString());
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Invoice Resource shall contain a Description field.
     *
     * @return void
     */
    public function testDescriptionField()
    {
        $invoice = $this->novaResource(Invoice::class);

        $field = $invoice->field(__('nova.description'));

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
     * The Invoice Resource shall contain an Amount field.
     *
     * @return void
     */
    public function testAmountField()
    {
        $invoice = $this->novaResource(Invoice::class);

        $field = $invoice->field(__('nova.amount'));

        $field->assertHasRule('required');
        $field->assertShownOnIndex();
        $field->assertShownOnDetail();
        $field->assertShownWhenCreating();
        $field->assertShownWhenUpdating();
        $field->assertNotNullable();
        $field->assertSortable();
    }

    /**
     * The Invoice Resource shall contain Invoice Filters.
     *
     * @return void
     */
    public function testFilters()
    {
        $resource = $this->novaResource(Invoice::class);

        $resource->assertHasFilter(InvoiceVendorFilter::class);
        $resource->assertHasFilter(RecentlyCreatedFilter::class);
        $resource->assertHasFilter(RecentlyUpdatedFilter::class);
    }

    /**
     * The Invoice Resource shall contain no Actions.
     *
     * @return void
     */
    public function testActions()
    {
        $resource = $this->novaResource(Invoice::class);

        $resource->assertHasNoActions();
    }
}
