<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Group\RelationManagers;

use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\Wiki\Anime\Theme as ThemeResource;
use App\Models\Wiki\Group;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class ThemeGroupRelationManager.
 */
class ThemeGroupRelationManager extends BaseRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @return string
     */
    protected static string $relationship = Group::RELATION_THEMES;

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
     * The index page of the Theme.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return $table
            ->heading(ThemeResource::getPluralLabel())
            ->modelLabel(ThemeResource::getLabel())
            ->recordTitleAttribute(ThemeModel::ATTRIBUTE_SLUG)
            ->inverseRelationship(ThemeModel::RELATION_GROUP)
            ->columns(ThemeResource::table($table)->getColumns())
            ->defaultSort(ThemeModel::TABLE.'.'.ThemeModel::ATTRIBUTE_ID, 'desc')
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