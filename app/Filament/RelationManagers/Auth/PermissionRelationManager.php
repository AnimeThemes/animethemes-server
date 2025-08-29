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

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Permission::ATTRIBUTE_NAME)
                ->defaultSort(Permission::TABLE.'.'.Permission::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * @return \Filament\Actions\Action[]
     */
    public static function getHeaderActions(): array
    {
        return PermissionResource::getTableActions();
    }
}
