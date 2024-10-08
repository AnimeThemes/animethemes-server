<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\Actions\HasPivotActionLogs;
use App\Enums\Auth\Role;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use Filament\Forms\Form;
use Filament\Tables\Actions\CreateAction as DefaultCreateAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Livewire\Component;

/**
 * Class CreateAction.
 */
class CreateAction extends DefaultCreateAction
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

        $this->form(fn (Form $form, BaseRelationManager $livewire) => [
            ...$livewire->form($form)->getComponents(),
            ...$livewire->getPivotFields(),
        ]);

        $this->after(function ($livewire, $record) {
            if ($livewire instanceof BaseRelationManager) {
                $relationship = $livewire->getRelationship();
                if ($relationship instanceof BelongsToMany) {
                    $this->pivotActionLog('Create and Attach', $livewire, $record);
                }

                if ($relationship instanceof HasMany) {
                    $this->associateActionLog('Create and Associate', $livewire, $record);
                }
            }
        });

        $this->hidden(function (Component $livewire, Request $request) {
            if ($livewire instanceof ResourceRelationManager) {
                return !$request->user()->hasRole(Role::ADMIN->value);
            }

            return false;
        });
    }
}
