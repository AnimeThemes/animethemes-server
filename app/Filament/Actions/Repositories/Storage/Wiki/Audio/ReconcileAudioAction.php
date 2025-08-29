<?php

declare(strict_types=1);

namespace App\Filament\Actions\Repositories\Storage\Wiki\Audio;

use App\Concerns\Repositories\Wiki\ReconcilesAudioRepositories;
use App\Constants\Config\AudioConstants;
use App\Filament\Actions\Repositories\Storage\ReconcileStorageAction;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class ReconcileAudioAction extends ReconcileStorageAction
{
    use ReconcilesAudioRepositories;

    public static function getDefaultName(): ?string
    {
        return 'reconcile-audio';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.audios')]));

        $this->visible(Gate::allows('create', Audio::class));
    }

    /**
     * The name of the disk.
     */
    public function disk(): string
    {
        return Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED);
    }
}
