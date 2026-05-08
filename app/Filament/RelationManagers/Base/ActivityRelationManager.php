<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Base;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Admin\ActivityResource;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\Activity;
use Filament\Tables\Table;

class ActivityRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'activities';

    protected static ?string $recordTitleAttribute = Activity::ATTRIBUTE_ID;

    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ActivityResource::class;

    public static function isLazy(): true
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->defaultPaginationPageOption(5);
    }

    public function canCreate(): bool
    {
        return false;
    }
}
