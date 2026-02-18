<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\SynonymResource;
use App\Models\Wiki\Synonym;
use Filament\Tables\Table;

abstract class SynonymRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = SynonymResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Synonym::ATTRIBUTE_TEXT)
                ->defaultSort(Synonym::TABLE.'.'.Synonym::ATTRIBUTE_ID, 'desc')
        );
    }
}
