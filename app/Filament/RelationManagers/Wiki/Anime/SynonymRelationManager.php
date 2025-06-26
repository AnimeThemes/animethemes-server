<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Anime;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Anime\Synonym as SynonymResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SynonymRelationManager.
 */
abstract class SynonymRelationManager extends BaseRelationManager
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
        return SynonymResource::form($schema);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(SynonymResource::getEloquentQuery()->getEagerLoads()))
                ->heading(SynonymResource::getPluralLabel())
                ->modelLabel(SynonymResource::getLabel())
                ->recordTitleAttribute(AnimeSynonym::ATTRIBUTE_TEXT)
                ->columns(SynonymResource::table($table)->getColumns())
                ->defaultSort(AnimeSynonym::TABLE.'.'.AnimeSynonym::ATTRIBUTE_ID, 'desc')
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
            ...SynonymResource::getFilters(),
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
            ...SynonymResource::getActions(),
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
            ...SynonymResource::getBulkActions(),
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
            ...SynonymResource::getTableActions(),
        ];
    }
}
