<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdUnbindableField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Studio\StudioNameField;
use App\GraphQL\Definition\Fields\Wiki\Studio\StudioSlugField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Pivot\Morph\ImageableType;
use App\GraphQL\Definition\Types\Pivot\Morph\ResourceableType;
use App\GraphQL\Definition\Types\Pivot\Wiki\AnimeStudioType;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\MorphToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Studio;

class StudioType extends EloquentType implements ReportableType
{
    /**
     * The description of the type.
     */
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
