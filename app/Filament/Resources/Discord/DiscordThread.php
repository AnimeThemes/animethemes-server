<?php

declare(strict_types=1);

namespace App\Filament\Resources\Discord;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use App\Actions\Discord\DiscordThreadAction;
use App\Filament\Actions\Models\Discord\DiscordEditMessageAction;
use App\Filament\Actions\Models\Discord\DiscordSendMessageAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Filters\DateFilter;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Discord\DiscordThread\Pages\ListDiscordThreads;
use App\Filament\Resources\Discord\DiscordThread\Pages\ViewDiscordThread;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Models\BaseModel;
use App\Models\Discord\DiscordThread as DiscordThreadModel;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Class DiscordThread.
 */
class DiscordThread extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = DiscordThreadModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.discord_thread');
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
        return __('filament.resources.label.discord_threads');
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
        return __('filament.resources.group.discord');
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
        return __('filament-icons.resources.discord_thread');
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
        return DiscordThreadModel::ATTRIBUTE_NAME;
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'discord-thread';
    }

    /**
     * Get the eloquent query for the resource.
     *
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([DiscordThreadModel::RELATION_ANIME]);
    }

    /**
     * The form to the actions.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(DiscordThreadModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.discord_thread.id.name'))
                    ->helperText(__('filament.fields.discord_thread.id.help'))
                    ->disabledOn(['edit'])
                    ->formatStateUsing(fn ($state) => strval($state))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, string $state) => $set(DiscordThreadModel::ATTRIBUTE_NAME, Arr::get(new DiscordThreadAction()->get($state), 'thread.name'))),

                TextInput::make(DiscordThreadModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.discord_thread.name.name'))
                    ->helperText(__('filament.fields.discord_thread.name.help'))
                    ->required(),

                BelongsTo::make(DiscordThreadModel::ATTRIBUTE_ANIME)
                    ->resource(AnimeResource::class)
                    ->required(),
            ]);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(DiscordThreadModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.discord_thread.id.name')),

                TextColumn::make(DiscordThreadModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.discord_thread.name.name'))
                    ->copyableWithMessage()
                    ->searchable(),

                BelongsToColumn::make(DiscordThreadModel::RELATION_ANIME, AnimeResource::class),
            ])
            ->defaultSort(BaseModel::CREATED_AT, 'desc');
    }

    /**
     * Get the infolist available for the resource.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(DiscordThreadModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.discord_thread.id.name')),

                        TextEntry::make(DiscordThreadModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.discord_thread.name.name')),

                        BelongsToEntry::make(DiscordThreadModel::RELATION_ANIME, AnimeResource::class),
                    ])
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
            RelationGroup::make(static::getLabel(), [
                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public static function getFilters(): array
    {
        return [
            DateFilter::make(BaseModel::ATTRIBUTE_CREATED_AT)
                ->label(__('filament.fields.base.created_at')),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),
        ];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return [
            ...parent::getTableActions(),

            ActionGroup::make([
                DiscordEditMessageAction::make('edit-message'),

                DiscordSendMessageAction::make('send-message'),
            ]),
        ];
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
            'index' => ListDiscordThreads::route('/'),
            'view' => ViewDiscordThread::route('/{record:thread_id}'),
        ];
    }
}
