<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\Wiki\ThemeType;
use App\GraphQL\Definition\Fields\EnumField;
use App\GraphQL\Support\Filter\EqFilter;
use App\GraphQL\Support\Filter\Filter;
use App\GraphQL\Support\Filter\InFilter;
use App\GraphQL\Support\Filter\NotInFilter;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Validation\Rules\Enum;

class AnimeThemeTypeField extends EnumField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_TYPE, ThemeType::class, nullable: false);
    }

    public function description(): string
    {
        return 'The type of the sequence';
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return [
            new EqFilter($this),
            new InFilter($this, [ThemeType::OP->value, ThemeType::ED->value]),
            new NotInFilter($this),
        ];
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
            new Enum(ThemeType::class),
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
            new Enum(ThemeType::class),
        ];
    }
}
