<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Admin;

use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Admin\Feature\FeatureNameField;
use App\GraphQL\Definition\Fields\Admin\Feature\FeatureValueField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Fields\Field;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Admin\Feature;

class FeatureType extends EloquentType implements HasFields
{
    /**
     * The description of the type.
     */
    public function getDescription(): string
    {
        return "Represents a feature flag that enable/disable site functionalities.\n\nFor example, the 'allow_discord_notifications' feature enables/disables discord notifications for the configured bot.";
    }

    /**
     * The fields of the type.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            new IdField(Feature::ATTRIBUTE_ID),
            new FeatureNameField(),
            new FeatureValueField(),
            new CreatedAtField(),
            new UpdatedAtField(),
        ];
    }
}
