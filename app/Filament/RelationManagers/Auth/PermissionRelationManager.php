<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Auth;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Auth\Permission as PermissionResource;
use App\Models\Auth\Permission;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PermissionRelationManager.
 */
abstract class PermissionRelationManager extends BaseRelationManager
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
        return PermissionResource::form($form);
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
            ->modifyQueryUsing(fn (Builder $query) => $query->with(PermissionResource::getEloquentQuery()->getEagerLoads()))
                ->heading(PermissionResource::getPluralLabel())
                ->modelLabel(PermissionResource::getLabel())
                ->recordTitleAttribute(Permission::ATTRIBUTE_NAME)
                ->columns(PermissionResource::table($table)->getColumns())
                ->defaultSort(Permission::TABLE . '.' . Permission::ATTRIBUTE_ID, 'desc')
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
            ...PermissionResource::getFilters(),
        ];
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
        return PermissionResource::getActions();
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
            ...PermissionResource::getBulkActions(),
        ];
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
        return PermissionResource::getTableActions();
    }
}
