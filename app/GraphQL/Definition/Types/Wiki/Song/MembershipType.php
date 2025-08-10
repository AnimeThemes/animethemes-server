<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki\Song;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Song\Membership\MembershipAliasField;
use App\GraphQL\Definition\Fields\Wiki\Song\Membership\MembershipAsField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Wiki\ArtistType;
use App\GraphQL\Support\Relations\BelongsToRelation;
use App\GraphQL\Support\Relations\MorphManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Song\Membership;

class MembershipType extends EloquentType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function description(): string
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
            new BelongsToRelation(new ArtistType(), Membership::RELATION_GROUP)
                ->notNullable(),
            new BelongsToRelation(new ArtistType(), Membership::RELATION_MEMBER)
                ->notNullable(),
            new MorphManyRelation(new PerformanceType(), Membership::RELATION_PERFORMANCES),
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
            new IdField(Membership::ATTRIBUTE_ID, Membership::class),
            new MembershipAliasField(),
            new MembershipAsField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
