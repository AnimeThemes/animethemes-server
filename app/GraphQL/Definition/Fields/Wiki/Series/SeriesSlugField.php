<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Series;

use App\Contracts\GraphQL\Fields\BindableField;
use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\GraphQL\Definition\Fields\StringField;
use App\Models\Wiki\Series;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class SeriesSlugField extends StringField implements BindableField, CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(Series::ATTRIBUTE_SLUG, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The URL slug & route key of the resource';
    }

    /**
     * Get the model that the field should bind to.
     *
     * @return class-string<Series>
     */
    public function bindTo(): string
    {
        return Series::class;
    }

    /**
     * Get the column that the field should use to bind.
     */
    public function bindUsingColumn(): string
    {
        return $this->column;
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
            Rule::unique(Series::class),
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
            Rule::unique(Series::class)->ignore(Arr::get($args, 'id')->getKey()),
        ];
    }
}
