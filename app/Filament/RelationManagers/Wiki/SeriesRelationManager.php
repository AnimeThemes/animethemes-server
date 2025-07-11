<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Series as SeriesResource;
use App\Models\Wiki\Series;
use Filament\Tables\Table;

/**
 * Class SeriesRelationManager.
 */
abstract class SeriesRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = SeriesResource::class;

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Series::ATTRIBUTE_NAME)
                ->defaultSort(Series::TABLE.'.'.Series::ATTRIBUTE_ID, 'desc')
        );
    }
}
