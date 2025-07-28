<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\HasFields;
use App\Contracts\GraphQL\HasRelations;
use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\LocalizedEnumField;
use App\GraphQL\Definition\Fields\Wiki\ExternalResource\ExternalResourceExternalIdField;
use App\GraphQL\Definition\Fields\Wiki\ExternalResource\ExternalResourceLinkField;
use App\GraphQL\Definition\Fields\Wiki\ExternalResource\ExternalResourceSiteField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Pivot\Wiki\AnimeResourceType;
use App\GraphQL\Definition\Types\Pivot\Wiki\ArtistResourceType;
use App\GraphQL\Definition\Types\Pivot\Wiki\SongResourceType;
use App\GraphQL\Definition\Types\Pivot\Wiki\StudioResourceType;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\ExternalResource;

class ExternalResourceType extends EloquentType implements HasFields, HasRelations, ReportableType
{
    /**
     * The description of the type.
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
            new BelongsToManyRelation($this, AnimeType::class, ExternalResource::RELATION_ANIME, AnimeResourceType::class),
            new BelongsToManyRelation($this, ArtistType::class, ExternalResource::RELATION_ARTISTS, ArtistResourceType::class),
            new BelongsToManyRelation($this, SongType::class, ExternalResource::RELATION_SONGS, SongResourceType::class),
            new BelongsToManyRelation($this, StudioType::class, ExternalResource::RELATION_STUDIOS, StudioResourceType::class),
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
            new IdField(ExternalResource::ATTRIBUTE_ID, ExternalResource::class),
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
