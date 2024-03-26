<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Base;

use App\Actions\Storage\Base\UploadAction as BaseUploadAction;
use App\Contracts\Storage\InteractsWithDisk;
use App\Nova\Actions\Storage\StorageAction;
use App\Rules\Storage\StorageDirectoryExistsRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class UploadAction.
 */
abstract class UploadAction extends StorageAction implements InteractsWithDisk
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
        $fs = Storage::disk($this->disk());

        return [
            File::make(__('nova.actions.storage.upload.fields.file.name'), 'file')
                ->required()
                ->rules($this->fileRules())
                ->help(__('nova.actions.storage.upload.fields.file.help')),

            Text::make(__('nova.actions.storage.upload.fields.path.name'), 'path')
                ->rules(fn ($request) => [
                    'doesnt_start_with:/',
                    'doesnt_end_with:/',
                    empty($request->input('path')) ? '' : 'string',
                    empty($request->input('path')) ? '' : new StorageDirectoryExistsRule($fs),
                ])
                ->help(__('nova.actions.storage.upload.fields.path.help')),
        ];
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return BaseUploadAction
     */
    abstract protected function action(ActionFields $fields, Collection $models): BaseUploadAction;

    /**
     * Get the file validation rules.
     *
     * @return array
     */
    abstract protected function fileRules(): array;
}
