<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\ExternalResource\RelationManagers;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\RelationManagers\Wiki\StudioRelationManager;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

/**
 * Class StudioResourceRelationManager.
 */
class StudioResourceRelationManager extends StudioRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = ExternalResource::RELATION_STUDIOS;

    /**
     * Get the pivot components of the relation.
     *
     * @return array<int, Component>
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
     * @return array<int, Column>
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
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Studio::RELATION_RESOURCES)
        );
    }
}
