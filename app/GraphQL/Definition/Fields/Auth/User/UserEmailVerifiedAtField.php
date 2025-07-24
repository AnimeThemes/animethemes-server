<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\User;

use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\Models\Auth\User;

class UserEmailVerifiedAtField extends DateTimeTzField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_EMAIL_VERIFIED_AT, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The date the user verified their email';
    }
}
