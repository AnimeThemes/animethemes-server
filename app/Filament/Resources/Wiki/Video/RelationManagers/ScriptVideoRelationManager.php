<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\RelationManagers;

use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\Wiki\Video\Script as ScriptResource;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class ScriptVideoRelationManager.
 */
class ScriptVideoRelationManager extends BaseRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @return string
     */
    protected static string $relationship = Video::RELATION_SCRIPT;

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Form $form): Form
    {
        return ScriptResource::form($form);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return $table
            ->heading(ScriptResource::getLabel())
            ->modelLabel(ScriptResource::getLabel())
            ->recordTitleAttribute(VideoScript::ATTRIBUTE_PATH)
            ->inverseRelationship(VideoScript::RELATION_VIDEO)
            ->columns(ScriptResource::table($table)->getColumns());
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
        return array_merge(
            parent::getFilters(),
            [],
        );
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            [],
        );
    }

    /**
     * Get the bulk actions available for the relation.
     * 
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return ScriptResource::getHeaderActions();
    }
}
