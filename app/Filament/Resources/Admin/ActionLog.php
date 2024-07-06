<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Enums\Actions\ActionLogStatus;
use App\Enums\Auth\Role;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Admin\ActionLog\Pages\ListActionLogs;
use App\Filament\Resources\Admin\ActionLog\Pages\ViewActionLog;
use App\Filament\Resources\Auth\User;
use App\Models\Admin\ActionLog as ActionLogModel;
use App\Models\Auth\User as UserModel;
use App\Models\BaseModel;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

/**
 * Class ActionLog.
 */
class ActionLog extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = ActionLogModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.action_log');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralLabel(): string
    {
        return __('filament.resources.label.action_logs');
    }

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
     * The icon displayed to the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament.resources.icon.action_logs');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordSlug(): string
    {
        return 'action-logs';
    }

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make(ActionLogModel::ATTRIBUTE_EXCEPTION)
                    ->label(__('filament.fields.action_log.exception'))
                    ->disabled()
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
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
        return parent::table($table)
            ->columns([
                TextColumn::make(ActionLogModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                TextColumn::make(ActionLogModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.action_log.name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make(ActionLogModel::ATTRIBUTE_USER)
                    ->label(__('filament.resources.singularLabel.user'))
                    ->urlToRelated(User::class, ActionLogModel::RELATION_USER, true)
                    ->sortable(),

                TextColumn::make(ActionLogModel::ATTRIBUTE_TARGET)
                    ->label(__('filament.fields.action_log.target'))
                    ->formatStateUsing(fn ($state) => class_basename($state) . ': ' . $state->getName())
                    ->sortable(),

                TextColumn::make(ActionLogModel::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.action_log.status'))
                    ->formatStateUsing(fn (ActionLogStatus $state) => $state->localize())
                    ->color(fn (ActionLogStatus $state) => $state->color())
                    ->badge()
                    ->sortable(),

                TextColumn::make(BaseModel::ATTRIBUTE_CREATED_AT)
                    ->label(__('filament.fields.action_log.happened_at'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make(ActionLogModel::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.action_log.finished_at'))
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
            ]);
    }

    /**
     * Get the infolist available for the resource.
     *
     * @param  Infolist  $infolist
     * @return Infolist
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make(static::getRecordTitle($infolist->getRecord()))
                    ->schema([
                        TextEntry::make(ActionLogModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(ActionLogModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.action_log.name'))
                            ->formatStateUsing(fn ($state) => ucfirst($state)),

                        TextEntry::make(ActionLogModel::ATTRIBUTE_USER)
                            ->label(__('filament.resources.singularLabel.user'))
                            ->urlToRelated(User::class, ActionLogModel::RELATION_USER, true),

                        TextEntry::make(ActionLogModel::ATTRIBUTE_TARGET)
                            ->label(__('filament.fields.action_log.target'))
                            ->formatStateUsing(fn ($state) => class_basename($state) . ': ' . $state->getName()),

                        TextEntry::make(ActionLogModel::ATTRIBUTE_STATUS)
                            ->label(__('filament.fields.action_log.status'))
                            ->formatStateUsing(fn (ActionLogStatus $state) => $state->localize())
                            ->color(fn (ActionLogStatus $state) => $state->color())
                            ->badge(),

                        TextEntry::make(BaseModel::ATTRIBUTE_CREATED_AT)
                            ->label(__('filament.fields.action_log.happened_at'))
                            ->dateTime(),

                        TextEntry::make(ActionLogModel::ATTRIBUTE_FINISHED_AT)
                            ->label(__('filament.fields.action_log.finished_at'))
                            ->dateTime()
                            ->placeholder('-'),

                        TextEntry::make(ActionLogModel::ATTRIBUTE_EXCEPTION)
                            ->label(__('filament.fields.action_log.exception'))
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
            ]);
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(
                static::getLabel(),
                [],
            ),
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
            SelectFilter::make(ActionLogModel::ATTRIBUTE_STATUS)
                ->label(__('filament.fields.action_log.status'))
                ->options(ActionLogStatus::asSelectArray()),

            DateRangeFilter::make(BaseModel::ATTRIBUTE_CREATED_AT)
                ->label(__('filament.fields.action_log.happened_at')),

            DateRangeFilter::make(ActionLogModel::ATTRIBUTE_FINISHED_AT)
                ->label(__('filament.fields.action_log.finished_at')),
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
        return array_merge(
            parent::getActions(),
            [],
        );
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
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the header actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }

    /**
     * Determine whether the related model can be created.
     *
     * @return bool
     */
    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * Determine whether the related model can be edited.
     *
     * @param  Model  $record
     * @return bool
     */
    public static function canEdit(Model $record): bool
    {
        return false;
    }

    /**
     * Determine whether the related model can be deleted.
     *
     * @param  Model  $record
     * @return bool
     */
    public static function canDelete(Model $record): bool
    {
        return false;
    }

    /**
     * Determine whether the related model can be force-deleted.
     *
     * @param  Model  $record
     * @return bool
     */
    public static function canForceDelete(Model $record): bool
    {
        return false;
    }

    /**
     * Determine if the user can access the table.
     *
     * @return bool
     */
    public static function canAccess(): bool
    {
        /** @var UserModel */
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
            'index' => ListActionLogs::route('/'),
            'view' => ViewActionLog::route('/{record:id}'),
        ];
    }
}
