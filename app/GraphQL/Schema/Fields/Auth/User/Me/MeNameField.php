<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Auth\User\Me;

use App\Enums\GraphQL\SortType;
use App\GraphQL\Schema\Fields\StringField;
use App\GraphQL\Support\Filter\Filter;
use App\Models\Auth\User;

class MeNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_NAME, nullable: false);
    }

    public function description(): string
    {
        return 'The username of authenticated user';
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
