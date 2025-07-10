<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\RelationManagers;

use App\Filament\RelationManagers\Wiki\Video\ScriptRelationManager;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Filament\Tables\Table;

/**
 * Class ScriptVideoRelationManager.
 */
class ScriptVideoRelationManager extends ScriptRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Video::RELATION_SCRIPT;

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
                ->inverseRelationship(VideoScript::RELATION_VIDEO)
        );
    }
}
