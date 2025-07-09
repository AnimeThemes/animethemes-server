<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\MoveScriptAction as MoveScript;
use App\Constants\Config\VideoConstants;
use App\Filament\Actions\Storage\Base\MoveAction;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Class MoveScriptAction.
 */
class MoveScriptAction extends MoveAction
{
    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'move-script';
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video_script.move.name'));

        $this->visible(Auth::user()->can('create', VideoScript::class));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  VideoScript  $script
     * @param  array  $fields
     * @return MoveScript
     */
    protected function storageAction(?Model $script, array $fields): MoveScript
    {
        /** @var string $path */
        $path = Arr::get($fields, 'path');

        return new MoveScript($script, $path);
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
     * Resolve the default value for the path field.
     *
     * @return string|null
     */
    protected function defaultPath(): ?string
    {
        $script = $this->getRecord();

        return $script instanceof VideoScript
            ? $script->path
            : null;
    }

    /**
     * The file extension that the path must end with.
     *
     * @return string
     */
    protected function allowedFileExtension(): string
    {
        return '.txt';
    }
}
