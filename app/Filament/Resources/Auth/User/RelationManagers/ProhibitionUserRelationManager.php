<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\RelationManagers;

use App\Filament\Actions\Models\Auth\User\GiveProhibitionAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\RelationManagers\Auth\ProhibitionRelationManager;
use App\Models\Auth\Prohibition;
use App\Models\Auth\User;
use Filament\Tables\Table;

class ProhibitionUserRelationManager extends ProhibitionRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = User::RELATION_PROHIBITIONS;

    public function getPivotComponents(): array
    {
        return [
            GiveProhibitionAction::getExpiresAtField(),

            GiveProhibitionAction::getReasonField()
                ->disabled(),
        ];
    }

    public function getPivotColumns(): array
    {
        return [
            TextColumn::make('expires_at')
                ->date('M j, Y H:i:s')
                ->sortable(),

            TextColumn::make('reason')
                ->sortable(),
        ];
    }

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Prohibition::RELATION_USERS)
        );
    }

    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [];
    }
}
