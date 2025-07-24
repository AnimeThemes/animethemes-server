<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Video;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Video\Script as ScriptResource;
use App\Models\Wiki\Video\VideoScript;
use Filament\Tables\Table;

abstract class ScriptRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ScriptResource::class;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(VideoScript::ATTRIBUTE_PATH)
                ->defaultSort(VideoScript::TABLE.'.'.VideoScript::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * Determine whether the related model can be created.
     */
    public function canCreate(): bool
    {
        return false;
    }
}
