<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Document\Page;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\Document\Page;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class PageSlugField extends StringField implements BindableField, CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Page::ATTRIBUTE_SLUG, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The URL slug & route key of the resource';
    }

    /**
     * The resolver to cast the model.
     */
    public function bindResolver(array $args): null
    {
        return null;
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
            'regex:/^[\pL\pM\pN\/_-]+$/u',
            Rule::unique(Page::class),
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
            'regex:/^[\pL\pM\pN\/_-]+$/u',
            Rule::unique(Page::class)->ignore(Arr::get($args, 'id')->getKey()),
        ];
    }
}
