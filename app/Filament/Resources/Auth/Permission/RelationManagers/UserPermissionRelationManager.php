<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\Permission\RelationManagers;

use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\Auth\User as UserResource;
use App\Models\Auth\Permission;
use App\Models\Auth\User;
use Filament\Forms\Form;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;

/**
 * Class UserPermissionRelationManager.
 */
class UserPermissionRelationManager extends BaseRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @return string
     */
    protected static string $relationship = Permission::RELATION_USERS;

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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return $table
            ->heading(UserResource::getPluralLabel())
            ->modelLabel(UserResource::getLabel())
            ->recordTitleAttribute(User::ATTRIBUTE_NAME)
            ->inverseRelationship(User::RELATION_PERMISSIONS)
            ->columns(UserResource::table($table)->getColumns())
            ->defaultSort(User::TABLE.'.'.User::ATTRIBUTE_ID, 'desc');
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
        return [];
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
        return [
            ViewAction::make(),
        ];
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
