<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Auth;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Auth\User as UserResource;
use App\Models\Auth\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class UserRelationManager.
 */
abstract class UserRelationManager extends BaseRelationManager
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
        return UserResource::form($form);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(UserResource::getEloquentQuery()->getEagerLoads()))
                ->heading(UserResource::getPluralLabel())
                ->modelLabel(UserResource::getLabel())
                ->recordTitleAttribute(User::ATTRIBUTE_NAME)
                ->columns(UserResource::table($table)->getColumns())
                ->defaultSort(User::TABLE.'.'.User::ATTRIBUTE_ID, 'desc')
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
            ...UserResource::getFilters(),
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
            ...UserResource::getActions(),
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
            ...UserResource::getBulkActions(),
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
        return UserResource::getTableActions();
    }
}
