<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Wiki\Anime\Theme;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Models\Wiki\ThemeType;
use App\GraphQL\Definition\Fields\EnumField;
use App\GraphQL\Support\Directives\Filters\EqFilterDirective;
use App\GraphQL\Support\Directives\Filters\FilterDirective;
use App\GraphQL\Support\Directives\Filters\InFilterDirective;
use App\GraphQL\Support\Directives\Filters\NotInFilterDirective;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Validation\Rules\Enum;

class AnimeThemeTypeField extends EnumField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(AnimeTheme::ATTRIBUTE_TYPE, ThemeType::class, nullable: false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The type of the sequence';
    }

    /**
     * The directives available for this filter.
     *
     * @return FilterDirective[]
     */
    public function filterDirectives(): array
    {
        return [
            new EqFilterDirective($this),
            new InFilterDirective($this, '[OP, ED]'),
            new NotInFilterDirective($this),
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
