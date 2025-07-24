<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\User;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Auth\User;

class UserNameField extends StringField
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
        return 'The username of the resource';
    }
}
