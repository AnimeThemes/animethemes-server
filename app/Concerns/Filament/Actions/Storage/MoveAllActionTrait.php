<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Storage;

use App\Actions\Storage\Base\MoveAction;
use App\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Actions\Storage\Wiki\Video\MoveVideoAction;
use App\Actions\Storage\Wiki\Video\Script\MoveScriptAction;
use App\Constants\Config\AudioConstants;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use App\Rules\Storage\StorageFileDirectoryExistsRule;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Trait MoveAllActionTrait
 */
trait MoveAllActionTrait
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.moveAll'));
        $this->icon('heroicon-o-arrow-long-right');

        $this->authorize('create', [Audio::class, Video::class, VideoScript::class]);

        $this->action(fn (array $data) => $this->handle($data));
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     */
    public function getForm(Form $form): ?Form
    {
        $videoPath = $this->videoDefaultPath();
        $audioPath = $this->audioDefaultPath();
        $scriptPath = $this->scriptDefaultPath();

        $videoFs = Storage::disk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        $audioFs = Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
        $scriptFs = Storage::disk(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        return $form
            ->schema([
                TextInput::make('video')
                    ->label(__('filament.resources.singularLabel.video'))
                    ->helperText(__('filament.actions.storage.move.fields.path.help'))
                    ->required()
                    ->rules(['required', 'string', 'doesnt_start_with:/', "ends_with:.webm", new StorageFileDirectoryExistsRule($videoFs)])
                    ->default($videoPath),

                TextInput::make('audio')
                    ->label(__('filament.resources.singularLabel.audio'))
                    ->helperText(__('filament.actions.storage.move.fields.path.help'))
                    ->hidden($audioPath === null)
                    ->required($audioPath !== null)
                    ->rules(['string', 'doesnt_start_with:/', "ends_with:.ogg", new StorageFileDirectoryExistsRule($audioFs)])
                    ->default($audioPath),

                TextInput::make('script')
                    ->label(__('filament.resources.singularLabel.video_script'))
                    ->helperText(__('filament.actions.storage.move.fields.path.help'))
                    ->hidden($scriptPath === null)
                    ->required($scriptPath !== null)
                    ->rules(['string', 'doesnt_start_with:/', "ends_with:.txt", new StorageFileDirectoryExistsRule($scriptFs)])
                    ->default($scriptPath),
            ]);
    }

    /**
     * Handle the action.
     *
     * @param  array  $fields
     * @return void
     */
    public function handle(array $fields): void
    {
        $videoPath = Arr::get($fields, 'video');
        $audioPath = Arr::get($fields, 'audio');
        $scriptPath = Arr::get($fields, 'script');

        if (is_string($videoPath)) {
            $action = new MoveVideoAction($this->getVideo(), $videoPath);

            $this->resolveAction($action);
        }

        if (is_string($audioPath) && ($audio = $this->getVideo()->audio)) {
            $action = new MoveAudioAction($audio, $audioPath);

            $this->resolveAction($action);
        }

        if (is_string($scriptPath) && ($script = $this->getVideo()->videoscript)) {
            $action = new MoveScriptAction($script, $scriptPath);

            $this->resolveAction($action);
        }
    }

    /**
     * Resolve an action.
     *
     * @param  MoveAction  $action
     * @return void
     */
    protected function resolveAction(MoveAction $action): void
    {
        $storageResults = $action->handle();

        $storageResults->toLog();

        $action->then($storageResults);

        $actionResult = $storageResults->toActionResult();

        if ($actionResult->hasFailed()) {
            $this->failedLog($actionResult->getMessage());
        }
    }

    /**
     * Get the video.
     *
     * @return Video|null
     */
    protected function getVideo(): ?Video
    {
        $record = $this->getRecord();

        return $record instanceof Video
            ? $record
            : null;
    }

    /**
     * Resolve the default value for the path field of the video.
     *
     * @return string|null
     */
    protected function videoDefaultPath(): ?string
    {
        $video = $this->getVideo();

        return $video instanceof Video
            ? $video->path
            : null;
    }

    /**
     * Resolve the default value for the path field of the audio.
     *
     * @return string|null
     */
    protected function audioDefaultPath(): ?string
    {
        $video = $this->getVideo();

        $audio = $video->audio;

        return $audio instanceof Audio
            ? $audio->path
            : null;
    }

    /**
     * Resolve the default value for the path field of the script.
     *
     * @return string|null
     */
    protected function scriptDefaultPath(): ?string
    {
        $video = $this->getVideo();

        $script = $video->videoscript;

        return $script instanceof VideoScript
            ? $script->path
            : null;
    }
}
