<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Storage\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\MoveScriptAction as MoveScript;
use App\Constants\Config\VideoConstants;
use App\Models\BaseModel;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

/**
 * Trait MoveScriptActionTrait.
 */
trait MoveScriptActionTrait
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.video_script.move.name'));

        $this->authorize('create', VideoScript::class);
    }

    /**
     * Get the underlying storage action.
     *
     * @param  VideoScript  $script
     * @param  array  $fields
     * @return MoveScript
     */
    protected function storageAction(BaseModel $script, array $fields): MoveScript
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
