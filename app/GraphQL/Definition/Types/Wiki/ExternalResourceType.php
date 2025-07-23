<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\ExternalResource\ExternalResourceExternalIdField;
use App\GraphQL\Definition\Fields\Wiki\ExternalResource\ExternalResourceLinkField;
use App\GraphQL\Definition\Fields\Wiki\ExternalResource\ExternalResourceSiteField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\Edges\Wiki\ExternalResource\ResourceAnimeEdgeType;
use App\GraphQL\Definition\Types\Edges\Wiki\ExternalResource\ResourceArtistEdgeType;
use App\GraphQL\Definition\Types\Edges\Wiki\ExternalResource\ResourceSongEdgeType;
use App\GraphQL\Definition\Types\Edges\Wiki\ExternalResource\ResourceStudioEdgeType;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Wiki\ExternalResource;

/**
 * Class ExternalResourceType.
 */
class ExternalResourceType extends EloquentType implements HasFields, HasRelations
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a site with supplementary information for another resource such as an anime or artist.\n\nFor example, the Bakemonogatari anime has MyAnimeList, AniList and AniDB resources.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation(new ResourceAnimeEdgeType(), ExternalResource::RELATION_ANIME),
            new BelongsToManyRelation(new ResourceArtistEdgeType(), ExternalResource::RELATION_ARTISTS),
            new BelongsToManyRelation(new ResourceSongEdgeType(), ExternalResource::RELATION_SONGS),
            new BelongsToManyRelation(new ResourceStudioEdgeType(), ExternalResource::RELATION_STUDIOS),
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
            new IdField(ExternalResource::ATTRIBUTE_ID),
            new ExternalResourceExternalIdField(),
            new ExternalResourceLinkField(),
            new ExternalResourceSiteField(),
            new LocalizedEnumField(new ExternalResourceSiteField()),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
