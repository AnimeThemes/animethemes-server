<?php

declare(strict_types=1);

namespace App\Nova\Actions\Repositories\Storage\Admin\Dump;

use App\Concerns\Repositories\Admin\ReconcilesDumpRepositories;
use App\Constants\Config\DumpConstants;
use App\Nova\Actions\Repositories\Storage\ReconcileStorageAction;
use Illuminate\Support\Facades\Config;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class ReconcileDumpAction.
 */
class ReconcileDumpAction extends ReconcileStorageAction
{
    use ReconcilesDumpRepositories;

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.repositories.name', ['label' => __('nova.resources.label.dumps')]);
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
