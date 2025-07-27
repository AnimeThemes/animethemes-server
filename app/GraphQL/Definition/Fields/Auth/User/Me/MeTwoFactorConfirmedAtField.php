<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\User\Me;

use App\Enums\GraphQL\SortType;
use App\GraphQL\Definition\Directives\Filters\FilterDirective;
use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\Models\Auth\User;

class MeTwoFactorConfirmedAtField extends DateTimeTzField
{
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_TWO_FACTOR_CONFIRMED_AT, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The date the user confirmed their two-factor authentication';
    }

    /**
     * The directives available for this filter.
     *
     * @return FilterDirective[]
     */
    public function filterDirectives(): array
    {
        return [];
    }

    /**
     * The sort type of the field.
     */
    public function sortType(): SortType
    {
        return SortType::NONE;
    }
}
