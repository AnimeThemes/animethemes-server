<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\RelationManagers;

use App\Filament\RelationManagers\Wiki\Video\ScriptRelationManager;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Filament\Tables\Table;

class ScriptVideoRelationManager extends ScriptRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Video::RELATION_SCRIPT;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(VideoScript::RELATION_VIDEO)
        );
    }
}
