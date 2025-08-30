<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Enums\Auth\Role;
use App\Enums\Models\Admin\ActionLogStatus;
use App\Filament\Actions\Base\ViewAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Filters\DateFilter;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\Admin\ActionLog\Pages\ManageActionLogs;
use App\Filament\Resources\Auth\User;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\ActionLog as ActionLogModel;
use App\Models\Auth\User as UserModel;
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

class ActionLog extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ActionLogModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.action_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.action_logs');
    }

    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.admin');
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
        return ActionLogModel::ATTRIBUTE_NAME;
    }

    /**
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            ActionLogModel::RELATION_USER,
            ActionLogModel::RELATION_TARGET,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TODO: JSON values are not being displayed
                KeyValue::make(ActionLogModel::ATTRIBUTE_FIELDS)
                    ->label(__('filament.fields.action_log.fields.name'))
                    ->keyLabel(__('filament.fields.action_log.fields.keys'))
                    ->valueLabel(__('filament.fields.action_log.fields.values'))
                    ->columnSpanFull()
                    ->hidden(fn ($state) => is_null($state))
                    ->formatStateUsing(function (?array $state) {
                        return collect($state)->mapWithKeys(function ($value, $key) {
                            if (is_array($value)) {
                                $value = json_encode($value);
                            }

                            return [$key => blank($value) ? '-' : $value];
                        })->toArray();
                    }),

                Textarea::make(ActionLogModel::ATTRIBUTE_EXCEPTION)
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
                TextColumn::make(ActionLogModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ActionLogModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.action_log.name'))
                    ->searchable(),

                BelongsToColumn::make(ActionLogModel::RELATION_USER, User::class),

                TextColumn::make(ActionLogModel::ATTRIBUTE_TARGET)
                    ->label(__('filament.fields.action_log.target'))
                    ->formatStateUsing(fn ($state) => Str::headline(class_basename($state)).': '.$state->getName()),

                TextColumn::make(ActionLogModel::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.action_log.status'))
                    ->formatStateUsing(fn (ActionLogStatus $state) => $state->localize())
                    ->badge(),

                TextColumn::make(BaseModel::ATTRIBUTE_CREATED_AT)
                    ->label(__('filament.fields.action_log.happened_at'))
                    ->dateTime(),

                TextColumn::make(ActionLogModel::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.action_log.finished_at'))
                    ->dateTime(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make(ActionLogModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.action_log.name'))
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                BelongsToEntry::make(ActionLogModel::RELATION_USER, User::class),

                TextEntry::make(ActionLogModel::ATTRIBUTE_TARGET)
                    ->label(__('filament.fields.action_log.target'))
                    ->formatStateUsing(fn ($state) => class_basename($state).': '.$state->getName()),

                TextEntry::make(ActionLogModel::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.action_log.status'))
                    ->formatStateUsing(fn (ActionLogStatus $state) => $state->localize())
                    ->badge(),

                TextEntry::make(BaseModel::ATTRIBUTE_CREATED_AT)
                    ->label(__('filament.fields.action_log.happened_at'))
                    ->dateTime(),

                TextEntry::make(ActionLogModel::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.action_log.finished_at'))
                    ->dateTime(),

                KeyValueEntry::make(ActionLogModel::ATTRIBUTE_FIELDS)
                    ->label(__('filament.fields.action_log.fields.name'))
                    ->keyLabel(__('filament.fields.action_log.fields.keys'))
                    ->valueLabel(__('filament.fields.action_log.fields.values'))
                    ->columnSpanFull()
                    ->hidden(fn ($state) => is_null($state))
                    ->formatStateUsing(function (?array $state) {
                        return collect($state)->mapWithKeys(function ($value, $key) {
                            if (is_array($value)) {
                                $value = json_encode($value);
                            }

                            return [$key => blank($value) ? '-' : $value];
                        })->toArray();
                    }),

                TextEntry::make(ActionLogModel::ATTRIBUTE_EXCEPTION)
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
            SelectFilter::make(ActionLogModel::ATTRIBUTE_STATUS)
                ->label(__('filament.fields.action_log.status'))
                ->options(ActionLogStatus::class),

            DateFilter::make(BaseModel::ATTRIBUTE_CREATED_AT)
                ->label(__('filament.fields.action_log.happened_at')),

            DateFilter::make(ActionLogModel::ATTRIBUTE_FINISHED_AT)
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
        /** @var UserModel $user */
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
