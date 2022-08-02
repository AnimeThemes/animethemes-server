<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki;

use App\Contracts\Repositories\RepositoryInterface;
use App\Nova\Actions\ReconcileAction;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class ReconcileStorageAction.
 */
abstract class ReconcileStorageAction extends ReconcileAction
{
    /**
     * Get the name of the disk that represents the filesystem.
     *
     * @return string
     */
    abstract protected function disk(): string;

    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  ActionFields  $fields
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @return void
     */
    protected function handleFilters(
        ActionFields $fields,
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository
    ): void {
        $path = $fields->get('path');
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

        $directories = collect($fs->allDirectories())
            ->filter(fn (string $directory) => ! is_numeric($directory))
            ->sortDesc();

        $directories = $directories->combine($directories);

        return [
            Select::make(__('nova.path'), 'path')
                ->required()
                ->rules('required')
                ->help(__('nova.reconcile_video_path_help'))
                ->options($directories),
        ];
    }
}
