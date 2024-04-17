<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Repositories\Storage;

use App\Contracts\Repositories\RepositoryInterface;
use App\Contracts\Storage\InteractsWithDisk;
use App\Filament\TableActions\Repositories\ReconcileTableAction;
use App\Rules\Storage\StorageDirectoryExistsRule;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * Class ReconcileStorageTableAction.
 */
abstract class ReconcileStorageTableAction extends ReconcileTableAction implements InteractsWithDisk
{
    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @param  array  $data
     * @return void
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
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getForm(Form $form): Form
    {
        $fs = Storage::disk($this->disk());

        return $form
            ->schema([
                TextInput::make(__('filament.actions.repositories.storage.fields.path.name'), 'path')
                    ->required()
                    ->rules(['required', 'string', 'doesnt_start_with:/', new StorageDirectoryExistsRule($fs)])
                    ->helperText(__('filament.actions.repositories.storage.fields.path.help')),
            ]);
    }
}
