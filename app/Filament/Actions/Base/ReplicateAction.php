<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use App\Models\Admin\ActionLog;
use Filament\Actions\ReplicateAction as BaseReplicateAction;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

/**
 * Class ReplicateAction.
 */
class ReplicateAction extends BaseReplicateAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (string $model) => Gate::allows('create', $model));

        $this->schema(fn (Schema $schema, BaseListResources|BaseManageResources|BaseRelationManager $livewire) => $livewire->form($schema)->getComponents());

        $this->successRedirectUrl(fn (Model $replica) => Filament::getModelResource($replica)::getUrl('view', ['record' => $replica]));

        $this->after(fn (Model $replica) => ActionLog::modelCreated($replica));
    }
}
