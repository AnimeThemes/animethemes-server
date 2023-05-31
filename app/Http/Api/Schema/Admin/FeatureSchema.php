<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Admin;

use App\Http\Api\Field\Admin\Feature\FeatureNameField;
use App\Http\Api\Field\Admin\Feature\FeatureValueField;
use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\IdField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Resources\Admin\Resource\FeatureResource;
use App\Models\Admin\Feature;

/**
 * Class FeatureSchema.
 */
class FeatureSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return FeatureResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new IdField($this, Feature::ATTRIBUTE_ID),
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new FeatureNameField($this),
            new FeatureValueField($this),
        ];
    }
}
