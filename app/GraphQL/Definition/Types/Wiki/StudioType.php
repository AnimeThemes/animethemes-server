<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Wiki;

use App\Contracts\GraphQL\HasRelations;
use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Fields\Wiki\Studio\StudioNameField;
use App\GraphQL\Definition\Fields\Wiki\Studio\StudioSlugField;
use App\GraphQL\Definition\Types\EloquentType;
use App\GraphQL\Definition\Types\Pivot\Wiki\AnimeStudioType;
use App\GraphQL\Definition\Types\Pivot\Wiki\StudioImageType;
use App\GraphQL\Definition\Types\Pivot\Wiki\StudioResourceType;
use App\GraphQL\Support\Relations\BelongsToManyRelation;
use App\GraphQL\Support\Relations\Relation;
use App\Models\Wiki\Studio;

class StudioType extends EloquentType implements HasRelations, ReportableType
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
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
            new BelongsToManyRelation($this, ImageType::class, Studio::RELATION_IMAGES, StudioImageType::class),
            new BelongsToManyRelation($this, ExternalResourceType::class, Studio::RELATION_RESOURCES, StudioResourceType::class),
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
            new IdField(Studio::ATTRIBUTE_ID, Studio::class),
            new StudioNameField(),
            new StudioSlugField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }
}
