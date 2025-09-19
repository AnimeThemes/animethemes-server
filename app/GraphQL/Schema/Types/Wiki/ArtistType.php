<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdUnbindableField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Wiki\Artist\ArtistInformationField;
use App\GraphQL\Schema\Fields\Wiki\Artist\ArtistNameField;
use App\GraphQL\Schema\Fields\Wiki\Artist\ArtistSlugField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ImageableType;
use App\GraphQL\Schema\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Schema\Types\Pivot\Wiki\ArtistMemberType;
use App\GraphQL\Schema\Types\Wiki\Song\MembershipType;
use App\GraphQL\Schema\Types\Wiki\Song\PerformanceType;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\MorphManyRelation;
use App\GraphQL\Support\Relations\MorphToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Artist;

class ArtistType extends EloquentType implements ReportableType
{
    public function description(): string
    {
        return "Represents a musical performer of anime sequences.\n\nFor example, Chiwa Saitou is the musical performer of the Bakemonogatari OP1 theme, among many others.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation($this, ArtistType::class, Artist::RELATION_GROUPS, ArtistMemberType::class),
            new BelongsToManyRelation($this, ArtistType::class, Artist::RELATION_MEMBERS, ArtistMemberType::class),
            new MorphToManyRelation($this, ImageType::class, Artist::RELATION_IMAGES, ImageableType::class),
            new MorphToManyRelation($this, ExternalResourceType::class, Artist::RELATION_RESOURCES, ResourceableType::class),
            new HasManyRelation(new MembershipType(), Artist::RELATION_GROUPSHIPS),
            new HasManyRelation(new MembershipType(), Artist::RELATION_MEMBERSHIPS),
            new MorphManyRelation(new PerformanceType(), Artist::RELATION_PERFORMANCES),
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
            new IdUnbindableField(Artist::ATTRIBUTE_ID),
            new ArtistNameField(),
            new ArtistSlugField(),
            new ArtistInformationField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
