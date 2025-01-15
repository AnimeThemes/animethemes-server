<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage\Base;

use App\Actions\Storage\Base\PruneAction as BasePruneAction;
use App\Filament\TableActions\Storage\StorageTableAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;

/**
 * Class PruneTableAction.
 */
abstract class PruneTableAction extends StorageTableAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(__('filament-icons.table_actions.base.prune'));
    }

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
