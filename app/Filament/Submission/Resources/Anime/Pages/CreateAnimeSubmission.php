<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources\Anime\Pages;

use App\Actions\Submission\SubmitNewAnimeAction;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Submission\Components\SelectBuilder;
use App\Filament\Submission\Resources\AnimeSubmissionResource;
use App\Filament\Submission\Resources\Schema\AnimeForm;
use App\Filament\Submission\Resources\Schema\ArtistForm;
use App\Filament\Submission\Resources\Schema\EntryForm;
use App\Filament\Submission\Resources\Schema\ResourceForm;
use App\Filament\Submission\Resources\Schema\SeriesForm;
use App\Filament\Submission\Resources\Schema\SongForm;
use App\Filament\Submission\Resources\Schema\StudioForm;
use App\Filament\Submission\Resources\Schema\SynonymForm;
use App\Filament\Submission\Resources\Schema\ThemeForm;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionAnime;
use App\Models\User\Submission\SubmissionPerformance;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Series;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateAnimeSubmission extends CreateRecord
{
    protected static string $resource = AnimeSubmissionResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make(Submission::ATTRIBUTE_SOURCE)
                    ->label(__('submissions.fields.base.source.name'))
                    ->helperText(__('submissions.fields.base.source.help'))
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
                            ->schema(AnimeForm::configure($schema)->getComponents()),

                        Tab::make(SubmissionAnime::RELATION_SYNONYMS)
                            ->label(__('filament.resources.label.synonyms'))
                            ->schema([
                                Repeater::make(SubmissionAnime::RELATION_SYNONYMS)
                                    ->label(__('filament.resources.label.synonyms'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.synonym')]))
                                    ->defaultItems(0)
                                    ->schema(SynonymForm::configure($schema)->getComponents()),
                            ]),

                        Tab::make(SubmissionAnime::RELATION_THEMES)
                            ->label(__('filament.resources.label.anime_themes'))
                            ->schema([
                                Repeater::make(SubmissionAnime::RELATION_THEMES)
                                    ->label(__('filament.resources.label.anime_themes'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.anime_theme')]))
                                    ->schema([
                                        Tabs::make('Tabs')
                                            ->schema([
                                                Tab::make('theme')
                                                    ->label(__('filament.resources.singularLabel.anime_theme'))
                                                    ->schema(ThemeForm::configure($schema)->getComponents()),

                                                Tab::make('song')
                                                    ->label(__('filament.resources.singularLabel.song'))
                                                    ->schema([
                                                        SelectBuilder::make('song')
                                                            ->label(__('filament.resources.singularLabel.song'))
                                                            ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.song')]))
                                                            ->maxItems(1)
                                                            ->reorderable(false)
                                                            ->set(Song::class, SongForm::class),

                                                        Repeater::make('performances')
                                                            ->label(__('filament.resources.label.artists'))
                                                            ->live(true)
                                                            ->key('song.performances')
                                                            ->collapsible()
                                                            ->defaultItems(0)
                                                            ->columns(3)
                                                            ->columnSpanFull()
                                                            ->schema([
                                                                SelectBuilder::make('artist')
                                                                    ->label(__('filament.resources.singularLabel.artist'))
                                                                    ->maxItems(1)
                                                                    ->reorderable(false)
                                                                    ->columnSpanFull()
                                                                    ->set(Artist::class, ArtistForm::class),

                                                                TextInput::make(SubmissionPerformance::ATTRIBUTE_AS)
                                                                    ->label(__('filament.fields.performance.as.name'))
                                                                    ->helperText(__('filament.fields.performance.as.help')),

                                                                TextInput::make(SubmissionPerformance::ATTRIBUTE_ALIAS)
                                                                    ->label(__('filament.fields.performance.alias.name'))
                                                                    ->helperText(__('filament.fields.performance.alias.help')),

                                                                Repeater::make('members')
                                                                    ->label(__('filament.resources.label.members'))
                                                                    ->helperText(__('filament.fields.performance.members.help'))
                                                                    ->collapsible()
                                                                    ->defaultItems(0)
                                                                    ->columns(3)
                                                                    ->columnSpanFull()
                                                                    ->schema([
                                                                        SelectBuilder::make('member')
                                                                            ->label(__('filament.fields.performance.member'))
                                                                            ->maxItems(1)
                                                                            ->reorderable(false)
                                                                            ->columnSpanFull()
                                                                            ->set(Artist::class, ArtistForm::class),

                                                                        TextInput::make(SubmissionPerformance::ATTRIBUTE_MEMBER_AS)
                                                                            ->label(__('filament.fields.performance.member_as.name'))
                                                                            ->helperText(__('filament.fields.performance.member_as.help')),

                                                                        TextInput::make(SubmissionPerformance::ATTRIBUTE_MEMBER_ALIAS)
                                                                            ->label(__('filament.fields.performance.member_alias.name'))
                                                                            ->helperText(__('filament.fields.performance.member_alias.help')),
                                                                    ]),
                                                            ]),
                                                    ]),

                                                Tab::make('entries')
                                                    ->label(__('filament.resources.label.anime_theme_entries'))
                                                    ->schema([
                                                        Repeater::make('entries')
                                                            ->label(__('filament.resources.label.anime_theme_entries'))
                                                            ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.anime_theme_entry')]))
                                                            ->schema(EntryForm::configure($schema)->getComponents()),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        Tab::make(SubmissionAnime::RELATION_SERIES)
                            ->label(__('filament.resources.label.series'))
                            ->schema([
                                SelectBuilder::make(SubmissionAnime::RELATION_SERIES)
                                    ->label(__('filament.resources.label.series'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.series')]))
                                    ->set(Series::class, SeriesForm::class),
                            ]),

                        Tab::make(SubmissionAnime::RELATION_RESOURCES)
                            ->label(__('filament.resources.label.external_resources'))
                            ->schema([
                                Repeater::make(SubmissionAnime::RELATION_RESOURCES)
                                    ->label(__('filament.resources.label.external_resources'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.external_resource')]))
                                    ->defaultItems(0)
                                    ->schema(ResourceForm::configure($schema)->getComponents()),
                            ]),

                        Tab::make(SubmissionAnime::RELATION_STUDIOS)
                            ->label(__('filament.resources.label.studios'))
                            ->schema([
                                SelectBuilder::make(SubmissionAnime::RELATION_STUDIOS)
                                    ->label(__('filament.resources.label.studios'))
                                    ->addActionLabel(__('filament.buttons.add', ['label' => __('filament.resources.singularLabel.studio')]))
                                    ->set(Studio::class, StudioForm::class),
                            ]),
                    ]),
            ]);
    }

    // TODO: Refactor common submission creation logic
    protected function handleRecordCreation(array $data): Model
    {
        new SubmitNewAnimeAction()->handle(Auth::user(), $data);

        $this->halt();

        return new Submission();
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
