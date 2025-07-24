<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class ResourceAnimeRelationManager extends ResourceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Anime::RELATION_RESOURCES;

    /**
     * Get the pivot components of the relation.
     *
     * @return Component[]
     */
    public function getPivotComponents(): array
    {
        return [
            TextInput::make(AnimeResource::ATTRIBUTE_AS)
                ->label(__('filament.fields.anime.resources.as.name'))
                ->helperText(__('filament.fields.anime.resources.as.help')),
        ];
    }

    /**
     * Get the pivot columns of the relation.
     *
     * @return Column[]
     */
    public function getPivotColumns(): array
    {
        return [
            TextColumn::make(AnimeResource::ATTRIBUTE_AS)
                ->label(__('filament.fields.anime.resources.as.name')),
        ];
    }

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(ExternalResource::RELATION_ANIME)
        );
    }
}
