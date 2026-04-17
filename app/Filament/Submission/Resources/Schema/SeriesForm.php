<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Schema;

use App\Filament\Components\Fields\TextInput;
use App\Models\User\Submission\SubmissionSeries;
use App\Models\Wiki\Series;
use Filament\Schemas\Schema;

class SeriesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(SubmissionSeries::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.series.name.name'))
                    ->helperText(__('filament.fields.series.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

                TextInput::make(SubmissionSeries::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.series.slug.name'))
                    ->helperText(__('filament.fields.series.slug.help'))
                    ->required()
                    ->alphaDash()
                    ->maxLength(192)
                    ->unique(Series::TABLE, Series::ATTRIBUTE_SLUG),
            ])
            ->columns(1);
    }
}
