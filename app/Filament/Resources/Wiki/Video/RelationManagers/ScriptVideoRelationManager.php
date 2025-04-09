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

    /**
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return [
            ...parent::getFilters(),
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            ...parent::getActions(),
        ];
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
        ];
    }

    /**
     * Get the header actions available for the relation.
     * These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
        ];
    }
}
