<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdUnbindableField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\BelongsToManyRelation;
use App\GraphQL\Schema\Fields\Relations\HasManyRelation;
use App\GraphQL\Schema\Fields\Relations\MorphManyRelation;
use App\GraphQL\Schema\Fields\Relations\MorphToManyRelation;
use App\GraphQL\Schema\Fields\Wiki\Artist\ArtistInformationField;
use App\GraphQL\Schema\Fields\Wiki\Artist\ArtistNameField;
use App\GraphQL\Schema\Fields\Wiki\Artist\ArtistSlugField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ImageableType;
use App\GraphQL\Schema\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Schema\Types\Pivot\Wiki\ArtistMemberType;
use App\GraphQL\Schema\Types\Wiki\Song\MembershipType;
use App\GraphQL\Schema\Types\Wiki\Song\PerformanceType;
use App\Models\Wiki\Artist;

class ArtistType extends EloquentType
{
    public function description(): string
    {
        return "Represents a musical performer of anime sequences.\n\nFor example, Chiwa Saitou is the musical performer of the Bakemonogatari OP1 theme, among many others.";
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdUnbindableField(Artist::ATTRIBUTE_ID),
            new ArtistNameField(),
            new ArtistSlugField(),
            new ArtistInformationField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),

            new BelongsToManyRelation($this, new ArtistType(), Artist::RELATION_GROUPS, new ArtistMemberType()),
            new BelongsToManyRelation($this, new ArtistType(), Artist::RELATION_MEMBERS, new ArtistMemberType()),
            new MorphToManyRelation($this, new ImageType(), Artist::RELATION_IMAGES, new ImageableType()),
            new MorphToManyRelation($this, new ExternalResourceType(), Artist::RELATION_RESOURCES, new ResourceableType()),
            new HasManyRelation(new MembershipType(), Artist::RELATION_GROUPSHIPS),
            new HasManyRelation(new MembershipType(), Artist::RELATION_MEMBERSHIPS),
            new MorphManyRelation(new PerformanceType(), Artist::RELATION_PERFORMANCES),
        ];
    }
}
