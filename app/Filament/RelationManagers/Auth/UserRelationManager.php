<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Auth;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Auth\User as UserResource;
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

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(User::ATTRIBUTE_NAME)
                ->defaultSort(User::TABLE.'.'.User::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * Get the header actions available for the relation. These are merged with the table actions of the resources.
     *
     * @return \Filament\Actions\Action[]
     */
    public static function getHeaderActions(): array
    {
        return UserResource::getTableActions();
    }
}
