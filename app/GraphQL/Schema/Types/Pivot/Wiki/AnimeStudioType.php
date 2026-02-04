<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Pivot\Wiki;

use App\Contracts\GraphQL\Types\SubmitableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Relations\BelongsToRelation;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use App\GraphQL\Schema\Types\Wiki\AnimeType;
use App\GraphQL\Schema\Types\Wiki\StudioType;
use App\Pivots\Wiki\AnimeStudio;

class AnimeStudioType extends PivotType implements SubmitableType
{
    public function description(): string
    {
        return 'Represents the association between an anime and a studio.';
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fieldClasses(): array
    {
        return [
            new CreatedAtField(),
            new UpdatedAtField(),

            new BelongsToRelation(new AnimeType(), AnimeStudio::RELATION_ANIME)
                ->nonNullable(),
            new BelongsToRelation(new StudioType(), AnimeStudio::RELATION_STUDIO)
                ->nonNullable(),
        ];
    }
}
