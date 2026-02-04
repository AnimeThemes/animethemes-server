<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki\Song;

use App\Contracts\GraphQL\Types\SubmitableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Fields\Relations\MorphManyRelation;
use App\GraphQL\Schema\Fields\Wiki\Song\Membership\MembershipAliasField;
use App\GraphQL\Schema\Fields\Wiki\Song\Membership\MembershipAsField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Wiki\ArtistType;
use App\Models\Wiki\Song\Membership;

class MembershipType extends EloquentType implements SubmitableType
{
    public function description(): string
    {
        return "Represents the link between an artist and a group related to the song credits.\n\nFor example, Sayuri Date is a member of Liella and has performed using the membership.";
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

            new BelongsToRelation(new ArtistType(), Membership::RELATION_GROUP)
                ->nonNullable(),
            new BelongsToRelation(new ArtistType(), Membership::RELATION_MEMBER)
                ->nonNullable(),
            new MorphManyRelation(new PerformanceType(), Membership::RELATION_PERFORMANCES),
        ];
    }
}
