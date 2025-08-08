<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdUnbindableField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Artist\ArtistInformationField;
use App\GraphQL\Definition\Fields\Wiki\Artist\ArtistNameField;
use App\GraphQL\Definition\Fields\Wiki\Artist\ArtistSlugField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Pivot\Wiki\ArtistMemberType;
use App\GraphQL\Definition\Types\Pivot\Wiki\ArtistResourceType;
use App\GraphQL\Definition\Types\Wiki\Song\MembershipType;
use App\GraphQL\Definition\Types\Wiki\Song\PerformanceType;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\HasManyRelation;
use App\GraphQL\Support\Relations\MorphManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Artist;

class ArtistType extends EloquentType implements ReportableType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
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
            new BelongsToManyRelation($this, ImageType::class, Artist::RELATION_IMAGES, ArtistMemberType::class),
            new BelongsToManyRelation($this, ExternalResourceType::class, Artist::RELATION_RESOURCES, ArtistResourceType::class),
            new HasManyRelation(new MembershipType(), Artist::RELATION_MEMBERSHIPS),
            new MorphManyRelation(new PerformanceType(), Artist::RELATION_PERFORMANCES),
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
