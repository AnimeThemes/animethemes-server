<?php

declare(strict_types=1);

namespace App\Filament\Actions\Repositories\Storage\Wiki\Audio;

use App\Concerns\Repositories\Wiki\ReconcilesAudioRepositories;
use App\Constants\Config\AudioConstants;
use App\Filament\Actions\Repositories\Storage\ReconcileStorageAction;
use App\Models\Wiki\Audio;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

/**
 * Class ReconcileAudioAction.
 */
class ReconcileAudioAction extends ReconcileStorageAction
{
    use ReconcilesAudioRepositories;

    /**
     * The default name of the action.
     *
     * @return string|null
     */
    public static function getDefaultName(): ?string
    {
        return 'reconcile-audio';
    }

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.audios')]));

        $this->visible(Gate::allows('create', Audio::class));
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
}
