<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\StudioResource;
use App\Models\Wiki\Studio;
use Filament\Tables\Table;

abstract class StudioRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = StudioResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Studio::ATTRIBUTE_NAME)
                ->defaultSort(Studio::TABLE.'.'.Studio::ATTRIBUTE_ID, 'desc')
        );
    }
}
