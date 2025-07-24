<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Wiki\ArtistRelationManager;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class GroupArtistRelationManager extends ArtistRelationManager
{
    /**
     * Get the pivot components of the relation.
     *
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public function getPivotComponents(): array
    {
        return [
            TextInput::make(ArtistMember::ATTRIBUTE_AS)
                ->label(__('filament.fields.artist.groups.as.name'))
                ->helperText(__('filament.fields.artist.groups.as.help')),

            TextInput::make(ArtistMember::ATTRIBUTE_ALIAS)
                ->label(__('filament.fields.artist.groups.alias.name'))
                ->helperText(__('filament.fields.artist.groups.alias.help')),

            TextInput::make(ArtistMember::ATTRIBUTE_NOTES)
                ->label(__('filament.fields.artist.groups.notes.name'))
                ->helperText(__('filament.fields.artist.groups.notes.help')),
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
            TextColumn::make(ArtistMember::ATTRIBUTE_AS)
                ->label(__('filament.fields.artist.groups.as.name')),

            TextColumn::make(ArtistMember::ATTRIBUTE_ALIAS)
                ->label(__('filament.fields.artist.groups.alias.name')),

            TextColumn::make(ArtistMember::ATTRIBUTE_NOTES)
                ->label(__('filament.fields.artist.groups.notes.name')),
        ];
    }

    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Artist::RELATION_GROUPS;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Artist::RELATION_MEMBERS)
        )
            ->heading(__('filament.resources.label.groups'))
            ->modelLabel(__('filament.resources.singularLabel.group'));
    }
}
