<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Auth\User;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Auth\User;

/**
 * Class UserNameField.
 */
class UserNameField extends StringField
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(User::ATTRIBUTE_NAME, nullable: false);
    }

    /**
     * The description of the field.
     *
     * @return string
     */
    public function description(): string
    {
        return 'The username of the resource';
    }
}
