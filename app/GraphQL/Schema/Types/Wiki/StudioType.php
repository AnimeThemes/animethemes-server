<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdUnbindableField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\BelongsToManyRelation;
use App\GraphQL\Schema\Fields\Relations\MorphToManyRelation;
use App\GraphQL\Schema\Fields\Wiki\Studio\StudioNameField;
use App\GraphQL\Schema\Fields\Wiki\Studio\StudioSlugField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ImageableType;
use App\GraphQL\Schema\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Schema\Types\Pivot\Wiki\AnimeStudioType;
use App\Models\Wiki\Studio;

class StudioType extends EloquentType
{
    public function description(): string
    {
        return "Represents a company that produces anime.\n\nFor example, Shaft is the studio that produced the anime Bakemonogatari.";
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new IdUnbindableField(Studio::ATTRIBUTE_ID),
            new StudioNameField(),
            new StudioSlugField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),

            new BelongsToManyRelation($this, new AnimeType(), Studio::RELATION_ANIME, new AnimeStudioType()),
            new MorphToManyRelation($this, new ImageType(), Studio::RELATION_IMAGES, new ImageableType()),
            new MorphToManyRelation($this, new ExternalResourceType(), Studio::RELATION_RESOURCES, new ResourceableType()),
        ];
    }
}
