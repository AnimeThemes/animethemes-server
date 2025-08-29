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

    public static function getDefaultName(): ?string
    {
        return 'reconcile-dump';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.repositories.name', ['label' => __('filament.resources.label.dumps')]));

        $this->visible(Gate::allows('create', Dump::class));
    }

    public function getSchema(Schema $schema): Schema
    {
        return $schema;
    }

    public function disk(): string
    {
        return Config::get(DumpConstants::DISK_QUALIFIED);
    }
}
