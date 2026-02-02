<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Enums\Auth\Role;
use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\Admin\ActionLogStatus;
use App\Filament\Actions\Base\ViewAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Filters\DateFilter;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\Admin\ActionLog\Pages\ManageActionLogs;
use App\Filament\Resources\Auth\UserResource;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\ActionLog;
use App\Models\Auth\User;
use App\Models\BaseModel;
use Filament\Facades\Filament;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActionLogResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ActionLog::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.action_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.action_logs');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::ADMIN;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedRectangleStack;
    }

    public static function getRecordSlug(): string
    {
        return 'action-logs';
    }

    public static function getRecordTitleAttribute(): string
    {
        return ActionLog::ATTRIBUTE_NAME;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            ActionLog::RELATION_USER,
            ActionLog::RELATION_TARGET,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TODO: JSON values are not being displayed
                KeyValue::make(ActionLog::ATTRIBUTE_FIELDS)
                    ->label(__('filament.fields.action_log.fields.name'))
                    ->keyLabel(__('filament.fields.action_log.fields.keys'))
                    ->valueLabel(__('filament.fields.action_log.fields.values'))
                    ->columnSpanFull()
                    ->hidden(fn ($state): bool => is_null($state))
                    ->formatStateUsing(fn (?array $state) => collect($state)->mapWithKeys(function ($value, $key): array {
                        if (is_array($value)) {
                            $value = json_encode($value);
                        }

                        return [$key => blank($value) ? '-' : $value];
                    })->toArray()),

                Textarea::make(ActionLog::ATTRIBUTE_EXCEPTION)
                    ->label(__('filament.fields.action_log.exception'))
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl('')
            ->columns([
                TextColumn::make(ActionLog::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ActionLog::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.action_log.name'))
                    ->searchable(),

                BelongsToColumn::make(ActionLog::RELATION_USER, UserResource::class),

                TextColumn::make(ActionLog::ATTRIBUTE_TARGET)
                    ->label(__('filament.fields.action_log.target'))
                    ->formatStateUsing(fn ($state): string => Str::headline(class_basename($state)).': '.$state->getName()),

                TextColumn::make(ActionLog::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.action_log.status'))
                    ->formatStateUsing(fn (ActionLogStatus $state): ?string => $state->localize())
                    ->badge(),

                TextColumn::make(BaseModel::ATTRIBUTE_CREATED_AT)
                    ->label(__('filament.fields.action_log.happened_at'))
                    ->dateTime(),

                TextColumn::make(ActionLog::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.action_log.finished_at'))
                    ->dateTime(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make(ActionLog::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.action_log.name'))
                    ->formatStateUsing(fn ($state): string => ucfirst((string) $state)),

                BelongsToEntry::make(ActionLog::RELATION_USER, UserResource::class),

                TextEntry::make(ActionLog::ATTRIBUTE_TARGET)
                    ->label(__('filament.fields.action_log.target'))
                    ->formatStateUsing(fn ($state): string => class_basename($state).': '.$state->getName()),

                TextEntry::make(ActionLog::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.action_log.status'))
                    ->formatStateUsing(fn (ActionLogStatus $state): ?string => $state->localize())
                    ->badge(),

                TextEntry::make(BaseModel::ATTRIBUTE_CREATED_AT)
                    ->label(__('filament.fields.action_log.happened_at'))
                    ->dateTime(),

                TextEntry::make(ActionLog::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.action_log.finished_at'))
                    ->dateTime(),

                KeyValueEntry::make(ActionLog::ATTRIBUTE_FIELDS)
                    ->label(__('filament.fields.action_log.fields.name'))
                    ->keyLabel(__('filament.fields.action_log.fields.keys'))
                    ->valueLabel(__('filament.fields.action_log.fields.values'))
                    ->columnSpanFull()
                    ->hidden(fn ($state): bool => is_null($state))
                    ->formatStateUsing(fn (?array $state) => collect($state)->mapWithKeys(function ($value, $key): array {
                        if (is_array($value)) {
                            $value = json_encode($value);
                        }

                        return [$key => blank($value) ? '-' : $value];
                    })->toArray()),

                TextEntry::make(ActionLog::ATTRIBUTE_EXCEPTION)
                    ->label(__('filament.fields.action_log.exception'))
                    ->columnSpanFull()
                    ->size(TextSize::Large),
            ])
            ->columns(3);
    }

    /**
     * @return array<int, \Filament\Tables\Filters\BaseFilter>
     */
    public static function getFilters(): array
    {
        return [
            SelectFilter::make(ActionLog::ATTRIBUTE_STATUS)
                ->label(__('filament.fields.action_log.status'))
                ->options(ActionLogStatus::class),

            DateFilter::make(BaseModel::ATTRIBUTE_CREATED_AT)
                ->label(__('filament.fields.action_log.happened_at')),

            DateFilter::make(ActionLog::ATTRIBUTE_FINISHED_AT)
                ->label(__('filament.fields.action_log.finished_at')),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    /**
     * @param  array<int, \Filament\Actions\ActionGroup|\Filament\Actions\Action>|null  $actionsIncludedInGroup
     * @return array<int, \Filament\Actions\ActionGroup|\Filament\Actions\Action>
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [];
    }

    /**
     * @return array<int, \Filament\Actions\ActionGroup|\Filament\Actions\Action>
     */
    public static function getTableActions(): array
    {
        return [];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canForceDelete(Model $record): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Filament::auth()->user();

        return $user->hasRole(Role::ADMIN->value);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ManageActionLogs::route('/'),
        ];
    }
}
