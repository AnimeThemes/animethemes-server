<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Filament\Resources\BaseRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * Class AnimesRelationManager.
 */
class AnimesRelationManager extends BaseRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @return string
     */
    protected static string $relationship = ExternalResource::RELATION_ANIME;

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(Anime::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->helperText(__('filament.fields.anime.name.help'))
                    ->required()
                    ->rules(['required', 'max:192'])
                    ->maxLength(192)
                    ->live()
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(Anime::ATTRIBUTE_SLUG, Str::slug($state, '_'))),

                TextInput::make(Anime::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->helperText(__('filament.fields.anime.slug.help'))
                    ->required()
                    ->rules(['required', 'max:192', 'alpha_dash', Rule::unique(Anime::class)]),

                TextInput::make(Anime::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name'))
                    ->helperText(__('filament.fields.anime.year.help'))
                    ->numeric()
                    ->required()
                    ->rules(['required', 'digits:4', 'integer'])
                    ->minValue(1960)
                    ->maxValue(intval(date('Y')) + 1),

                Select::make(Anime::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->helperText(__('filament.fields.anime.season.help'))
                    ->options(AnimeSeason::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(AnimeSeason::class)]),

                Select::make(Anime::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->helperText(__('filament.fields.anime.media_format.help'))
                    ->options(AnimeMediaFormat::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(AnimeMediaFormat::class)]),

                MarkdownEditor::make(Anime::ATTRIBUTE_SYNOPSIS)
                    ->label(__('filament.fields.anime.synopsis.name'))
                    ->helperText(__('filament.fields.anime.synopsis.help'))
                    ->columnSpan(2)
                    ->maxLength(65535)
                    ->rules('max:65535'),
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
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute(Anime::ATTRIBUTE_NAME)
            ->inverseRelationship(Anime::RELATION_RESOURCES)
            ->columns([
                TextColumn::make(Anime::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make(Anime::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.anime.name.name'))
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                TextColumn::make(Anime::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.anime.slug.name'))
                    ->sortable()
                    ->copyable(),

                TextColumn::make(Anime::ATTRIBUTE_YEAR)
                    ->label(__('filament.fields.anime.year.name'))
                    ->numeric()
                    ->sortable(),

                SelectColumn::make(Anime::ATTRIBUTE_SEASON)
                    ->label(__('filament.fields.anime.season.name'))
                    ->options(AnimeSeason::asSelectArray())
                    ->sortable(),

                SelectColumn::make(Anime::ATTRIBUTE_MEDIA_FORMAT)
                    ->label(__('filament.fields.anime.media_format.name'))
                    ->options(AnimeMediaFormat::asSelectArray())
                    ->sortable(),

                TextColumn::make(Anime::ATTRIBUTE_SYNOPSIS)
                    ->label(__('filament.fields.anime.synopsis.name'))
                    ->hidden(),
            ])
            ->filters(static::getFilters())
            ->headerActions(static::getHeaderActions())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
    }

    /**
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return array_merge(
            parent::getFilters(),
            [],
        );
    }

    /**
     * Get the actions available for the relation.
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
     * Get the bulk actions available for the relation.
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
     * Get the header actions available for the relation.
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
}
