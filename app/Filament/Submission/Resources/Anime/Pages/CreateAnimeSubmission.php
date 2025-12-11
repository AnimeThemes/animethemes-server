<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Anime\Pages;

use App\Enums\Models\User\SubmissionStatus;
use App\Filament\Actions\Models\Wiki\Song\LoadArtistsAction;
use App\Filament\Actions\Models\Wiki\Song\Performance\LoadMembersAction;
use App\Filament\Components\Fields\SubmissionBelongsTo;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Resources\Wiki\Anime as AnimeModel;
use App\Filament\Resources\Wiki\Anime\Synonym;
use App\Filament\Resources\Wiki\Anime\Theme;
use App\Filament\Resources\Wiki\Anime\Theme\Entry;
use App\Filament\Resources\Wiki\Anime\Theme\Schemas\ThemeForm;
use App\Filament\Resources\Wiki\Artist as ArtistResource;
use App\Filament\Resources\Wiki\ExternalResource;
use App\Filament\Resources\Wiki\Group;
use App\Filament\Resources\Wiki\Series as SeriesResource;
use App\Filament\Resources\Wiki\Song;
use App\Filament\Resources\Wiki\Song\RelationManagers\PerformanceSongRelationManager;
use App\Filament\Resources\Wiki\Studio as StudioResource;
use App\Filament\Submission\Resources\AnimeSubmissionResource;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStage;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource as ExternalResourceModel;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song as SongModel;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Models\Wiki\Studio;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CreateAnimeSubmission extends CreateRecord
{
    protected static string $resource = AnimeSubmissionResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make(SubmissionStage::ATTRIBUTE_NOTES)
                    ->label(__('submissions.fields.base.notes.name'))
                    ->helperText(__('submissions.fields.base.notes.help'))
                    ->required()
                    ->rows(5),

                Tabs::make('tabs')
                    ->vertical()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('anime')
                            ->statePath('anime')
                            ->label(__('filament.resources.label.anime'))
                            ->columns(2)
                            ->schema(AnimeModel::form($schema)->getComponents()),

                        Tab::make(Anime::RELATION_SYNONYMS)
                            ->label(__('filament.resources.label.anime_synonyms'))
                            ->schema([
                                Repeater::make(Anime::RELATION_SYNONYMS)
                                    ->label(__('filament.resources.label.anime_synonyms'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.anime_synonym')]))
                                    ->defaultItems(0)
                                    ->schema(Synonym::form($schema)->getComponents()),
                            ]),

                        Tab::make(Anime::RELATION_THEMES)
                            ->label(__('filament.resources.label.anime_themes'))
                            ->schema([
                                Repeater::make(Anime::RELATION_THEMES)
                                    ->label(__('filament.resources.label.anime_themes'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.anime_theme')]))
                                    ->schema([
                                        Tabs::make('Tabs')
                                            ->schema([
                                                Tab::make('theme')
                                                    ->label(__('filament.resources.singularLabel.anime_theme'))
                                                    ->schema([
                                                        ThemeForm::typeField(),
                                                        ThemeForm::sequenceField(),
                                                        ThemeForm::slugField(),

                                                        SubmissionBelongsTo::make(AnimeTheme::ATTRIBUTE_GROUP)
                                                            ->resource(Group::class)
                                                            ->showCreateOption()
                                                            ->live()
                                                            ->partiallyRenderComponentsAfterStateUpdated([AnimeTheme::ATTRIBUTE_SLUG])
                                                            ->afterStateUpdated(fn (Set $set, Get $get) => Theme::setThemeSlug($set, $get)),

                                                        Hidden::make(AnimeTheme::ATTRIBUTE_GROUP.'_virtual'),
                                                    ]),

                                                Tab::make('song')
                                                    ->label(__('filament.resources.singularLabel.song'))
                                                    ->schema([
                                                        SubmissionBelongsTo::make(AnimeTheme::ATTRIBUTE_SONG)
                                                            ->resource(Song::class)
                                                            ->showCreateOption()
                                                            ->live()
                                                            ->hintAction(LoadArtistsAction::make()),

                                                        Hidden::make(AnimeTheme::ATTRIBUTE_SONG.'_virtual'),

                                                        Repeater::make('performances')
                                                            ->label(__('filament.resources.label.artists'))
                                                            ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.artist')]))
                                                            ->live(true)
                                                            ->key('song.performances')
                                                            ->collapsible()
                                                            ->defaultItems(0)
                                                            ->columns(3)
                                                            ->columnSpanFull()
                                                            ->reorderableWithDragAndDrop(false)
                                                            ->reorderableWithButtons()
                                                            ->formatStateUsing(function (Get $get): array {
                                                                /** @var SongModel|null $song */
                                                                $song = SongModel::query()->find($get(Performance::ATTRIBUTE_SONG));

                                                                return PerformanceSongRelationManager::formatArtists($song);
                                                            })
                                                            ->schema([
                                                                SubmissionBelongsTo::make(Artist::ATTRIBUTE_ID)
                                                                    ->resource(ArtistResource::class)
                                                                    ->showCreateOption()
                                                                    ->required()
                                                                    ->hintAction(LoadMembersAction::make()),

                                                                Hidden::make(Artist::ATTRIBUTE_ID.'_virtual'),

                                                                TextInput::make(Performance::ATTRIBUTE_AS)
                                                                    ->label(__('filament.fields.performance.as.name'))
                                                                    ->helperText(__('filament.fields.performance.as.help')),

                                                                TextInput::make(Performance::ATTRIBUTE_ALIAS)
                                                                    ->label(__('filament.fields.performance.alias.name'))
                                                                    ->helperText(__('filament.fields.performance.alias.help')),

                                                                Repeater::make('memberships')
                                                                    ->label(__('filament.resources.label.memberships'))
                                                                    ->helperText(__('filament.fields.performance.memberships.help'))
                                                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.member')]))
                                                                    ->collapsible()
                                                                    ->defaultItems(0)
                                                                    ->columns(3)
                                                                    ->columnSpanFull()
                                                                    ->reorderableWithDragAndDrop(false)
                                                                    ->reorderableWithButtons()
                                                                    ->schema([
                                                                        SubmissionBelongsTo::make(Membership::ATTRIBUTE_MEMBER)
                                                                            ->resource(ArtistResource::class)
                                                                            ->showCreateOption()
                                                                            ->label(__('filament.fields.membership.member'))
                                                                            ->required(),

                                                                        Hidden::make(Membership::ATTRIBUTE_MEMBER.'_virtual')
                                                                            ->dehydrated(true),

                                                                        TextInput::make(Membership::ATTRIBUTE_AS)
                                                                            ->label(__('filament.fields.membership.as.name'))
                                                                            ->helperText(__('filament.fields.membership.as.help')),

                                                                        TextInput::make(Membership::ATTRIBUTE_ALIAS)
                                                                            ->label(__('filament.fields.membership.alias.name'))
                                                                            ->helperText(__('filament.fields.membership.alias.help')),
                                                                    ]),
                                                            ]),
                                                    ]),

                                                Tab::make('entries')
                                                    ->label(__('filament.resources.label.anime_theme_entries'))
                                                    ->schema([
                                                        Repeater::make(AnimeTheme::RELATION_ENTRIES)
                                                            ->label(__('filament.resources.label.anime_theme_entries'))
                                                            ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.anime_theme_entry')]))
                                                            ->schema(Entry::form($schema)->getComponents()),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        Tab::make(Anime::RELATION_SERIES)
                            ->label(__('filament.resources.label.series'))
                            ->schema([
                                Repeater::make(Anime::RELATION_SERIES)
                                    ->label(__('filament.resources.label.series'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.series')]))
                                    ->defaultItems(0)
                                    ->schema([
                                        SubmissionBelongsTo::make(Series::ATTRIBUTE_ID)
                                            ->resource(SeriesResource::class)
                                            ->showCreateOption()
                                            ->required(),

                                        Hidden::make(Series::ATTRIBUTE_ID.'_virtual'),
                                    ]),
                            ]),

                        Tab::make(Anime::RELATION_RESOURCES)
                            ->label(__('filament.resources.label.external_resources'))
                            ->schema([
                                Repeater::make(Anime::RELATION_RESOURCES)
                                    ->label(__('filament.resources.label.external_resources'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.external_resource')]))
                                    ->defaultItems(0)
                                    ->schema([
                                        SubmissionBelongsTo::make(ExternalResourceModel::ATTRIBUTE_ID)
                                            ->resource(ExternalResource::class)
                                            ->showCreateOption()
                                            ->required(),

                                        Hidden::make(ExternalResourceModel::ATTRIBUTE_ID.'_virtual'),
                                    ]),
                            ]),

                        Tab::make(Anime::RELATION_STUDIOS)
                            ->label(__('filament.resources.label.studios'))
                            ->schema([
                                Repeater::make(Anime::RELATION_STUDIOS)
                                    ->label(__('filament.resources.label.studios'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.studio')]))
                                    ->defaultItems(0)
                                    ->schema([
                                        SubmissionBelongsTo::make(Studio::ATTRIBUTE_ID)
                                            ->resource(StudioResource::class)
                                            ->showCreateOption()
                                            ->required(),

                                        Hidden::make(Studio::ATTRIBUTE_ID.'_virtual'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    // TODO: Refactor common submission creation logic
    protected function handleRecordCreation(array $data): Model
    {
        $submission = Submission::query()
            ->create([
                Submission::ATTRIBUTE_STATUS => SubmissionStatus::PENDING->value,
                Submission::ATTRIBUTE_TYPE => CreateAnimeSubmission::class,
                Submission::ATTRIBUTE_USER => Auth::id(),
            ]);

        SubmissionStage::query()
            ->create([
                SubmissionStage::ATTRIBUTE_SUBMISSION => $submission->getKey(),
                SubmissionStage::ATTRIBUTE_FIELDS => Arr::except($data, SubmissionStage::ATTRIBUTE_NOTES),
                SubmissionStage::ATTRIBUTE_NOTES => Arr::get($data, SubmissionStage::ATTRIBUTE_NOTES),
                SubmissionStage::ATTRIBUTE_STAGE => 1,
            ]);

        $this->halt();

        return new Anime();
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label(__('submissions.buttons.submit'));
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return parent::getCreateAnotherFormAction()
            ->label(__('submissions.buttons.submit_another'));
    }
}
