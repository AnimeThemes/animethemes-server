<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\ExternalResource as ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Wiki\AnimeResource;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;

/**
 * Class ResourceRelationManager.
 */
abstract class ResourceRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ExternalResourceResource::class;

    /**
     * Get the pivot fields of the relation.
     *
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public function getPivotFields(): array
    {
        return [
            TextInput::make(AnimeResource::ATTRIBUTE_AS)
                ->label(__('filament.fields.anime.resources.as.name'))
                ->helperText(__('filament.fields.anime.resources.as.help')),
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
                ->recordTitleAttribute(ExternalResource::ATTRIBUTE_LINK)
                ->columns(ExternalResourceResource::table($table)->getColumns())
                ->defaultSort(ExternalResource::TABLE.'.'.ExternalResource::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),
            ...ExternalResourceResource::getActions(),
        ];
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
            ...ExternalResourceResource::getBulkActions(),
        ];
    }

    /**
     * Get the header actions available for the relation.
     * These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            ...ExternalResourceResource::getTableActions(),
        ];
    }
}
