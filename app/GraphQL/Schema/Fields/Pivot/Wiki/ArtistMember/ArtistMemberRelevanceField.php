<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Pivot\Wiki\ArtistMember;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\IntField;
use App\Pivots\Wiki\ArtistMember;

class ArtistMemberRelevanceField extends IntField implements CreatableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(ArtistMember::ATTRIBUTE_RELEVANCE, nullable: false);
    }

    public function description(): string
    {
        return 'Used to determine the relevance order of members in group';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'nullable',
            'integer',
            'min:1',
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'nullable',
            'integer',
            'min:1',
        ];
    }
}
