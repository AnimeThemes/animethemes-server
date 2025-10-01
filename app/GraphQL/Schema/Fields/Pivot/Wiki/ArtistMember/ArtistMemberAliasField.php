<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Pivot\Wiki\ArtistMember;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\StringField;
use App\Pivots\Wiki\ArtistMember;

class ArtistMemberAliasField extends StringField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(ArtistMember::ATTRIBUTE_ALIAS);
    }

    public function description(): string
    {
        return 'Used to distinguish member by alias';
    }

    /**
     * @param  array<string, mixed>  $args
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
