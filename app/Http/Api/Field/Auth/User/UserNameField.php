<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Auth\User;

use App\Http\Api\Field\StringField;
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
        parent::__construct(User::ATTRIBUTE_NAME);
    }
}
