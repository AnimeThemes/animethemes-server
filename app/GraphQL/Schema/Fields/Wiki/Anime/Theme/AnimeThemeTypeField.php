<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Wiki\Anime\Theme;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\Wiki\ThemeType;
use App\GraphQL\Filter\EqFilter;
use App\GraphQL\Filter\Filter;
use App\GraphQL\Filter\InFilter;
use App\GraphQL\Filter\NotInFilter;
use App\GraphQL\Schema\Fields\EnumField;
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
            new InFilter($this),
            new NotInFilter($this),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        return [
            'required',
            new Enum(ThemeType::class),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
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
