<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\User\Me;

use App\Enums\GraphQL\SortType;
use App\GraphQL\Definition\Fields\StringField;
use App\GraphQL\Support\Filter\Filter;
use App\Models\Auth\User;

class MeEmailField extends StringField
{
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_EMAIL, nullable: false);
    }

    public function description(): string
    {
        return 'The email of the user';
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
