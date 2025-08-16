<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\RelationManagers;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Wiki\StudioRelationManager;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Morph\Resourceable;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class StudioResourceRelationManager extends StudioRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = ExternalResource::RELATION_STUDIOS;

    /**
     * Get the pivot components of the relation.
     *
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
     * Get the pivot columns of the relation.
     *
     * @return Column[]
     */
    public function getPivotColumns(): array
    {
        return [
            TextColumn::make(Resourceable::ATTRIBUTE_AS)
                ->label(__('filament.fields.resourceable.as.name')),
        ];
    }

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Studio::RELATION_RESOURCES)
        );
    }
}
