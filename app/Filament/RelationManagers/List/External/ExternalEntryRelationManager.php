<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List\External;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\List\External\ExternalEntry as ExternalEntryResource;
use App\Models\List\External\ExternalEntry;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class ExternalEntryRelationManager.
 */
abstract class ExternalEntryRelationManager extends BaseRelationManager
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
        return ExternalEntryResource::form($form);
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
                ->heading(ExternalEntryResource::getPluralLabel())
                ->modelLabel(ExternalEntryResource::getLabel())
                ->recordTitleAttribute(ExternalEntry::ATTRIBUTE_ID)
                ->columns(ExternalEntryResource::table($table)->getColumns())
                ->defaultSort(ExternalEntry::TABLE . '.' . ExternalEntry::ATTRIBUTE_ID, 'desc')
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
            ExternalEntryResource::getFilters(),
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
            ExternalEntryResource::getActions(),
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
            ExternalEntryResource::getBulkActions(),
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
            ExternalEntryResource::getTableActions(),
        );
    }
}
