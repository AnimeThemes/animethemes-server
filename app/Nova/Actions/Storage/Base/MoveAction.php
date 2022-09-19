<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Base;

use App\Actions\Storage\Base\MoveAction as BaseMoveAction;
use App\Contracts\Storage\InteractsWithDisk;
use App\Nova\Actions\Storage\StorageAction;
use App\Rules\Storage\StorageFileDirectoryExistsRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class MoveAction.
 */
abstract class MoveAction extends StorageAction implements InteractsWithDisk
{
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
        $defaultPath = $this->defaultPath($request);

        $fs = Storage::disk(Config::get($this->disk()));

        return [
            Text::make(__('nova.actions.storage.move.fields.path.name'), 'path')
                ->required()
                ->rules(['required', 'string', 'doesnt_start_with:/', "ends_with:{$this->allowedFileExtension()}", new StorageFileDirectoryExistsRule($fs)])
                ->default(fn () => $defaultPath)
                ->help(__('nova.actions.storage.move.fields.path.help')),
        ];
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return BaseMoveAction
     */
    abstract protected function action(ActionFields $fields, Collection $models): BaseMoveAction;

    /**
     * Resolve the default value for the path field.
     *
     * @param  NovaRequest  $request
     * @return string|null
     */
    abstract protected function defaultPath(NovaRequest $request): ?string;

    /**
     * The file extension that the path must end with.
     *
     * @return string
     */
    abstract protected function allowedFileExtension(): string;
}
