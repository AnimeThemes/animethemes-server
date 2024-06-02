<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Components\Filters\DateFilter;
use App\Models\BaseModel;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
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
     * Determine if the resource can globally search.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function canGloballySearch(): bool
    {
        return false;
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
                ->dateTime()
                ->placeholder('-'),
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
            TrashedFilter::make(),

            DateFilter::make(BaseModel::ATTRIBUTE_CREATED_AT)
                ->attribute(BaseModel::ATTRIBUTE_CREATED_AT)
                ->labels(__('filament.filters.base.created_at_from'),  __('filament.filters.base.created_at_to')),

            DateFilter::make(BaseModel::ATTRIBUTE_UPDATED_AT)
                ->attribute(BaseModel::ATTRIBUTE_UPDATED_AT)
                ->labels(__('filament.filters.base.updated_at_from'),  __('filament.filters.base.updated_at_to')),

            DateFilter::make(BaseModel::ATTRIBUTE_DELETED_AT)
                ->attribute(BaseModel::ATTRIBUTE_DELETED_AT)
                ->labels(__('filament.filters.base.deleted_at_from'),  __('filament.filters.base.deleted_at_to')),
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

            ActionGroup::make([
                DeleteAction::make()
                    ->label(__('filament.actions.base.delete')),

                ForceDeleteAction::make()
                    ->label(__('filament.actions.base.forcedelete'))
                    ->visible(true),
            ])
                ->icon('heroicon-o-trash')
                ->color('danger'),

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
                    ->label(__('filament.bulk_actions.base.delete'))
                    ->authorize('delete', (new static::$model)),

                ForceDeleteBulkAction::make()
                    ->label(__('filament.bulk_actions.base.forcedelete'))
                    ->hidden(false)
                    ->authorize('forcedelete', (new static::$model)),

                RestoreBulkAction::make()
                    ->label(__('filament.bulk_actions.base.restore'))
                    ->authorize('restore', (new static::$model)),
            ]),
        ];
    }

    /**
     * Get the eloquent query for the resource.
     * 
     * @return Builder
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
