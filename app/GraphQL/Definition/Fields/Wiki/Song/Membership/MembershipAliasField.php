<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Song\Membership;

use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Song\Membership;

class MembershipAliasField extends StringField
{
    public function __construct()
    {
        parent::__construct(Membership::ATTRIBUTE_ALIAS);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The alias the artist is using for this membership';
    }
}
