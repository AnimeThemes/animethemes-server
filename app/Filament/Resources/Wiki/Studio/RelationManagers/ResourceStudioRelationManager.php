<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Studio\RelationManagers;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class ResourceStudioRelationManager extends ResourceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Studio::RELATION_RESOURCES;

    /**
     * Get the pivot components of the relation.
     *
     * @return Component[]
     */
    public function getPivotComponents(): array
    {
        return [
            TextInput::make(StudioResource::ATTRIBUTE_AS)
                ->label(__('filament.fields.studio.resources.as.name'))
                ->helperText(__('filament.fields.studio.resources.as.help')),
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
            TextColumn::make(StudioResource::ATTRIBUTE_AS)
                ->label(__('filament.fields.studio.resources.as.name')),
        ];
    }

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(ExternalResource::RELATION_STUDIOS)
        );
    }
}
