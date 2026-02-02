<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List\External;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\External\ExternalEntryResource;
use App\Models\List\External\ExternalEntry;
use Filament\Tables\Table;

abstract class ExternalEntryRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ExternalEntryResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(ExternalEntry::ATTRIBUTE_ID)
                ->defaultSort(ExternalEntry::TABLE.'.'.ExternalEntry::ATTRIBUTE_ID, 'desc')
        );
    }
}
