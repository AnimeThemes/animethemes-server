<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Wiki\Artist\ArtistInformationField;
use App\GraphQL\Definition\Fields\Wiki\Artist\ArtistNameField;
use App\GraphQL\Definition\Fields\Wiki\Artist\ArtistSlugField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\HasManyRelation;
use App\GraphQL\Definition\Relations\MorphManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\BaseType;
use App\GraphQL\Definition\Types\Wiki\Song\MembershipType;
use App\GraphQL\Definition\Types\Wiki\Song\PerformanceType;
use App\Models\Wiki\Artist;

/**
 * Class ArtistType.
 */
class ArtistType extends BaseType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a musical performer of anime sequences.\n\nFor example, Chiwa Saitou is the musical performer of the Bakemonogatari OP1 theme, among many others.";
    }

    /**
     * The relations of the type.
     *
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation(new ArtistType(), Artist::RELATION_GROUPS, edgeType: 'ArtistMemberEdge'),
            new BelongsToManyRelation(new ArtistType(), Artist::RELATION_MEMBERS, edgeType: 'ArtistMemberEdge'),
            new BelongsToManyRelation(new ImageType(), Artist::RELATION_IMAGES, edgeType: 'ArtistImageEdge'),
            new BelongsToManyRelation(new ExternalResourceType(), Artist::RELATION_RESOURCES),
            new HasManyRelation(new MembershipType(), Artist::RELATION_MEMBERSHIPS),
            new MorphManyRelation(new PerformanceType(), Artist::RELATION_PERFORMANCES),
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
            new IdField(Artist::ATTRIBUTE_ID),
            new ArtistNameField(),
            new ArtistSlugField(),
            new ArtistInformationField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
