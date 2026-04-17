<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Schema;

use App\Enums\Models\Wiki\SynonymType;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Models\User\Submission\SubmissionSynonym;
use Filament\Schemas\Schema;

class SynonymForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make(SubmissionSynonym::ATTRIBUTE_TYPE)
                    ->label(__('filament.fields.synonym.type.name'))
                    ->helperText(__('filament.fields.synonym.type.help'))
                    ->options(SynonymType::class)
                    ->required(),

                TextInput::make(SubmissionSynonym::ATTRIBUTE_TEXT)
                    ->label(__('filament.fields.synonym.text.name'))
                    ->helperText(__('filament.fields.synonym.text.help'))
                    ->required()
                    ->maxLength(255),
            ])
            ->columns(1);
    }
}
