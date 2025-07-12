<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\ModelConstants;
use App\Contracts\Models\SoftDeletable;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\DetachAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\ReplicateAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Actions\Base\ViewAction;
use App\Filament\BulkActions\Base\DeleteBulkAction;
use App\Filament\BulkActions\Base\ForceDeleteBulkAction;
use App\Filament\BulkActions\Base\RestoreBulkAction;
use App\Filament\Components\Filters\DateFilter;
use App\Filament\RelationManagers\Base\ActionLogRelationManager;
use App\Models\BaseModel;
use App\Scopes\WithoutInsertSongScope;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Panel;
use Filament\Resources\Resource;
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
            ->defaultSort(fn (Table $table) => $table->hasSearch() ? null : static::getRecordRouteKeyName(), fn (Table $table) => $table->hasSearch() ? null : 'desc')
            ->filters(static::getFilters())
            ->filtersFormMaxHeight('400px')
            ->recordActions(static::getActions())
            ->toolbarActions(static::getBulkActions())
            ->headerActions(static::getTableActions())
            ->recordUrl(fn (Model $record): string => static::getUrl('view', ['record' => $record]))
            ->paginated([10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25)
            ->extremePaginationLinks()
            ->deferLoading(! app()->runningUnitTests());
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Filament\Tables\Filters\BaseFilter>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return [
            TrashedFilter::make()
                ->visible(in_array(SoftDeletable::class, class_implements(static::getModel()))),

            DateFilter::make(BaseModel::ATTRIBUTE_CREATED_AT)
                ->label(__('filament.fields.base.created_at')),

            DateFilter::make(BaseModel::ATTRIBUTE_UPDATED_AT)
                ->label(__('filament.fields.base.updated_at')),

            DateFilter::make(ModelConstants::ATTRIBUTE_DELETED_AT)
                ->label(__('filament.fields.base.deleted_at'))
                ->visible(in_array(SoftDeletable::class, class_implements(static::getModel()))),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, Action|ActionGroup>
     */
    public static function getActions(): array
    {
        $exists = collect(static::getRecordActions())->contains(fn (Action $action) => $action->isVisible());

        return [
            ViewAction::make(),

            EditAction::make(),

            ActionGroup::make(
                array_merge(
                    [
                        DetachAction::make(),

                        DeleteAction::make(),

                        ForceDeleteAction::make(),

                        RestoreAction::make(),

                        ReplicateAction::make(),
                    ],
                    $exists
                    ? [ActionGroup::make(static::getRecordActions())->dropdown(false)]
                    : [],
                )
            ),
        ];
    }

    /**
     * Get the record actions exclusive to the resource.
     *
     * @return array<int, Action>
     */
    public static function getRecordActions(): array
    {
        return [];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array<int, ActionGroup|Action>|null  $actionsIncludedInGroup
     * @return array<int, ActionGroup|Action>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
                ForceDeleteBulkAction::make(),
                RestoreBulkAction::make(),

                ...$actionsIncludedInGroup,
            ]),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array<int, ActionGroup|Action>
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
                WithoutInsertSongScope::class,
            ]);
    }

    /**
     * Get the base relationships available for all resources.
     *
     * @return array<int, \Filament\Resources\RelationManagers\RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
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
    public static function getSlug(?Panel $panel = null): string
    {
        return 'resources/'.static::getRecordSlug();
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    abstract public static function getRecordSlug(): string;
}
