<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Song;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Song\Performance as PerformanceResource;
use App\Models\Wiki\Song\Performance;
use Filament\Tables\Table;

abstract class PerformanceRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = PerformanceResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Performance::ATTRIBUTE_ID)
                ->defaultSort(Performance::TABLE.'.'.Performance::ATTRIBUTE_ID, 'desc')
        );
    }
}
