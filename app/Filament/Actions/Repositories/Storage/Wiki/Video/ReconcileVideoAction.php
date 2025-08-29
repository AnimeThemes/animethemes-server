<?php

declare(strict_types=1);

namespace App\Filament\Actions\Repositories\Storage\Wiki\Video;

use App\Concerns\Repositories\Wiki\ReconcilesVideoRepositories;
use App\Constants\Config\VideoConstants;
use App\Filament\Actions\Repositories\Storage\ReconcileStorageAction;
use App\Models\Wiki\Video;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class ReconcileVideoAction extends ReconcileStorageAction
{
    use ReconcilesVideoRepositories;

    public static function getDefaultName(): ?string
    {
        return 'reconcile-video';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.videos')]));

        $this->visible(Gate::allows('create', Video::class));
    }

    public function disk(): string
    {
        return Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED);
    }
}
