<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Enums\Auth\Role;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Models\Auth\User;
use Filament\Forms\Form;
use Filament\Tables\Actions\CreateAction as DefaultCreateAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

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

        $this->visible(function (BaseRelationManager $livewire) {
            if ($livewire instanceof ResourceRelationManager) {
                /** @var User $user */
                $user = Auth::user();
                return $user->hasRole(Role::ADMIN->value);
            }

            if ($livewire->getRelationship() instanceof BelongsToMany) {
                return false;
            }

            $ownerRecord = $livewire->getOwnerRecord();

            $gate = Gate::getPolicyFor($ownerRecord);

            $ability = Str::of('addAny')
                ->append(Str::singular(class_basename($livewire->getTable()->getModel())))
                ->__toString();

            return is_object($gate) & method_exists($gate, $ability)
                ? Gate::forUser(Auth::user())->any($ability, $ownerRecord)
                : true;
        });
    }
}
