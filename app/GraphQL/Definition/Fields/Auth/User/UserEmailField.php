<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\User;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Auth\User;

class UserEmailField extends StringField
{
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_EMAIL, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The email of the user';
    }
}
