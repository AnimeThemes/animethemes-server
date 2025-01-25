<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Base;

use App\Filament\Actions\Base\ViewAction;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Admin\ActionLog;
use App\Models\Admin\ActionLog as ActionLogModel;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ActionLogRelationManager.
 */
class ActionLogRelationManager extends BaseRelationManager
{
    protected static string $relationship = 'actionlogs';

    protected static ?string $recordTitleAttribute = ActionLogModel::ATTRIBUTE_ID;

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(fn (Builder $query) => $query->with(ActionLog::getEloquentQuery()->getEagerLoads()))
            ->defaultSort(ActionLogModel::ATTRIBUTE_ID, 'desc')
            ->heading(__('filament.resources.label.action_logs'))
            ->pluralModelLabel(__('filament.resources.label.action_logs'))
            ->columns(ActionLog::table($table)->getColumns())
            ->paginationPageOptions([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->actions([
                ViewAction::make()
                    ->form(fn (Form $form) => ActionLog::form($form)),
            ]);
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
        return [];
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
