<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Wiki;

use App\Contracts\GraphQL\Types\ReportableType;
use App\GraphQL\Schema\Fields\Base\CreatedAtField;
use App\GraphQL\Schema\Fields\Base\DeletedAtField;
use App\GraphQL\Schema\Fields\Base\IdField;
use App\GraphQL\Schema\Fields\Base\UpdatedAtField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Fields\Wiki\ThemeGroup\ThemeGroupNameField;
use App\GraphQL\Schema\Fields\Wiki\ThemeGroup\ThemeGroupSlugField;
use App\GraphQL\Schema\Relations\HasManyRelation;
use App\GraphQL\Schema\Relations\Relation;
use App\GraphQL\Schema\Types\EloquentType;
use App\GraphQL\Schema\Types\Wiki\Anime\AnimeThemeType;
use App\Models\Wiki\Group;

class ThemeGroupType extends EloquentType implements ReportableType
{
    public function description(): string
    {
        return "Represents the group that accompanies a Theme.\n\nFor example, English Version is the group for english dubbed Theme.";
    }

    /**
     * The relations of the type.
     *
     * @return Relation[]
     */
    public function relations(): array
    {
        return [
            new HasManyRelation(new AnimeThemeType(), Group::RELATION_THEMES),
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
            new IdField(Group::ATTRIBUTE_ID, Group::class),
            new ThemeGroupNameField(),
            new ThemeGroupSlugField(),
            new CreatedAtField(),
            new UpdatedAtField(),
            new DeletedAtField(),
        ];
    }

    /**
     * Get the model string representation for the type.
     *
     * @return class-string<Group>
     */
    public function model(): string
    {
        return Group::class;
    }
}
