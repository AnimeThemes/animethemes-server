<?php

declare(strict_types=1);

namespace App\Filament\Actions\Repositories\Storage\Wiki\Video\Script;

use App\Concerns\Repositories\Wiki\Video\ReconcilesScriptRepositories;
use App\Constants\Config\VideoConstants;
use App\Filament\Actions\Repositories\Storage\ReconcileStorageAction;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class ReconcileScriptAction extends ReconcileStorageAction
{
    use ReconcilesScriptRepositories;

    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'reconcile-script';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.video_scripts')]));

        $this->visible(Gate::allows('create', VideoScript::class));
    }

    /**
     * The name of the disk.
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::SCRIPT_DISK_QUALIFIED);
    }
}
