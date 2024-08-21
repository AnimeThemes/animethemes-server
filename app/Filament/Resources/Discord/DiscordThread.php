<?php

declare(strict_types=1);

namespace App\Filament\Resources\Discord;

use App\Actions\Discord\DiscordThreadAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Discord\DiscordThread\Pages\CreateDiscordThread;
use App\Filament\Resources\Discord\DiscordThread\Pages\EditDiscordThread;
use App\Filament\Resources\Discord\DiscordThread\Pages\ListDiscordThreads;
use App\Filament\Resources\Discord\DiscordThread\Pages\ViewDiscordThread;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Filament\TableActions\Models\Discord\DiscordEditMessageTableAction;
use App\Filament\TableActions\Models\Discord\DiscordSendMessageTableAction;
use App\Models\BaseModel;
use App\Models\Discord\DiscordThread as DiscordThreadModel;
use App\Models\Wiki\Anime;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Support\Arr;

/**
 * Class DiscordThread.
 */
class DiscordThread extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
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
        return __('filament.resources.icon.discord_thread');
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordSlug(): string
    {
        return 'discord-thread';
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
                TextInput::make(DiscordThreadModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.discord_thread.id.name'))
                    ->helperText(__('filament.fields.discord_thread.id.help'))
                    ->disabledOn(['edit'])
                    ->formatStateUsing(fn ($state) => strval($state))
                    ->required()
                    ->rules(['required'])
                    ->live()
                    ->afterStateUpdated(fn (Set $set, string $state) => $set(DiscordThreadModel::ATTRIBUTE_NAME, Arr::get((new DiscordThreadAction())->get($state), 'thread.name'))),

                TextInput::make(DiscordThreadModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.discord_thread.name.name'))
                    ->helperText(__('filament.fields.discord_thread.name.help'))
                    ->required()
                    ->rules(['required', 'string']),

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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(DiscordThreadModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.discord_thread.id.name'))
                    ->sortable(),

                TextColumn::make(DiscordThreadModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.discord_thread.name.name'))
                    ->sortable()
                    ->copyableWithMessage()
                    ->searchable()
                    ->toggleable(),

                BelongsToColumn::make(DiscordThreadModel::RELATION_ANIME.'.'.Anime::ATTRIBUTE_NAME)
                    ->resource(AnimeResource::class)
                    ->toggleable(),
            ])
            ->defaultSort(BaseModel::CREATED_AT, 'desc');
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
                        TextEntry::make(DiscordThreadModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.discord_thread.id.name')),

                        TextEntry::make(DiscordThreadModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.discord_thread.name.name')),

                        TextEntry::make(DiscordThreadModel::RELATION_ANIME.'.'.Anime::ATTRIBUTE_NAME)
                            ->label(__('filament.resources.singularLabel.anime'))
                            ->urlToRelated(AnimeResource::class, DiscordThreadModel::RELATION_ANIME),
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
            RelationGroup::make(static::getLabel(),
                array_merge(
                    [],
                    parent::getBaseRelations(),
                )
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
        return array_merge(
            [],
            parent::getFilters(),
        );
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
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
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
            [
                ActionGroup::make([
                    DiscordEditMessageTableAction::make('edit-message'),

                    DiscordSendMessageTableAction::make('send-message'),
                ]),
            ],
        );
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
            'create' => CreateDiscordThread::route('/create'),
            'view' => ViewDiscordThread::route('/{record:thread_id}'),
            'edit' => EditDiscordThread::route('/{record:thread_id}/edit'),
        ];
    }
}