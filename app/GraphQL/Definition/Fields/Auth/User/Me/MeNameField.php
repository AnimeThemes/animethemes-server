<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\User\Me;

use App\Enums\GraphQL\SortType;
use App\GraphQL\Definition\Fields\StringField;
use App\GraphQL\Support\Directives\Filters\FilterDirective;
use App\Models\Auth\User;

class MeNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_NAME, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The username of authenticated user';
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
