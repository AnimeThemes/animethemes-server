<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\RelationManagers\Wiki\Song\PerformanceRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Performance;
use Filament\Tables\Table;

class GroupPerformanceArtistRelationManager extends PerformanceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Artist::RELATION_GROUP_PERFORMANCES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Performance::RELATION_SONG)
        )
            ->heading(__('filament.resources.label.group_performances'))
            ->modelLabel(__('filament.resources.singularLabel.group_performance'));
    }

    /**
     * @return array<int, \Filament\Actions\Action>
     */
    public static function getRecordActions(): array
    {
        return [];
    }

    /**
     * @param  array<int, \Filament\Actions\ActionGroup|\Filament\Actions\Action>|null  $actionsIncludedInGroup
     * @return array<int, \Filament\Actions\ActionGroup|\Filament\Actions\Action>
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [];
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array<int, \Filament\Actions\Action>
     */
    public static function getHeaderActions(): array
    {
        return [];
    }
}
