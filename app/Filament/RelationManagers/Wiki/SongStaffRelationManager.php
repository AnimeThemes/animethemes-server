<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\SongStaffResource;
use App\Models\Wiki\SongStaff;
use Filament\Tables\Table;

abstract class SongStaffRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = SongStaffResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(SongStaff::ATTRIBUTE_ID)
        );
    }
}
