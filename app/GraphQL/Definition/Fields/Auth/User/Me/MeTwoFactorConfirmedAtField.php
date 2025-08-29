<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\User\Me;

use App\Enums\GraphQL\SortType;
use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\GraphQL\Support\Filter\Filter;
use App\Models\Auth\User;

class MeTwoFactorConfirmedAtField extends DateTimeTzField
{
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_TWO_FACTOR_CONFIRMED_AT, nullable: false);
    }

    public function description(): string
    {
        return 'The date the user confirmed their two-factor authentication';
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return [];
    }

    public function sortType(): SortType
    {
        return SortType::NONE;
    }
}
