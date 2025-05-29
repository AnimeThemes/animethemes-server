<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistMember\ArtistMemberAliasField;
use App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistMember\ArtistMemberAsField;
use App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistMember\ArtistMemberNotesField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\Pivots\Wiki\ArtistMember;

/**
 * Class ArtistMemberType.
 */
class ArtistMemberType extends PivotType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the association of an artist and a group/unit.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new ArtistType(), ArtistMember::RELATION_ARTIST, nullable: false),
            new BelongsToRelation(new ArtistType(), ArtistMember::RELATION_MEMBER, nullable: false),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            new ArtistMemberAliasField(),
            new ArtistMemberAsField(),
            new ArtistMemberNotesField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
