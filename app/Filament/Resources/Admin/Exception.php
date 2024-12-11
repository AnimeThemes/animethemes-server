<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Enums\Auth\Role;
use App\Filament\Actions\Base\ViewAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Resources\Admin\Exception\Pages\ListExceptions;
use App\Filament\Resources\Admin\Exception\Pages\ViewException;
use App\Models\Auth\User;
use BezhanSalleh\FilamentExceptions\Models\Exception as ExceptionModel;
use BezhanSalleh\FilamentExceptions\Resources\ExceptionResource;
use Filament\Facades\Filament;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;

/**
 * Class Exception.
 */
class Exception extends ExceptionResource
{
    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.admin');
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function table(Table $table): Table
    {
        return $table
            ->query(ExceptionModel::query())
            ->columns([
                TextColumn::make('method')
                    ->label(__('filament-exceptions::filament-exceptions.columns.method'))
                    ->badge()
                    ->colors([
                        'gray',
                        'success' => fn ($state): bool => $state === 'GET',
                        'primary' => fn ($state): bool => $state === 'POST',
                        'warning' => fn ($state): bool => $state === 'PUT' || $state === 'PATCH',
                        'danger' => fn ($state): bool => $state === 'DELETE',
                        'gray' => fn ($state): bool => $state === 'OPTIONS',
                    ])
                    ->searchable(),

                TextColumn::make('path')
                    ->label(__('filament-exceptions::filament-exceptions.columns.path'))
                    ->searchable(),

                TextColumn::make('type')
                    ->label(__('filament-exceptions::filament-exceptions.columns.type'))
                    ->searchable(),

                TextColumn::make('code')
                    ->label(__('filament-exceptions::filament-exceptions.columns.code'))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label(__('filament-exceptions::filament-exceptions.columns.occurred_at'))
                    ->searchable()
                    ->dateTime(),
            ])
            ->actions([
                ViewAction::make('view')
                    ->url(fn (ExceptionModel $record): string => ExceptionResource::getUrl('view', ['record' => $record]))
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Determine if the user can access the table.
     *
     * @return bool
     */
    public static function canViewAny(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();
        return $user->hasRole(Role::ADMIN->value);
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListExceptions::route('/'),
            'view' => ViewException::route('/{record}')
        ];
    }
}
