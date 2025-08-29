<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistMember;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Pivots\Wiki\ArtistMember;

class ArtistMemberNotesField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(ArtistMember::ATTRIBUTE_NOTES);
    }

    public function description(): string
    {
        return 'Used to extra annotation, like member role';
    }

    /**
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
