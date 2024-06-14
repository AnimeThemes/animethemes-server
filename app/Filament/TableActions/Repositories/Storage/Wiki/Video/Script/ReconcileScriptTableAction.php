<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Repositories\Storage\Wiki\Video\Script;

use App\Concerns\Repositories\Wiki\Video\ReconcilesScriptRepositories;
use App\Constants\Config\VideoConstants;
use App\Filament\TableActions\Repositories\Storage\ReconcileStorageTableAction;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileScriptTableAction.
 */
class ReconcileScriptTableAction extends ReconcileStorageTableAction
{
    use ReconcilesScriptRepositories;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.video_scripts')]));

        $this->authorize('create', VideoScript::class);
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
}
