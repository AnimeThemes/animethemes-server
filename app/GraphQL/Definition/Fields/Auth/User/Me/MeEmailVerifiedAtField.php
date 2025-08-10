<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\User\Me;

use App\Enums\GraphQL\SortType;
use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\GraphQL\Support\Filter\Filter;
use App\Models\Auth\User;

class MeEmailVerifiedAtField extends DateTimeTzField
{
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_EMAIL_VERIFIED_AT, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The date the user verified their email';
    }

    /**
     * The filters of the field.
     *
     * @return Filter[]
     */
    public function getFilters(): array
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
