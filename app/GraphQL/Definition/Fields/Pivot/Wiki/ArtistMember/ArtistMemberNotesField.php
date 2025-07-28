<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistMember;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Attributes\Resolvers\UseFieldDirective;
use App\GraphQL\Definition\Fields\StringField;
use App\GraphQL\Resolvers\PivotResolver;
use App\Pivots\Wiki\ArtistMember;

#[UseFieldDirective(PivotResolver::class)]
class ArtistMemberNotesField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(ArtistMember::ATTRIBUTE_NOTES);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'Used to extra annotation, like member role';
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
