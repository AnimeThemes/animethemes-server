<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Billing;

use App\Enums\Models\Billing\Service;
use App\Http\Api\Field\DateField;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\FloatField;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Billing\Resource\TransactionResource;
use App\Models\Billing\Transaction;

/**
 * Class TransactionSchema.
 */
class TransactionSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @var string|null
     */
    public static ?string $model = Transaction::class;

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
                new IntField(BaseResource::ATTRIBUTE_ID, Transaction::ATTRIBUTE_ID),
                new DateField(Transaction::ATTRIBUTE_DATE),
                new EnumField(Transaction::ATTRIBUTE_SERVICE, Service::class),
                new StringField(Transaction::ATTRIBUTE_DESCRIPTION),
                new FloatField(Transaction::ATTRIBUTE_AMOUNT),
                new StringField(Transaction::ATTRIBUTE_EXTERNAL_ID),
            ],
        );
    }
}
