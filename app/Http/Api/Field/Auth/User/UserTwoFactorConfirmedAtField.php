<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Auth\User;

use App\Http\Api\Field\DateField;
use App\Http\Api\Schema\Schema;
use App\Models\Auth\User;

class UserTwoFactorConfirmedAtField extends DateField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, User::ATTRIBUTE_TWO_FACTOR_CONFIRMED_AT);
    }
}
