<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\List\ExternalProfile as ExternalProfileResource;
use App\Models\List\ExternalProfile;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class ExternalProfileRelationManager.
 */
abstract class ExternalProfileRelationManager extends BaseRelationManager
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
        return ExternalProfileResource::form($form);
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
                ->heading(ExternalProfileResource::getPluralLabel())
                ->modelLabel(ExternalProfileResource::getLabel())
                ->recordTitleAttribute(ExternalProfile::ATTRIBUTE_NAME)
                ->columns(ExternalProfileResource::table($table)->getColumns())
                ->defaultSort(ExternalProfile::TABLE . '.' . ExternalProfile::ATTRIBUTE_ID, 'desc')
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
            ExternalProfileResource::getFilters(),
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
            ExternalProfileResource::getActions(),
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
            ExternalProfileResource::getBulkActions(),
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
            ExternalProfileResource::getTableActions(),
        );
    }
}
