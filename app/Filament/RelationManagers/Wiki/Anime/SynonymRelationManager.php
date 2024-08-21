<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Anime;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Anime\Synonym as SynonymResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class SynonymRelationManager.
 */
abstract class SynonymRelationManager extends BaseRelationManager
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
        return SynonymResource::form($form);
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
        return parent::table(
            $table
                ->heading(SynonymResource::getPluralLabel())
                ->modelLabel(SynonymResource::getLabel())
                ->recordTitleAttribute(AnimeSynonym::ATTRIBUTE_TEXT)
                ->columns(SynonymResource::table($table)->getColumns())
                ->defaultSort(AnimeSynonym::TABLE . '.' . AnimeSynonym::ATTRIBUTE_ID, 'desc')
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
            SynonymResource::getFilters(),
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
            SynonymResource::getActions(),
        );
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            SynonymResource::getBulkActions(),
        );
    }

    /**
     * Get the header actions available for the relation. These are merged with the table actions of the resources.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            SynonymResource::getTableActions(),
        );
    }
}
