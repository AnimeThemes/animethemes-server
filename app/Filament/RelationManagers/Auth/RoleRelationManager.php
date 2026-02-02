<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Auth;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Auth\RoleResource;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\Role;
use Filament\Tables\Table;

abstract class RoleRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = RoleResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Role::ATTRIBUTE_NAME)
                ->defaultSort(Role::TABLE.'.'.Role::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * @return \Filament\Actions\Action[]
     */
    public static function getHeaderActions(): array
    {
        return RoleResource::getTableActions();
    }
}
