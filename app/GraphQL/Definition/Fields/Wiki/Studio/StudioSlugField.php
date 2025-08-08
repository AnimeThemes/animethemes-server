<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Studio;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\RouteableField;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Studio;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StudioSlugField extends StringField implements CreatableField, RequiredOnCreation, RouteableField, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Studio::ATTRIBUTE_SLUG, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The URL slug & route key of the resource';
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            'max:192',
            'alpha_dash',
            Rule::unique(Studio::class),
        ];
    }

    /**
     * Set the update validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'max:192',
            'alpha_dash',
            Rule::unique(Studio::class)->ignore(Arr::get($args, 'id')->getKey()),
        ];
    }
}
