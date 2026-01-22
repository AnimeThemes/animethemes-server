<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\RelationManagers;

use App\Filament\Actions\Models\Auth\User\GiveProhibitionAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\RelationManagers\Auth\SanctionRelationManager;
use App\Models\Auth\Sanction;
use App\Models\Auth\User;
use Filament\Tables\Table;

class SanctionUserRelationManager extends SanctionRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = User::RELATION_SANCTIONS;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Sanction::RELATION_USERS)
        );
    }

    public function getPivotComponents(): array
    {
        return [
            GiveProhibitionAction::getExpiresAtField()
                ->helperText(__('filament.actions.user.give_sanction.expires_at.help')),

            GiveProhibitionAction::getReasonField()
                ->helperText(__('filament.actions.user.give_sanction.reason.help'))
                ->disabled(),
        ];
    }

    public function getPivotColumns(): array
    {
        return [
            TextColumn::make('expires_at')
                ->label(__('filament.actions.user.give_sanction.expires_at.name'))
                ->date('M j, Y H:i:s')
                ->sortable(),

            TextColumn::make('reason')
                ->label(__('filament.actions.user.give_sanction.reason.name'))
                ->sortable(),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action>
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),
        ];
    }

    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [];
    }
}
