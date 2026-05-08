<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Enums\Auth\Role;
use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\Admin\ActivityStatus;
use App\Filament\Actions\Base\ViewAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\Admin\Activity\Pages\ManageActivities;
use App\Filament\Resources\Auth\UserResource;
use App\Filament\Resources\BaseResource;
use App\Models\Admin\Activity;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Activity::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.activity');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.activities');
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
        return Activity::ATTRIBUTE_NAME;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TODO: JSON values are not being displayed
                KeyValue::make(Activity::ATTRIBUTE_PROPERTIES)
                    ->label(__('filament.fields.activity.fields.name'))
                    ->keyLabel(__('filament.fields.activity.fields.keys'))
                    ->valueLabel(__('filament.fields.activity.fields.values'))
                    ->columnSpanFull()
                    ->hidden(fn ($state): bool => is_null($state))
                    ->formatStateUsing(fn (?array $state) => collect($state)->mapWithKeys(function ($value, $key): array {
                        if (is_array($value)) {
                            $value = json_encode($value);
                        }

                        return [$key => blank($value) ? '-' : $value];
                    })->toArray()),

                Textarea::make(Activity::ATTRIBUTE_EXCEPTION)
                    ->label(__('filament.fields.activity.exception'))
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->recordUrl('')
            ->columns([
                TextColumn::make(Activity::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Activity::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.activity.name'))
                    ->searchable(),

                BelongsToColumn::make(Activity::RELATION_USER, UserResource::class),

                TextColumn::make(Activity::RELATION_RELATED)
                    ->label(__('filament.fields.activity.target'))
                    ->formatStateUsing(fn ($state): string => Str::headline(class_basename($state)).': '.$state->getName()),

                TextColumn::make(Activity::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.activity.status'))
                    ->formatStateUsing(fn (ActivityStatus $state): ?string => $state->localize())
                    ->badge(),

                TextColumn::make(BaseModel::ATTRIBUTE_CREATED_AT)
                    ->label(__('filament.fields.activity.happened_at'))
                    ->dateTime(),

                TextColumn::make(Activity::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.activity.finished_at'))
                    ->dateTime(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make(Activity::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.activity.name'))
                    ->formatStateUsing(fn ($state): string => ucfirst((string) $state)),

                BelongsToEntry::make(Activity::RELATION_USER, UserResource::class),

                TextEntry::make(Activity::RELATION_RELATED)
                    ->label(__('filament.fields.activity.target'))
                    ->formatStateUsing(fn ($state): string => class_basename($state).': '.$state->getName()),

                TextEntry::make(Activity::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.activity.status'))
                    ->formatStateUsing(fn (ActivityStatus $state): ?string => $state->localize())
                    ->badge(),

                TextEntry::make(BaseModel::ATTRIBUTE_CREATED_AT)
                    ->label(__('filament.fields.activity.happened_at'))
                    ->dateTime(),

                TextEntry::make(Activity::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.activity.finished_at'))
                    ->dateTime(),

                KeyValueEntry::make(Activity::ATTRIBUTE_PROPERTIES)
                    ->label(__('filament.fields.activity.fields.name'))
                    ->keyLabel(__('filament.fields.activity.fields.keys'))
                    ->valueLabel(__('filament.fields.activity.fields.values'))
                    ->columnSpanFull()
                    ->hidden(fn ($state): bool => is_null($state))
                    ->formatStateUsing(fn (?array $state) => collect($state)->mapWithKeys(function ($value, $key): array {
                        if (is_array($value)) {
                            $value = json_encode($value);
                        }

                        return [$key => blank($value) ? '-' : $value];
                    })->toArray()),

                TextEntry::make(Activity::ATTRIBUTE_EXCEPTION)
                    ->label(__('filament.fields.activity.exception'))
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
            SelectFilter::make(Activity::ATTRIBUTE_STATUS)
                ->label(__('filament.fields.activity.status'))
                ->options(ActivityStatus::class),
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
            'index' => ManageActivities::route('/'),
        ];
    }
}
