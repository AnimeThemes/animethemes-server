<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasActionLogs;
use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use App\Filament\Resources\Base\BaseViewResource;
use App\Models\Admin\ActionLog;
use Filament\Actions\EditAction as BaseEditAction;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class EditAction.
 */
class EditAction extends BaseEditAction
{
    use HasActionLogs;
    use HasPivotActionLogs;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(function ($livewire) {
            if ($livewire instanceof BaseListResources || $livewire instanceof BaseRelationManager) {
                return '';
            }

            return null;
        });

        $this->iconSize(IconSize::Medium);

        $this->schema(fn (Schema $schema, BaseRelationManager|BaseManageResources|BaseListResources|BaseViewResource $livewire) => [
            ...$livewire->form($schema)->getComponents(),
            ...($livewire instanceof BaseRelationManager ? $livewire->getPivotComponents() : []),
        ]);

        $this->after(function ($livewire, Model $record, EditAction $action) {
            if ($livewire instanceof BaseListResources || $livewire instanceof BaseViewResource) {
                ActionLog::modelUpdated($record);
            }

            if ($livewire instanceof BaseRelationManager) {
                if ($livewire->getRelationship() instanceof BelongsToMany) {
                    $this->pivotActionLog('Update Attached', $livewire, $record, $action);
                }
            }
        });
    }
}
