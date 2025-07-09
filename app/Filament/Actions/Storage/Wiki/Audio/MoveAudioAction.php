<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Audio;

use App\Actions\Storage\Wiki\Audio\MoveAudioAction as MoveAudio;
use App\Constants\Config\AudioConstants;
use App\Filament\Actions\Storage\Base\MoveAction;
use App\Models\Wiki\Audio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Class MoveAudioAction.
 */
class MoveAudioAction extends MoveAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'move-audio';
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.audio.move.name'));

        $this->visible(Auth::user()->can('create', Audio::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Audio  $audio
     * @param  array  $fields
     * @return MoveAudio
     */
    protected function storageAction(?Model $audio, array $fields): MoveAudio
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
