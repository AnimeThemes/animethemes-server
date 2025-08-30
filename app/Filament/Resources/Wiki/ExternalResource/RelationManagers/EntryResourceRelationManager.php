<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\RelationManagers;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Wiki\Anime\Theme\EntryRelationManager;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Morph\Resourceable;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class EntryResourceRelationManager extends EntryRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = ExternalResource::RELATION_ANIMETHEMEENTRIES;

    /**
     * @return Component[]
     */
    public function getPivotComponents(): array
    {
        return [
            TextInput::make(Resourceable::ATTRIBUTE_AS)
                ->label(__('filament.fields.resourceable.as.name'))
                ->helperText(__('filament.fields.resourceable.as.help')),
        ];
    }

    /**
     * @return Column[]
     */
    public function getPivotColumns(): array
    {
        return [
            TextColumn::make(Resourceable::ATTRIBUTE_AS)
                ->label(__('filament.fields.resourceable.as.name')),
        ];
    }

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(AnimeThemeEntry::RELATION_RESOURCES)
        );
    }
}
