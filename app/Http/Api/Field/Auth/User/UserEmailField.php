<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Auth\User;

use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Models\Auth\User;

/**
 * Class UserEmailField.
 */
class UserEmailField extends StringField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, User::ATTRIBUTE_EMAIL);
    }
}
