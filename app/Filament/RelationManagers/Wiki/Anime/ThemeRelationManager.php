<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Anime;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Theme as ThemeResource;
use App\Models\Wiki\Anime\AnimeTheme;
use Filament\Tables\Table;

abstract class ThemeRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ThemeResource::class;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitle(fn ($record) => $record->getName())
                ->defaultSort(AnimeTheme::TABLE.'.'.AnimeTheme::ATTRIBUTE_ID, 'desc')
        );
    }
}
