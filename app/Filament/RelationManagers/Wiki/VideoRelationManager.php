<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\VideoResource;
use App\Models\Wiki\Video;
use Filament\Tables\Table;

abstract class VideoRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = VideoResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(Video::ATTRIBUTE_FILENAME)
                ->defaultSort(Video::TABLE.'.'.Video::ATTRIBUTE_ID, 'desc')
        );
    }

    public function canCreate(): bool
    {
        return false;
    }
}
