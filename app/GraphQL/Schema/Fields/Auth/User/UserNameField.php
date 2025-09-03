<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Auth\User;

use App\GraphQL\Schema\Fields\StringField;
use App\Models\Auth\User;

class UserNameField extends StringField
{
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_NAME, nullable: false);
    }

    public function description(): string
    {
        return 'The username of the resource';
    }
}
