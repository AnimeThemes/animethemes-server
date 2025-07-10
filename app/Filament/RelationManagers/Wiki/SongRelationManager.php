<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Song as SongResource;
use App\Models\Wiki\Song;
use Filament\Tables\Table;

/**
 * Class SongRelationManager.
 */
abstract class SongRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = SongResource::class;

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
                ->recordTitleAttribute(Song::ATTRIBUTE_TITLE)
                ->defaultSort(Song::TABLE.'.'.Song::ATTRIBUTE_ID, 'desc')
        );
    }
}
