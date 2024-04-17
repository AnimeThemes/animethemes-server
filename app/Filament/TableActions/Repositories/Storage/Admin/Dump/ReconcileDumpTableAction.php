<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Repositories\Storage\Admin\Dump;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Constants\Config\DumpConstants;
use App\Filament\TableActions\Repositories\Storage\ReconcileStorageTableAction;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Config;

/**
 * Class ReconcileDumpTableAction.
 */
class ReconcileDumpTableAction extends ReconcileStorageTableAction
{
    use ReconcilesDumpRepositories;

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getForm(Form $form): Form
    {
        return $form;
    }

    /**
     * The name of the disk.
     *
     * @return string
     */
    public function disk(): string
    {
        return Config::get(DumpConstants::DISK_QUALIFIED);
    }
}
