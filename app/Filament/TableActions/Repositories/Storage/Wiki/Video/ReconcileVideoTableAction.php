<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Repositories\Storage\Wiki\Video;

use App\Concerns\Repositories\Wiki\ReconcilesVideoRepositories;
use App\Constants\Config\VideoConstants;
use App\Filament\TableActions\Repositories\Storage\ReconcileStorageTableAction;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileVideoTableAction.
 */
class ReconcileVideoTableAction extends ReconcileStorageTableAction
{
    use ReconcilesVideoRepositories;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.videos')]));

        $this->authorize('create', Video::class);
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }
}
