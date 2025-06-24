<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Models\Wiki\Anime;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnimeRelationManager.
 */
abstract class AnimeRelationManager extends BaseRelationManager
{
    /**
     * The form to the actions.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Schema $schema): Schema
    {
        return AnimeResource::form($schema);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(AnimeResource::getEloquentQuery()->getEagerLoads()))
                ->heading(AnimeResource::getPluralLabel())
                ->modelLabel(AnimeResource::getLabel())
                ->recordTitleAttribute(Anime::ATTRIBUTE_NAME)
                ->columns(AnimeResource::table($table)->getColumns())
                ->defaultSort(Anime::TABLE.'.'.Anime::ATTRIBUTE_ID, 'desc')
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
        return [
            ...AnimeResource::getFilters(),
        ];
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
            ...AnimeResource::getRecordActions(),
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
            ...AnimeResource::getBulkActions(),
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
            ...AnimeResource::getTableActions(),
        ];
    }
}
