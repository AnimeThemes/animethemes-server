<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\Wiki\Series as SeriesResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class SeriesAnimeRelationManager.
 */
class SeriesAnimeRelationManager extends BaseRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @return string
     */
    protected static string $relationship = Anime::RELATION_SERIES;

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
        return SeriesResource::form($form);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return $table
            ->heading(SeriesResource::getPluralLabel())
            ->modelLabel(SeriesResource::getLabel())
            ->recordTitleAttribute(Series::ATTRIBUTE_NAME)
            ->inverseRelationship(Series::RELATION_ANIME)
            ->columns(SeriesResource::table($table)->getColumns())
            ->defaultSort(Series::TABLE.'.'.Series::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->headerActions(static::getHeaderActions())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
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
            parent::getFilters(),
            [],
        );
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
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
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}