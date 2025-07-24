<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Auth\User;

use App\Http\Api\Field\DateField;
use App\Http\Api\Schema\Schema;
use App\Models\Auth\User;

class UserEmailVerifiedAtField extends DateField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, User::ATTRIBUTE_EMAIL_VERIFIED_AT);
    }
}
