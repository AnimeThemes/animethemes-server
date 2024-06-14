<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\MoveAudioAction as MoveAudio;
use App\Constants\Config\AudioConstants;
use App\Models\Wiki\Audio;
use App\Filament\Actions\Storage\Base\MoveAction;
use App\Models\BaseModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * Class MoveAudioAction.
 */
class MoveAudioAction extends MoveAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.audio.move.name'));

        $this->authorize('create', Audio::class);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Audio  $audio
     * @param  array  $fields
     * @return MoveAudio
     */
    protected function storageAction(BaseModel $audio, array $fields): MoveAudio
    {
        /** @var string $path */
        $path = Arr::get($fields, 'path');

        return new MoveAudio($audio, $path);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }

    /**
     * Resolve the default value for the path field.
     *
     * @return string|null
     */
    protected function defaultPath(): ?string
    {
        $audio = $this->getRecord();

        return $audio instanceof Audio
            ? $audio->path
            : null;
    }

    /**
     * The file extension that the path must end with.
     *
     * @return string
     */
    protected function allowedFileExtension(): string
    {
        return '.ogg';
    }
}
