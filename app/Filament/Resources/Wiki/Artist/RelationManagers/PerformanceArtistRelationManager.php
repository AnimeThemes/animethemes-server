<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Wiki\Song\PerformanceRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Performance;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Component;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\Relation;

class PerformanceArtistRelationManager extends PerformanceRelationManager
{
    /**
     * @return Component[]
     */
    public function getPivotComponents(): array
    {
        return [
            Hidden::make(Performance::ATTRIBUTE_ARTIST_TYPE)
                ->default(Relation::getMorphAlias(Artist::class)),

            TextInput::make(Performance::ATTRIBUTE_AS)
                ->label(__('filament.fields.performance.as.name'))
                ->helperText(__('filament.fields.performance.as.help')),

            TextInput::make(Performance::ATTRIBUTE_ALIAS)
                ->label(__('filament.fields.performance.alias.name'))
                ->helperText(__('filament.fields.performance.alias.help')),
        ];
    }

    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Artist::RELATION_PERFORMANCES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Performance::RELATION_SONG)
        );
    }
}
