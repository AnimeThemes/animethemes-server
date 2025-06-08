<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\ExternalResource\ExternalResourceExternalIdField;
use App\GraphQL\Definition\Fields\Wiki\ExternalResource\ExternalResourceLinkField;
use App\GraphQL\Definition\Fields\Wiki\ExternalResource\ExternalResourceSiteField;
use App\GraphQL\Definition\Relations\BelongsToManyRelation;
use App\GraphQL\Definition\Relations\Relation;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Wiki\ExternalResource;

/**
 * Class ExternalResourceType.
 */
class ExternalResourceType extends EloquentType
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
     * @return array<int, Relation>
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation(new AnimeType(), ExternalResource::RELATION_ANIME, edgeType: 'ExternalResourceAnimeEdge'),
            new BelongsToManyRelation(new ArtistType(), ExternalResource::RELATION_ARTISTS, edgeType: 'ExternalResourceArtistEdge'),
            new BelongsToManyRelation(new SongType(), ExternalResource::RELATION_SONGS, edgeType: 'ExternalResourceSongEdge'),
            new BelongsToManyRelation(new StudioType(), ExternalResource::RELATION_STUDIOS, edgeType: 'ExternalResourceStudioEdge'),
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
