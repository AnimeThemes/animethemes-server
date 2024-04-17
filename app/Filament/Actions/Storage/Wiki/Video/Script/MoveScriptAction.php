<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\MoveScriptAction as MoveScript;
use App\Constants\Config\VideoConstants;
use App\Models\Wiki\Video\VideoScript;
use App\Filament\Actions\Storage\Base\MoveAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * Class MoveScriptAction.
 */
class MoveScriptAction extends MoveAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  Model  $script
     * @param  array  $fields
     * @return MoveScript
     */
    protected function storageAction(Model $script, array $fields): MoveScript
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
