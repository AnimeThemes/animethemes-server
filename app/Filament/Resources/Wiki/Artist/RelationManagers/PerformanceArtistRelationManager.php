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

/**
 * Class PerformanceArtistRelationManager.
 */
class PerformanceArtistRelationManager extends PerformanceRelationManager
{
    /**
     * Get the pivot components of the relation.
     *
     * @return array<int, Component>
     */
    public function getPivotComponents(): array
    {
        return [
            Hidden::make(Performance::ATTRIBUTE_ARTIST_TYPE)
                ->default(Artist::class),

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
     *
     * @var string
     */
    protected static string $relationship = Artist::RELATION_PERFORMANCES;

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Performance::RELATION_SONG)
        );
    }
}
