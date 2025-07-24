<?php

declare(strict_types=1);

namespace App\Filament\Actions\Repositories\Storage\Admin\Dump;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Constants\Config\DumpConstants;
use App\Filament\Actions\Repositories\Storage\ReconcileStorageAction;
use App\Models\Admin\Dump;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;

class ReconcileDumpAction extends ReconcileStorageAction
{
    use ReconcilesDumpRepositories;

    /**
     * The default name of the action.
     */
    public static function getDefaultName(): ?string
    {
        return 'reconcile-dump';
    }

    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.dumps')]));

        $this->visible(Gate::allows('create', Dump::class));
    }

    /**
     * Get the schema available on the action.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getSchema(Schema $schema): Schema
    {
        return $schema;
    }

    /**
     * The name of the disk.
     */
    public function disk(): string
    {
        return Config::get(DumpConstants::DISK_QUALIFIED);
    }
}
