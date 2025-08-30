<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\ThemeGroup;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Group;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ThemeGroupSlugField extends StringField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Group::ATTRIBUTE_SLUG, nullable: false);
    }

    public function description(): string
    {
        return 'The slug of the group';
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            'string',
            'max:192',
            'alpha_dash',
            Rule::unique(Group::class),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getUpdateRules(array $args): array
    {
        return [
            'sometimes',
            'required',
            'string',
            'max:192',
            'alpha_dash',
            Rule::unique(Group::class)->ignore(Arr::get($args, 'id')->getKey()),
        ];
    }
}
