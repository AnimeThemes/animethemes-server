<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseManageResources;
use Filament\Forms\Form;
use Filament\Tables\Actions\EditAction as DefaultEditAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class EditAction.
 */
class EditAction extends DefaultEditAction
{
    use HasPivotActionLogs;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.edit'));

        $this->form(fn (Form $form, BaseRelationManager|BaseManageResources $livewire) => [
            ...$livewire->form($form)->getComponents(),
            ...($livewire instanceof BaseRelationManager ? $livewire->getPivotFields() : []),
        ]);

        $this->after(function ($livewire, $record) {
            if ($livewire instanceof BaseRelationManager) {
                if ($livewire->getRelationship() instanceof BelongsToMany) {
                    $this->pivotActionLog('Update Attached', $livewire, $record);
                }
            }
        });
    }
}
