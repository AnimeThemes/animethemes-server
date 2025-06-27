<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Anime\Theme;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Anime\Theme\Entry as EntryResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class EntryRelationManager.
 */
abstract class EntryRelationManager extends BaseRelationManager
{
    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Form $form): Form
    {
        return EntryResource::form($form);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(EntryResource::getEloquentQuery()->getEagerLoads()))
                ->heading(EntryResource::getPluralLabel())
                ->modelLabel(EntryResource::getLabel())
                ->recordTitleAttribute(AnimeThemeEntry::ATTRIBUTE_VERSION)
                ->columns(EntryResource::table($table)->getColumns())
                ->defaultSort(AnimeThemeEntry::TABLE.'.'.AnimeThemeEntry::ATTRIBUTE_ID, 'desc')
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
            ...EntryResource::getFilters(),
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            ...parent::getActions(),
            ...EntryResource::getActions(),
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
            ...EntryResource::getBulkActions(),
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
            ...EntryResource::getTableActions(),
        ];
    }
}
