<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Admin\FeaturedTheme;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\GraphQL\Definition\Fields\DateTimeTzField;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class FeaturedThemeStartAtField extends DateTimeTzField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(FeaturedTheme::ATTRIBUTE_START_AT, 'startAt', nullable: false);
    }

    public function description(): string
    {
        return 'The start date of the resource';
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  array<string, mixed>  $args
     * @return array
     */
    public function getCreationRules(array $args): array
    {
        $allowedDateFormats = array_column(AllowedDateFormat::cases(), 'value');

        return [
            'required',
            Str::of('date_format:')
                ->append(implode(',', $allowedDateFormats))
                ->__toString(),
            Str::of('before:')
                ->append(Arr::get($args, FeaturedTheme::ATTRIBUTE_END_AT))
                ->__toString(),
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
        $allowedDateFormats = array_column(AllowedDateFormat::cases(), 'value');

        return [
            'sometimes',
            'required',
            Str::of('date_format:')
                ->append(implode(',', $allowedDateFormats))
                ->__toString(),
            Str::of('before:')
                ->append(Arr::get($args, FeaturedTheme::ATTRIBUTE_END_AT))
                ->__toString(),
        ];
    }
}
