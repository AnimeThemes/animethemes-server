<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Auth\User\Me;

use App\Enums\GraphQL\SortType;
use App\GraphQL\Schema\Fields\StringField;
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

    public function sortType(): SortType
    {
        return SortType::NONE;
    }
}
