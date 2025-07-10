<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\RelationManagers;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\RelationManagers\Wiki\SongRelationManager;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

/**
 * Class SongResourceRelationManager.
 */
class SongResourceRelationManager extends SongRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = ExternalResource::RELATION_SONGS;

    /**
     * Get the pivot components of the relation.
     *
     * @return array<int, Component>
     */
    public function getPivotComponents(): array
    {
        return [
            TextInput::make(SongResource::ATTRIBUTE_AS)
                ->label(__('filament.fields.song.resources.as.name'))
                ->helperText(__('filament.fields.song.resources.as.help')),
        ];
    }

    /**
     * Get the pivot columns of the relation.
     *
     * @return array<int, Column>
     */
    public function getPivotColumns(): array
    {
        return [
            TextColumn::make(SongResource::ATTRIBUTE_AS)
                ->label(__('filament.fields.song.resources.as.name')),
        ];
    }

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
                ->inverseRelationship(Song::RELATION_RESOURCES)
        );
    }
}
