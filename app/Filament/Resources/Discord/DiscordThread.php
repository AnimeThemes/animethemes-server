<?php

declare(strict_types=1);

namespace App\Filament\Resources\Discord;

use App\Actions\Discord\DiscordThreadAction;
use App\Enums\Filament\NavigationGroup;
use App\Filament\Actions\Models\Discord\DiscordEditMessageAction;
use App\Filament\Actions\Models\Discord\DiscordSendMessageAction;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Filters\DateFilter;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Discord\DiscordThread\Pages\ListDiscordThreads;
use App\Filament\Resources\Discord\DiscordThread\Pages\ViewDiscordThread;
use App\Filament\Resources\Wiki\Anime as AnimeResource;
use App\Models\BaseModel;
use App\Models\Discord\DiscordThread as DiscordThreadModel;
use Filament\Actions\ActionGroup;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class DiscordThread extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = DiscordThreadModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.discord_thread');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.discord_threads');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::DISCORD;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedChatBubbleLeftRight;
    }

    public static function getRecordTitleAttribute(): string
    {
        return DiscordThreadModel::ATTRIBUTE_NAME;
    }

    public static function getRecordSlug(): string
    {
        return 'discord-thread';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([DiscordThreadModel::RELATION_ANIME]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(DiscordThreadModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.discord_thread.id.name'))
                    ->helperText(__('filament.fields.discord_thread.id.help'))
                    ->disabledOn(['edit'])
                    ->formatStateUsing(fn ($state): string => strval($state))
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, string $state): mixed => $set(DiscordThreadModel::ATTRIBUTE_NAME, Arr::get(new DiscordThreadAction()->get($state), 'thread.name'))),

                TextInput::make(DiscordThreadModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.discord_thread.name.name'))
                    ->helperText(__('filament.fields.discord_thread.name.help'))
                    ->required(),

                BelongsTo::make(DiscordThreadModel::ATTRIBUTE_ANIME)
                    ->resource(AnimeResource::class)
                    ->required(),
            ]);
    }

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
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [
            DateFilter::make(BaseModel::ATTRIBUTE_CREATED_AT)
                ->label(__('filament.fields.base.created_at')),
        ];
    }

    /**
     * @return array<int, ActionGroup|\Filament\Actions\Action>
     */
    public static function getTableActions(): array
    {
        return [
            ...parent::getTableActions(),

            ActionGroup::make([
                DiscordEditMessageAction::make(),

                DiscordSendMessageAction::make(),
            ]),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListDiscordThreads::route('/'),
            'view' => ViewDiscordThread::route('/{record:thread_id}'),
        ];
    }
}
