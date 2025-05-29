<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Admin;

use App\GraphQL\Definition\Fields\Admin\Feature\FeatureNameField;
use App\GraphQL\Definition\Fields\Admin\Feature\FeatureValueField;
use App\GraphQL\Definition\Fields\Base\CreatedAtField;
use App\GraphQL\Definition\Fields\Base\IdField;
use App\GraphQL\Definition\Fields\Base\UpdatedAtField;
use App\GraphQL\Definition\Types\EloquentType;
use App\Models\Admin\Feature;

/**
 * Class FeatureType.
 */
class FeatureType extends EloquentType
{
    /**
     * The description of the type.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return "Represents a feature flag that enable/disable site functionalities.\n\nFor example, the 'allow_discord_notifications' feature enables/disables discord notifications for the configured bot.";
    }

    /**
     * The fields of the type.
     *
     * @return array
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
