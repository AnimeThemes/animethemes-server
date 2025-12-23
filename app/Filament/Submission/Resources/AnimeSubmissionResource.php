<?php

declare(strict_types=1);

namespace App\Filament\Submission\Resources;

use App\Enums\Models\User\SubmissionStatus;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Submission\Resources\Anime\Pages\CreateAnimeSubmission;
use App\Filament\Submission\Resources\Anime\Pages\ListAnimeSubmissions;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStage;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class AnimeSubmissionResource extends BaseSubmissionResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Submission::class;

    public static function getModelLabel(): string
    {
        return __('submissions.resources.singularLabel.anime');
    }

    public static function getPluralModelLabel(): string
    {
        return __('submissions.resources.label.anime');
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedTv;
    }

    public static function getRecordTitleAttribute(): string
    {
        return Submission::ATTRIBUTE_ID;
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }

    public static function getRecordSlug(): string
    {
        return 'anime';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $query->whereBelongsTo(Auth::user(), Submission::RELATION_USER);

        // Necessary to prevent lazy loading when loading related resources
        /** @phpstan-ignore-next-line */
        return $query->with([
            Submission::RELATION_STAGES,
        ]);
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(Submission::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make('name')
                    ->label(__('submissions.fields.submission.name'))
                    ->state(
                        fn (Submission $submission) => Arr::get(
                            $submission
                                ->stages
                                ->sortBy(SubmissionStage::ATTRIBUTE_CREATED_AT)
                                ->first()
                                ->getAttribute(SubmissionStage::ATTRIBUTE_FIELDS),
                            'anime.name'
                        )
                    ),

                TextColumn::make(Submission::ATTRIBUTE_STATUS)
                    ->label(__('submissions.fields.submission.status'))
                    ->formatStateUsing(fn (SubmissionStatus $state): string => $state->localize())
                    ->badge(),

                TextColumn::make(Submission::ATTRIBUTE_FINISHED_AT)
                    ->label(__('submissions.fields.submission.finished_at'))
                    ->dateTime(),
            ]);
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return [];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListAnimeSubmissions::route('/'),
            'create' => CreateAnimeSubmission::route('/submit'),
        ];
    }
}
