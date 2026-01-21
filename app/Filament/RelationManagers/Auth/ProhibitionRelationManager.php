<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Auth;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Auth\Prohibition as ProhibitionResource;
use App\Filament\Resources\BaseResource;
use App\Models\Auth\Prohibition;
use Filament\Tables\Table;

abstract class ProhibitionRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ProhibitionResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Prohibition::ATTRIBUTE_NAME)
                ->defaultSort(Prohibition::TABLE.'.'.Prohibition::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * @return \Filament\Actions\Action[]
     */
    public static function getHeaderActions(): array
    {
        return ProhibitionResource::getTableActions();
    }
}
