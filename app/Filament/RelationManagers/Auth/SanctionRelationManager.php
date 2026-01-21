<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Auth;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Auth\Sanction as SanctionResource;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\Sanction;
use Filament\Tables\Table;

abstract class SanctionRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = SanctionResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Sanction::ATTRIBUTE_NAME)
                ->defaultSort(Sanction::TABLE.'.'.Sanction::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * @return \Filament\Actions\Action[]
     */
    public static function getHeaderActions(): array
    {
        return SanctionResource::getTableActions();
    }
}
