<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Schema;

use App\Filament\Components\Fields\TextInput;
use App\Models\User\Submission\SubmissionStudio;
use App\Models\Wiki\Studio;
use Filament\Schemas\Schema;

class StudioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(SubmissionStudio::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.studio.name.name'))
                    ->helperText(__('filament.fields.studio.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

                TextInput::make(SubmissionStudio::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.studio.slug.name'))
                    ->helperText(__('filament.fields.studio.slug.help'))
                    ->required()
                    ->alphaDash()
                    ->maxLength(192)
                    ->unique(Studio::TABLE, Studio::ATTRIBUTE_SLUG),
            ])
            ->columns(1);
    }
}
