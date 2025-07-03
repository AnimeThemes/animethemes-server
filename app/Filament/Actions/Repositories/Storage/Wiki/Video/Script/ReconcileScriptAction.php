<?php

declare(strict_types=1);

namespace App\Filament\Actions\Repositories\Storage\Wiki\Video\Script;

use App\Concerns\Repositories\Wiki\Video\ReconcilesScriptRepositories;
use App\Constants\Config\VideoConstants;
use App\Filament\Actions\Repositories\Storage\ReconcileStorageAction;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileScriptAction.
 */
class ReconcileScriptAction extends ReconcileStorageAction
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

        $this->name('reconcile-script');

        $this->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.video_scripts')]));

        $this->visible(Auth::user()->can('create', VideoScript::class));
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
