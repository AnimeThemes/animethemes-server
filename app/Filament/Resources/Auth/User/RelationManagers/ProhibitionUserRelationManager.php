<?php

declare(strict_types=1);

namespace App\Filament\Resources\Auth\User\RelationManagers;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Auth\ProhibitionRelationManager;
use App\Models\Auth\Prohibition;
use App\Models\Auth\User;
use Filament\Forms\Components\DateTimePicker;
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
            DateTimePicker::make('expires_at')
                ->label(__('filament.actions.user.give_prohibition.expires_at.name'))
                ->helperText(__('filament.actions.user.give_prohibition.expires_at.help'))
                ->nullable(),

            TextInput::make('reason')
                ->label(__('filament.actions.user.give_prohibition.reason.name'))
                ->helperText(__('filament.actions.user.give_prohibition.reason.help'))
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
