<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\User;

use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\Models\Auth\User;

class UserTwoFactorConfirmedAtField extends DateTimeTzField
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
}
