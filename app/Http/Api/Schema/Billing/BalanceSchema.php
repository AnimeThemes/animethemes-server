<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Billing;

use App\Enums\Models\Billing\BalanceFrequency;
use App\Enums\Models\Billing\Service;
use App\Http\Api\Field\DateField;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\FloatField;
use App\Http\Api\Field\IntField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;

/**
 * Class BalanceSchema.
 */
class BalanceSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @var string|null
     */
    public static ?string $model = Balance::class;

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return BalanceResource::$wrap;
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
                new IntField(BaseResource::ATTRIBUTE_ID, Balance::ATTRIBUTE_ID),
                new FloatField(BalanceResource::ATTRIBUTE_BALANCE, Balance::ATTRIBUTE_BALANCE),
                new DateField(Balance::ATTRIBUTE_DATE),
                new EnumField(Balance::ATTRIBUTE_FREQUENCY, BalanceFrequency::class),
                new EnumField(Balance::ATTRIBUTE_SERVICE, Service::class),
                new FloatField(Balance::ATTRIBUTE_USAGE),
            ],
        );
    }
}
