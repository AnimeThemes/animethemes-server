<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Billing;

use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Billing\Balance\BalanceDateField;
use App\Http\Api\Field\Billing\Balance\BalanceFrequencyField;
use App\Http\Api\Field\Billing\Balance\BalanceMonthToDateField;
use App\Http\Api\Field\Billing\Balance\BalanceServiceField;
use App\Http\Api\Field\Billing\Balance\BalanceUsageField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Billing\Resource\BalanceResource;
use App\Models\Billing\Balance;

/**
 * Class BalanceSchema.
 */
class BalanceSchema extends EloquentSchema
{
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
                new IdField($this, Balance::ATTRIBUTE_ID),
                new BalanceMonthToDateField($this),
                new BalanceDateField($this),
                new BalanceFrequencyField($this),
                new BalanceServiceField($this),
                new BalanceUsageField($this),
            ],
        );
    }
}
