<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Video;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Video\Script as ScriptResource;
use App\Models\Wiki\Video\VideoScript;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ScriptRelationManager.
 */
abstract class ScriptRelationManager extends BaseRelationManager
{
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
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->modifyQueryUsing(fn (Builder $query) => $query->with(ScriptResource::getEloquentQuery()->getEagerLoads()))
                ->heading(ScriptResource::getPluralLabel())
                ->modelLabel(ScriptResource::getLabel())
                ->recordTitleAttribute(VideoScript::ATTRIBUTE_PATH)
                ->columns(ScriptResource::table($table)->getColumns())
                ->defaultSort(VideoScript::TABLE.'.'.VideoScript::ATTRIBUTE_ID, 'desc')
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
            ...ScriptResource::getFilters(),
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
            ...ScriptResource::getActions(),
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
            ...ScriptResource::getBulkActions(),
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
            ...ScriptResource::getTableActions(),
        ];
    }

    /**
     * Determine whether the related model can be created.
     *
     * @return bool
     */
    protected function canCreate(): bool
    {
        return false;
    }
}
