<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\ArtistResource;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class ResourceArtistRelationManager extends ResourceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Artist::RELATION_RESOURCES;

    /**
     * Get the pivot components of the relation.
     *
     * @return array<int, Component>
     */
    public function getPivotComponents(): array
    {
        return [
            TextInput::make(ArtistResource::ATTRIBUTE_AS)
                ->label(__('filament.fields.artist.resources.as.name'))
                ->helperText(__('filament.fields.artist.resources.as.help')),
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
            TextColumn::make(ArtistResource::ATTRIBUTE_AS)
                ->label(__('filament.fields.artist.resources.as.name')),
        ];
    }

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(ExternalResource::RELATION_ARTISTS)
        );
    }
}
