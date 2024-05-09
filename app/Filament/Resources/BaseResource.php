<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Models\BaseModel;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Class BaseResource.
 */
abstract class BaseResource extends Resource
{
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
            ->recordUrl(fn (Model $record): string => static::getUrl('edit', ['record' => $record]))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25);
    }

    /**
     * The panel for timestamp fields.
     *
     * @return array
     */
    public static function timestamps(): array
    {
        return [
            TextEntry::make(BaseModel::ATTRIBUTE_CREATED_AT)
                ->label(__('filament.fields.base.created_at'))
                ->dateTime(),

            TextEntry::make(BaseModel::ATTRIBUTE_UPDATED_AT)
                ->label(__('filament.fields.base.updated_at'))
                ->dateTime(),

            TextEntry::make(BaseModel::ATTRIBUTE_DELETED_AT)
                ->label(__('filament.fields.base.deleted_at'))
                ->dateTime(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return [
            TrashedFilter::make()
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return [
            ViewAction::make()
                ->label(__('filament.actions.base.view')),

            EditAction::make()
                ->label(__('filament.actions.base.edit')),

            DeleteAction::make()
                ->label(__('filament.actions.base.delete')),

            ForceDeleteAction::make()
                ->label(__('filament.actions.base.forcedelete')),

            RestoreAction::make()
                ->label(__('filament.actions.base.restore')),
        ];
    }

    /**
     * Get the bulk actions available for the resource.
     * 
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->label(__('filament.bulk_actions.base.delete')),

                ForceDeleteBulkAction::make()
                    ->label(__('filament.bulk_actions.base.forcedelete')),

                RestoreBulkAction::make()
                    ->label(__('filament.bulk_actions.base.restore')),
            ]),
        ];
    }

    /**
     * Get the eloquent query for the resource.
     * 
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Get the default slug (URI key) for the resources.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected static function getDefaultSlug(): string
    {
        return 'resources/';
    }
}