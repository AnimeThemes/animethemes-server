<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Fields\Wiki\ExternalResource\ExternalResourceExternalIdField;
use App\GraphQL\Schema\Fields\Wiki\ExternalResource\ExternalResourceLinkField;
use App\GraphQL\Schema\Fields\Wiki\ExternalResource\ExternalResourceSiteField;
use App\GraphQL\Schema\Relations\MorphToManyRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ResourceableType;
use App\Models\Wiki\ExternalResource;

class ExternalResourceType extends EloquentType implements ReportableType
{
    public function description(): string
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
            new MorphToManyRelation($this, AnimeType::class, ExternalResource::RELATION_ANIME, ResourceableType::class),
            new MorphToManyRelation($this, ArtistType::class, ExternalResource::RELATION_ARTISTS, ResourceableType::class),
            new MorphToManyRelation($this, SongType::class, ExternalResource::RELATION_SONGS, ResourceableType::class),
            new MorphToManyRelation($this, StudioType::class, ExternalResource::RELATION_STUDIOS, ResourceableType::class),
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
