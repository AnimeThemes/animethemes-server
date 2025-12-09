<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources;

use App\Contracts\Models\SoftDeletable;
use App\Filament\Actions\Base\ViewAction;
use App\Models\BaseModel;
use App\Scopes\WithoutInsertSongScope;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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

abstract class BaseSubmissionResource extends Resource
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
            ->recordUrl(fn (Model $record): string => static::getUrl('create', ['record' => $record]))
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
        ];
    }

    /**
     * @return array<int, Action|ActionGroup>
     */
    public static function getActions(): array
    {
        return [
            ...(Gate::allows('viewAny', static::$model) ? [ViewAction::make()] : []),
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
        return [];
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
        return [];
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'submissions/'.static::getRecordSlug();
    }

    abstract public static function getRecordSlug(): string;
}
