<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Billing;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Billing\Transaction\TransactionAmountField;
use App\Http\Api\Field\Billing\Transaction\TransactionDateField;
use App\Http\Api\Field\Billing\Transaction\TransactionDescriptionField;
use App\Http\Api\Field\Billing\Transaction\TransactionExternalIdField;
use App\Http\Api\Field\Billing\Transaction\TransactionServiceField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;

/**
 * Class TransactionSchema.
 */
class TransactionSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return TransactionResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IdField($this, Transaction::ATTRIBUTE_ID),
                new TransactionDateField($this),
                new TransactionServiceField($this),
                new TransactionDescriptionField($this),
                new TransactionAmountField($this),
                new TransactionExternalIdField($this),
            ],
        );
    }
}
