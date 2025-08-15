<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Pivot\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistMember\ArtistMemberAliasField;
use App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistMember\ArtistMemberAsField;
use App\GraphQL\Definition\Fields\Pivot\Wiki\ArtistMember\ArtistMemberNotesField;
use App\GraphQL\Definition\Types\Pivot\PivotType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Pivots\Wiki\ArtistMember;

class ArtistMemberType extends PivotType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function description(): string
    {
        return 'Represents the association of an artist and a group/unit.';
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new ArtistType(), ArtistMember::RELATION_ARTIST)
                ->notNullable(),
            new BelongsToRelation(new ArtistType(), ArtistMember::RELATION_MEMBER)
                ->notNullable(),
        ];
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
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
