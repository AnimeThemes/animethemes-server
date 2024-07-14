<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\UploadScriptAction as UploadScript;
use App\Constants\Config\VideoConstants;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Video\Script\Pages\ListScripts;
use App\Filament\TableActions\Storage\Base\UploadTableAction;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rules\File as FileRule;

/**
 * Class UploadScriptTableAction.
 */
class UploadScriptTableAction extends UploadTableAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video_script.upload.name'));

        $this->authorize('create', VideoScript::class);
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     */
    public function getForm(Form $form): Form
    {
        $model = $this->getRecord();

        return $form
            ->schema(
                array_merge(
                    parent::getForm($form)->getComponents(),
                    [
                        Hidden::make(Video::ATTRIBUTE_ID)
                            ->label(__('filament.resources.singularLabel.video'))
                            ->default(fn (BaseRelationManager|ListScripts $livewire) => $livewire instanceof BaseRelationManager ? $livewire->getOwnerRecord()->getKey() : null),
                    ],
                )
            );
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array  $fields
     * @return UploadScript
     */
    protected function storageAction(array $fields): UploadScript
    {
        /** @var UploadedFile $file */
        $file = Arr::get($fields, 'file');

        /** @var Video|null $video */
        $video = Video::query()->find(Arr::get($fields, Video::ATTRIBUTE_ID));

        $path = explode($video->filename, $video->path())[0];

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
