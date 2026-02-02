<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Auth;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Auth\UserResource;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\User;
use Filament\Tables\Table;

abstract class UserRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(User::ATTRIBUTE_NAME)
                ->defaultSort(User::TABLE.'.'.User::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * @return \Filament\Actions\Action[]
     */
    public static function getHeaderActions(): array
    {
        return UserResource::getTableActions();
    }
}
