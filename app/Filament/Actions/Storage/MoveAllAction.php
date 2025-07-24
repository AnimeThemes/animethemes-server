<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage;

use App\Actions\Storage\Base\MoveAction;
use App\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Actions\Storage\Wiki\Video\MoveVideoAction;
use App\Actions\Storage\Wiki\Video\Script\MoveScriptAction;
use App\Constants\Config\AudioConstants;
use App\Constants\Config\VideoConstants;
use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\TextInput;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
use App\Rules\Storage\StorageFileDirectoryExistsRule;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MoveAllAction extends BaseAction
{
    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'move-all';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.move_all'));
        $this->icon(__('filament-icons.actions.base.move_all'));

        $this->visible(Gate::any('create', [Audio::class, Video::class, VideoScript::class]));

        $this->action(fn (array $data) => $this->handle($data));
    }

    /**
     * Get the schema available on the action.
     */
    public function getSchema(Schema $schema): ?Schema
    {
        $videoPath = $this->videoDefaultPath();
        $audioPath = $this->audioDefaultPath();
        $scriptPath = $this->scriptDefaultPath();

        $videoFs = Storage::disk(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));
        $audioFs = Storage::disk(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));
        $scriptFs = Storage::disk(Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED));

        return $schema
            ->components([
                TextInput::make('video')
                    ->label(__('filament.resources.singularLabel.video'))
                    ->helperText(__('filament.actions.storage.move.fields.path.help'))
                    ->required()
                    ->doesntStartWith('/')
                    ->endsWith('.webm')
                    ->rule(new StorageFileDirectoryExistsRule($videoFs))
                    ->default($videoPath),

                TextInput::make('audio')
                    ->label(__('filament.resources.singularLabel.audio'))
                    ->helperText(__('filament.actions.storage.move.fields.path.help'))
                    ->hidden($audioPath === null)
                    ->required($audioPath !== null)
                    ->doesntStartWith('/')
                    ->endsWith('.ogg')
                    ->rule(new StorageFileDirectoryExistsRule($audioFs))
                    ->default($audioPath),

                TextInput::make('script')
                    ->label(__('filament.resources.singularLabel.video_script'))
                    ->helperText(__('filament.actions.storage.move.fields.path.help'))
                    ->hidden($scriptPath === null)
                    ->required($scriptPath !== null)
                    ->doesntStartWith('/')
                    ->endsWith('.txt')
                    ->rule(new StorageFileDirectoryExistsRule($scriptFs))
                    ->default($scriptPath),
            ]);
    }

    /**
     * Handle the action.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): void
    {
        $videoPath = Arr::get($data, 'video');
        $audioPath = Arr::get($data, 'audio');
        $scriptPath = Arr::get($data, 'script');

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
