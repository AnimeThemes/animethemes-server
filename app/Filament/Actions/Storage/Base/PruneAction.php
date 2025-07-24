<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Base;

use App\Actions\Storage\Base\PruneAction as BasePruneAction;
use App\Filament\Actions\Storage\StorageAction;
use App\Filament\Components\Fields\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

abstract class PruneAction extends StorageAction
{
    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(__('filament-icons.table_actions.base.prune'));
    }

    /**
     * Get the schema available on the action.
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
     * @param  Model|null  $record
     * @param  array<string, mixed>  $data
     * @return BasePruneAction
     */
    abstract protected function storageAction(?Model $record, array $data): BasePruneAction;
}
