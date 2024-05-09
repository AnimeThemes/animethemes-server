<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage\Base;

use App\Actions\Storage\Base\PruneAction as BasePruneAction;
use App\Filament\TableActions\Storage\StorageTableAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PruneTableAction.
 */
abstract class PruneTableAction extends StorageTableAction
{
    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('hours')
                    ->label(__('filament.actions.storage.prune.fields.hours.name'))
                    ->helperText(__('filament.actions.storage.prune.fields.hours.help'))
                    ->numeric(),
            ]);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array  $fields
     * @return BasePruneAction
     */
    abstract protected function storageAction(array $fields): BasePruneAction;
}
