<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Schema\Fields\StringField;
use App\Models\Wiki\Anime;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AnimeSlugField extends StringField implements BindableField, CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Anime::ATTRIBUTE_SLUG, nullable: false);
    }

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
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            'max:192',
            'alpha_dash',
            Rule::unique(Anime::class),
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
            'max:192',
            'alpha_dash',
            Rule::unique(Anime::class)->ignore(Arr::get($args, 'id')->getKey()),
        ];
    }
}
