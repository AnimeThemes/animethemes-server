<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Models\Admin\ActionLog;
use Filament\Actions\ReplicateAction as BaseReplicateAction;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

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

        $this->schema(fn (Schema $schema, $livewire) => $livewire->form($schema)->getComponents());

        $this->successRedirectUrl(fn (Model $replica) => Filament::getModelResource($replica)::getUrl('view', ['record' => $replica]));

        $this->after(fn (Model $replica) => ActionLog::modelCreated($replica));
    }
}
