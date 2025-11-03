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
use Filament\QueryBuilder\Constraints\Constraint;
use Filament\QueryBuilder\Constraints\DateConstraint;
use Filament\Resources\Resource;
use Filament\Support\Enums\Width;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;

abstract class BaseResource extends Resource
{
    public static function canGloballySearch(): bool
    {
        return false;
    }

    public static function getRecordRouteKeyName(): string
    {
        return (new static::$model)->getKeyName();
    }

    public static function getRecordTitleAttribute(): string
    {
        return (new static::$model)->getKeyName();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort(fn (Table $table): ?string => $table->hasSearch() ? null : static::getRecordRouteKeyName(), fn (Table $table): ?string => $table->hasSearch() ? null : 'desc')
            ->filters(static::getFilters())
            ->filtersLayout(FiltersLayout::Modal)
            ->filtersFormWidth(Width::FourExtraLarge)
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
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            TrashedFilter::make()
                ->visible(in_array(SoftDeletable::class, class_implements(static::getModel()))),
        ];
    }

    /**
     * @return Constraint[]
     */
    public static function getConstraints(): array
    {
        return [
            DateConstraint::make(BaseModel::ATTRIBUTE_CREATED_AT)
                ->label(__('filament.fields.base.created_at')),

            DateConstraint::make(BaseModel::ATTRIBUTE_UPDATED_AT)
                ->label(__('filament.fields.base.updated_at')),

            // ...[in_array(SoftDeletable::class, class_implements(static::getModel())) ? [
            //     DateConstraint::make(ModelConstants::ATTRIBUTE_DELETED_AT)
            //         ->label(__('filament.fields.base.deleted_at')),
            // ] : []],
        ];
    }

    /**
     * @return array<int, Action|ActionGroup>
     */
    public static function getActions(): array
    {
        return [
            ...(Gate::allows('viewAny', static::$model) ? [ViewAction::make()] : []),

            ...(Gate::allows('updateAny', static::$model) ? [EditAction::make()] : []),

            ActionGroup::make([
                DetachAction::make(),

                ...(Gate::allows('deleteAny', static::$model) ? [DeleteAction::make()] : []),

                ...(Gate::allows('forceDeleteAny', static::$model) ? [ForceDeleteAction::make()] : []),

                ...(Gate::allows('restoreAny', static::$model) ? [RestoreAction::make()] : []),

                ...(Gate::allows('create', static::$model) ? [ReplicateAction::make()] : []),

                ActionGroup::make(static::getRecordActions())->dropdown(false),
            ]),
        ];
    }

    /**
     * @return Action[]
     */
    public static function getRecordActions(): array
    {
        return [];
    }

    /**
     * @param  array<int, ActionGroup|Action>|null  $actionsIncludedInGroup
     * @return array<int, ActionGroup|Action>
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            BulkActionGroup::make([
                ...(Gate::allows('deleteAny', static::$model) ? [DeleteBulkAction::make()] : []),

                ...(Gate::allows('forceDeleteAny', static::$model) ? [ForceDeleteBulkAction::make()] : []),

                ...(Gate::allows('restoreAny', static::$model) ? [RestoreBulkAction::make()] : []),

                ...$actionsIncludedInGroup,
            ]),
        ];
    }

    /**
     * @return array<int, ActionGroup|Action>
     */
    public static function getTableActions(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
                WithoutInsertSongScope::class,
            ]);
    }

    /**
     * @return array<int, \Filament\Resources\RelationManagers\RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getBaseRelations(): array
    {
        return [
            ActionLogRelationManager::class,
        ];
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'resources/'.static::getRecordSlug();
    }

    abstract public static function getRecordSlug(): string;
}
