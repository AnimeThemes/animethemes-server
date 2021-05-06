<?php

namespace App\Models;

use App\Enums\InvoiceVendor;
use App\Events\Invoice\InvoiceCreated;
use App\Events\Invoice\InvoiceDeleted;
use App\Events\Invoice\InvoiceRestored;
use App\Events\Invoice\InvoiceUpdated;
use BenSampo\Enum\Traits\CastsEnums;

class Invoice extends BaseModel
{
    use CastsEnums;

    /**
     * @var array
     */
    protected $fillable = ['vendor', 'description', 'amount', 'external_id'];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => InvoiceCreated::class,
        'deleted' => InvoiceDeleted::class,
        'restored' => InvoiceRestored::class,
        'updated' => InvoiceUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'invoice_id';

    /**
     * @var array
     */
    protected $enumCasts = [
        'vendor' => InvoiceVendor::class,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'vendor' => 'int',
    ];

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->description;
    }
}
