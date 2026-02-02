<?php

declare(strict_types=1);

namespace App\Filament\Resources\User;

use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\User\SubmissionStatus;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\UserResource;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\User\Submission\Pages\ListSubmissions;
use App\Filament\Resources\User\Submission\Pages\ViewSubmission;
use App\Filament\Resources\User\Submission\RelationManagers\StageSubmissionRelationManager;
use App\Models\User\Submission;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SubmissionResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = Submission::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.submission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.submissions');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::USER;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedLightBulb;
    }

    public static function getRecordSlug(): string
    {
        return 'submissions';
    }

    public static function getRecordTitleAttribute(): string
    {
        return Submission::ATTRIBUTE_ID;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            Submission::RELATION_USER,
            Submission::RELATION_MODERATOR,
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(Submission::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(Submission::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.submission.status'))
                    ->formatStateUsing(fn (SubmissionStatus $state): ?string => $state->localize())
                    ->badge(),

                BelongsToColumn::make(Submission::RELATION_USER, UserResource::class),

                BelongsToColumn::make(Submission::RELATION_MODERATOR, UserResource::class)
                    ->label(__('filament.fields.submission.moderator')),

                TextColumn::make(Submission::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.submission.finished_at'))
                    ->dateTime(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(Submission::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(Submission::ATTRIBUTE_STATUS)
                            ->label(__('filament.fields.submission.status'))
                            ->formatStateUsing(fn (SubmissionStatus $state): ?string => $state->localize())
                            ->badge(),

                        BelongsToEntry::make(Submission::RELATION_USER, UserResource::class),

                        BelongsToEntry::make(Submission::RELATION_MODERATOR, UserResource::class)
                            ->label(__('filament.fields.submission.moderator')),

                        TextEntry::make(Submission::ATTRIBUTE_FINISHED_AT)
                            ->label(__('filament.fields.submission.finished_at'))
                            ->dateTime(),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                StageSubmissionRelationManager::class,
            ]),
        ];
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSubmissions::route('/'),
            'view' => ViewSubmission::route('/{record:submission_id}'),
        ];
    }
}
