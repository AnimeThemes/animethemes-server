<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Pivot\Wiki;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Pivot\Wiki\ArtistMember\ArtistMemberAliasField;
use App\GraphQL\Schema\Fields\Pivot\Wiki\ArtistMember\ArtistMemberAsField;
use App\GraphQL\Schema\Fields\Pivot\Wiki\ArtistMember\ArtistMemberNotesField;
use App\GraphQL\Schema\Fields\Pivot\Wiki\ArtistMember\ArtistMemberRelevanceField;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Schema\Types\Wiki\ArtistType;
use App\Pivots\Wiki\ArtistMember;

class ArtistMemberType extends PivotType
{
    public function description(): string
    {
        return 'Represents the association of an artist and a group/unit.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new ArtistMemberAliasField(),
            new ArtistMemberAsField(),
            new ArtistMemberNotesField(),
            new ArtistMemberRelevanceField(),
            new CreatedAtField(),
            new UpdatedAtField(),

            new BelongsToRelation(new ArtistType(), ArtistMember::RELATION_ARTIST)
                ->nonNullable(),
            new BelongsToRelation(new ArtistType(), ArtistMember::RELATION_MEMBER)
                ->nonNullable(),
        ];
    }
}
