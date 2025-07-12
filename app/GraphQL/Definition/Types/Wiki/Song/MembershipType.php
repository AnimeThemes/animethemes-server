<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Song;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Song\Membership\MembershipAliasField;
use App\GraphQL\Definition\Fields\Wiki\Song\Membership\MembershipAsField;
use App\GraphQL\Definition\Relations\BelongsToRelation;
use App\GraphQL\Definition\Relations\MorphManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\Models\Wiki\Song\Membership;

/**
 * Class MembershipType.
 */
class MembershipType extends EloquentType implements HasFields, HasRelations
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents the link between an artist and a group related to the song credits.\n\nFor example, Sayuri Date is a member of Liella and has performed using the membership.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToRelation(new ArtistType(), Membership::RELATION_ARTIST, 'group', false),
            new BelongsToRelation(new ArtistType(), Membership::RELATION_MEMBER, nullable: false),
            new MorphManyRelation(new PerformanceType(), Membership::RELATION_PERFORMANCES),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new IdField(Membership::ATTRIBUTE_ID),
            new MembershipAliasField(),
            new MembershipAsField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
