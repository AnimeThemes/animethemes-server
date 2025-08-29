<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Song\Membership;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Song\Membership;

class MembershipAliasField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Membership::ATTRIBUTE_ALIAS);
    }

    public function description(): string
    {
        return 'The alias the artist is using for this membership';
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }
}
