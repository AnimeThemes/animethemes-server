<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Auth\User\Me;

use App\Enums\GraphQL\SortType;
use App\GraphQL\Schema\Fields\DateTimeTzField;
use App\Models\Auth\User;

class MeEmailVerifiedAtField extends DateTimeTzField
{
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_EMAIL_VERIFIED_AT, nullable: false);
    }

    public function description(): string
    {
        return 'The date the user verified their email';
    }

    public function sortType(): SortType
    {
        return SortType::NONE;
    }
}
