<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdUnbindableField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Wiki\Studio\StudioNameField;
use App\GraphQL\Schema\Fields\Wiki\Studio\StudioSlugField;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Pivot\Morph\ImageableType;
use App\GraphQL\Schema\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Schema\Types\Pivot\Wiki\AnimeStudioType;
use App\GraphQL\Schema\Relations\BelongsToManyRelation;
use App\GraphQL\Schema\Relations\MorphToManyRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\Models\Wiki\Studio;

class StudioType extends EloquentType implements ReportableType
{
    public function description(): string
    {
        return "Represents a company that produces anime.\n\nFor example, Shaft is the studio that produced the anime Bakemonogatari.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new BelongsToManyRelation($this, AnimeType::class, Studio::RELATION_ANIME, AnimeStudioType::class),
            new MorphToManyRelation($this, ImageType::class, Studio::RELATION_IMAGES, ImageableType::class),
            new MorphToManyRelation($this, ExternalResourceType::class, Studio::RELATION_RESOURCES, ResourceableType::class),
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
            new IdUnbindableField(Studio::ATTRIBUTE_ID),
            new StudioNameField(),
            new StudioSlugField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
