<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Anime;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Anime\Theme as ThemeResource;
use App\Models\Wiki\Anime\AnimeTheme;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ThemeRelationManager.
 */
abstract class ThemeRelationManager extends BaseRelationManager
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
        return ThemeResource::form($form);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(ThemeResource::getEloquentQuery()->getEagerLoads()))
                ->heading(ThemeResource::getPluralLabel())
                ->modelLabel(ThemeResource::getLabel())
                ->recordTitle(fn ($record) => $record->getName())
                ->columns(ThemeResource::table($table)->getColumns())
                ->defaultSort(AnimeTheme::TABLE . '.' . AnimeTheme::ATTRIBUTE_ID, 'desc')
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
            ThemeResource::getFilters(),
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
            ...ThemeResource::getActions(),
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
            ...ThemeResource::getBulkActions(),
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
            ...ThemeResource::getTableActions(),
        ];
    }
}
