<?php

declare(strict_types=1);

namespace App\Nova\Actions\Repositories\Storage;

use App\Contracts\Repositories\RepositoryInterface;
use App\Contracts\Storage\InteractsWithDisk;
use App\Nova\Actions\Repositories\ReconcileAction;
use App\Rules\Storage\StorageDirectoryExistsRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class ReconcileStorageAction.
 */
abstract class ReconcileStorageAction extends ReconcileAction implements InteractsWithDisk
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
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(NovaRequest $request): array
    {
        $fs = Storage::disk($this->disk());

        return [
            Text::make(__('nova.actions.repositories.storage.fields.path.name'), 'path')
                ->required()
                ->rules(['required', 'string', 'doesnt_start_with:/', new StorageDirectoryExistsRule($fs)])
                ->help(__('nova.actions.repositories.storage.fields.path.help')),
        ];
    }
}
