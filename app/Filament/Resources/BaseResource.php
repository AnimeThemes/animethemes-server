<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\DetachAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Actions\Base\ViewAction;
use App\Filament\BulkActions\Base\DeleteBulkAction;
use App\Filament\BulkActions\Base\ForceDeleteBulkAction;
use App\Filament\BulkActions\Base\RestoreBulkAction;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\RelationManagers\Base\ActionLogRelationManager;
use App\Models\BaseModel;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

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
     * Get the route key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): string
    {
        /** @var Model $model */
        $model = static::getModel();

        return (new $model)->getKeyName();
    }

    /**
     * Get the title attribute for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): string
    {
        return (new static::$model)->getKeyName();
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
            ->defaultSort(static::getRecordRouteKeyName(), 'desc')
            ->filters(static::getFilters())
            ->filtersFormMaxHeight('400px')
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions())
            ->headerActions(static::getTableActions())
            ->recordUrl(fn (Model $record): string => static::getUrl('view', ['record' => $record]))
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
            TrashedFilter::make(),

            DateRangeFilter::make(BaseModel::ATTRIBUTE_CREATED_AT)
                ->label(__('filament.fields.base.created_at')),

            DateRangeFilter::make(BaseModel::ATTRIBUTE_UPDATED_AT)
                ->label(__('filament.fields.base.updated_at')),

            DateRangeFilter::make(BaseModel::ATTRIBUTE_DELETED_AT)
                ->label(__('filament.fields.base.deleted_at')),
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
            ViewAction::make(),

            EditAction::make(),

            ActionGroup::make([
                DetachAction::make(),

                DeleteAction::make(),

                ForceDeleteAction::make(),
            ])
                ->icon(__('filament-icons.actions.base.group_delete'))
                ->color('danger'),

            RestoreAction::make(),
        ];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            BulkActionGroup::make(
                array_merge(
                    [
                        DeleteBulkAction::make(),

                        ForceDeleteBulkAction::make(),

                        RestoreBulkAction::make(),
                    ],
                    $actionsIncludedInGroup,
                )
            ),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getTableActions(): array
    {
        return [];
    }

    /**
     * Get the eloquent query for the resource.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Get the base relationships available for all resources.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBaseRelations(): array
    {
        return [
            ActionLogRelationManager::class,
        ];
    }

    /**
     * Get the generic slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getSlug(): string
    {
        return 'resources/' . static::getRecordSlug();
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    abstract public static function getRecordSlug(): string;
}
