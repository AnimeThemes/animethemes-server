<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Admin\Feature;

use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\StringField;
use App\Models\Admin\Feature;

class FeatureValueField extends StringField implements UpdatableField
{
    public function __construct()
    {
        parent::__construct(Feature::ATTRIBUTE_VALUE, nullable: false);
    }

    public function description(): string
    {
        return 'The value of the resource';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'string',
            'max:192',
        ];
    }
}
