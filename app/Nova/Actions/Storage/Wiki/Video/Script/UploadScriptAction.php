<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\UploadScriptAction as UploadScript;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video;
use App\Nova\Actions\Storage\Base\UploadAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rules\File as FileRule;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class UploadScriptAction.
 */
class UploadScriptAction extends UploadAction
{
    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.video_script.upload.name');
    }

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        $model = $request->findModelQuery()->first();

        return array_merge(
            parent::fields($request),
            [
                Hidden::make(__('nova.resources.singularLabel.video'), Video::ATTRIBUTE_ID)
                    ->default(fn () => $model instanceof Video ? $model->getKey() : null),
            ],
        );
    }

    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return UploadScript
     */
    protected function action(ActionFields $fields, Collection $models): UploadScript
    {
        /** @var UploadedFile $file */
        $file = $fields->get('file');
        $path = $fields->get('path');

        /** @var Video|null $video */
        $video = Video::query()->find($fields->get(Video::ATTRIBUTE_ID));

        return new UploadScript($file, $path, $video);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED);
    }

    /**
     * Get the file validation rules.
     *
     * @return array
     */
    protected function fileRules(): array
    {
        return [
            'required',
            FileRule::types('txt')->max(2 * 1024),
        ];
    }
}
