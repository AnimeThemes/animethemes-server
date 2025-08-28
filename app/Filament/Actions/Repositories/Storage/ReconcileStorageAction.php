<?php

declare(strict_types=1);

namespace App\Filament\Actions\Repositories\Storage;

use App\Contracts\Repositories\RepositoryInterface;
use App\Contracts\Storage\InteractsWithDisk;
use App\Filament\Actions\Repositories\ReconcileAction;
use App\Filament\Components\Fields\TextInput;
use App\Rules\Storage\StorageDirectoryExistsRule;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

abstract class ReconcileStorageAction extends ReconcileAction implements InteractsWithDisk
{
    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(__('filament-icons.table_actions.base.reconcile'));
    }

    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleFilters(
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository,
        array $data = []
    ): void {
        parent::handleFilters($sourceRepository, $destinationRepository, $data);

        $path = Arr::get($data, 'path');
        if ($path !== null) {
            $sourceRepository->handleFilter('path', $path);
            $destinationRepository->handleFilter('path', $path);
        }
    }

    /**
     * Get the schema available on the action.
     */
    public function getSchema(Schema $schema): Schema
    {
        $fs = Storage::disk($this->disk());

        return $schema
            ->components([
                TextInput::make('path')
                    ->label(__('filament.actions.repositories.storage.fields.path.name'))
                    ->helperText(__('filament.actions.repositories.storage.fields.path.help'))
                    ->required()
                    ->doesntStartWith('/')
                    ->rule(new StorageDirectoryExistsRule($fs)),
            ]);
    }
}
