<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Wiki\Song\PerformanceRelationManager;
use App\Filament\Resources\Wiki\ArtistResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Performance;
use Filament\Schemas\Components\Component;
use Filament\Tables\Table;

class MemberPerformanceArtistRelationManager extends PerformanceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Artist::RELATION_MEMBER_PERFORMANCES;

    /**
     * @return Component[]
     */
    public function getPivotComponents(): array
    {
        return [
            BelongsTo::make(Performance::ATTRIBUTE_ARTIST)
                ->resource(ArtistResource::class)
                ->label(__('filament.fields.performance.group'))
                ->required()
                ->columnSpanFull(),

            TextInput::make(Performance::ATTRIBUTE_AS)
                ->label(__('filament.fields.performance.as.name'))
                ->helperText(__('filament.fields.performance.as.help')),

            TextInput::make(Performance::ATTRIBUTE_ALIAS)
                ->label(__('filament.fields.performance.alias.name'))
                ->helperText(__('filament.fields.performance.alias.help')),

            TextInput::make(Performance::ATTRIBUTE_MEMBER_AS)
                ->label(__('filament.fields.performance.member_as.name'))
                ->helperText(__('filament.fields.performance.member_as.help')),

            TextInput::make(Performance::ATTRIBUTE_MEMBER_ALIAS)
                ->label(__('filament.fields.performance.member_alias.name'))
                ->helperText(__('filament.fields.performance.member_alias.help')),
        ];
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->heading(__('filament.resources.label.member_performances'))
            ->modelLabel(__('filament.resources.singularLabel.member_performance'));
    }
}
