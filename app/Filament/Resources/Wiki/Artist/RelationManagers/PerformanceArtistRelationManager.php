<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\RelationManagers\Wiki\Song\PerformanceRelationManager;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Performance;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;

/**
 * Class PerformanceArtistRelationManager.
 */
class PerformanceArtistRelationManager extends PerformanceRelationManager
{
    /**
     * Get the pivot fields of the relation.
     *
     * @return array<int, Component>
     */
    public function getPivotFields(): array
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

    /**
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return array_merge(
            [],
            parent::getFilters(),
        );
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            [],
        );
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the header actions available for the relation. These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
