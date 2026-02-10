<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\Contracts\GraphQL\Types\SubmitableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\LocalizedEnumField;
use App\GraphQL\Schema\Fields\Relations\MorphToManyRelation;
use App\GraphQL\Schema\Fields\Wiki\ExternalResource\ExternalResourceExternalIdField;
use App\GraphQL\Schema\Fields\Wiki\ExternalResource\ExternalResourceLinkField;
use App\GraphQL\Schema\Fields\Wiki\ExternalResource\ExternalResourceSiteField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ResourceableType;
use App\Models\Wiki\ExternalResource;

class ExternalResourceType extends EloquentType implements SubmitableType
{
    public function description(): string
    {
        return "Represents a site with supplementary information for another resource such as an anime or artist.\n\nFor example, the Bakemonogatari anime has MyAnimeList, AniList and AniDB resources.";
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

            new MorphToManyRelation($this, new AnimeType(), ExternalResource::RELATION_ANIME, new ResourceableType()),
            new MorphToManyRelation($this, new ArtistType(), ExternalResource::RELATION_ARTISTS, new ResourceableType()),
            new MorphToManyRelation($this, new SongType(), ExternalResource::RELATION_SONGS, new ResourceableType()),
            new MorphToManyRelation($this, new StudioType(), ExternalResource::RELATION_STUDIOS, new ResourceableType()),
        ];
    }
}
