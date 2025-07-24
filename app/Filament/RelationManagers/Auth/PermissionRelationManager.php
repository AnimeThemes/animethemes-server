<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Auth;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Auth\Permission as PermissionResource;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\Permission;
use Filament\Tables\Table;

abstract class PermissionRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = PermissionResource::class;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Permission::ATTRIBUTE_NAME)
                ->defaultSort(Permission::TABLE.'.'.Permission::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * Get the header actions available for the relation. These are merged with the table actions of the resources.
     *
     * @return \Filament\Actions\Action[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return PermissionResource::getTableActions();
    }
}
