<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Admin\FeaturedTheme;

use App\Contracts\GraphQL\Fields\CreatableField;
use App\Contracts\GraphQL\Fields\RequiredOnCreation;
use App\Contracts\GraphQL\Fields\UpdatableField;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\GraphQL\Schema\Fields\DateTimeTzField;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Support\Str;

class FeaturedThemeEndAtField extends DateTimeTzField implements CreatableField, RequiredOnCreation, UpdatableField
{
    public function __construct()
    {
        parent::__construct(FeaturedTheme::ATTRIBUTE_END_AT, 'endAt', nullable: false);
    }

    public function description(): string
    {
        return 'The end date of the resource';
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getCreationRules(array $args): array
    {
        $allowedDateFormats = array_column(AllowedDateFormat::cases(), 'value');

        return [
            'required',
            'after:'.FeaturedTheme::ATTRIBUTE_START_AT,
            Str::of('date_format:')
                ->append(implode(',', $allowedDateFormats))
                ->__toString(),
        ];
    }

    /**
     * @param  array<string, mixed>  $args
     */
    public function getUpdateRules(array $args): array
    {
        $allowedDateFormats = array_column(AllowedDateFormat::cases(), 'value');

        return [
            'sometimes',
            'required',
            'after:'.FeaturedTheme::ATTRIBUTE_START_AT,
            Str::of('date_format:')
                ->append(implode(',', $allowedDateFormats))
                ->__toString(),
        ];
    }
}
