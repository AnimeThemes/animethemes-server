<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use Filament\Schemas\Schema;
use App\Actions\Storage\Base\PruneAction as BasePruneAction;
use App\Filament\Actions\Storage\StorageAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PruneAction.
 */
abstract class PruneAction extends StorageAction
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
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getSchema(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hours')
                    ->label(__('filament.actions.storage.prune.fields.hours.name'))
                    ->helperText(__('filament.actions.storage.prune.fields.hours.help'))
                    ->numeric(),
            ]);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Model|null  $model
     * @param  array  $fields
     * @return BasePruneAction
     */
    abstract protected function storageAction(?Model $model, array $fields): BasePruneAction;
}
