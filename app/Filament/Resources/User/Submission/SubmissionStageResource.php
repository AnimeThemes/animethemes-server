<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Submission;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\User\Submission\SubmissionStage\Pages\ListSubmissionStages;
use App\Filament\Resources\User\Submission\SubmissionStage\Pages\ViewSubmissionStage;
use App\Filament\Resources\User\SubmissionResource;
use App\Models\User\Submission\SubmissionStage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SubmissionStageResource extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = SubmissionStage::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.submission_stage');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.submission_stages');
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
        return 'submission-stages';
    }

    public static function getRecordTitleAttribute(): string
    {
        return SubmissionStage::ATTRIBUTE_ID;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            SubmissionStage::RELATION_SUBMISSION,
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
                TextColumn::make(SubmissionStage::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                BelongsToColumn::make(SubmissionStage::RELATION_SUBMISSION, SubmissionResource::class),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(SubmissionStage::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        BelongsToEntry::make(SubmissionStage::RELATION_SUBMISSION, SubmissionResource::class)
                            ->label(__('filament.resources.singularLabel.submission')),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSubmissionStages::route('/'),
            'view' => ViewSubmissionStage::route('/{record:stage_id}'),
        ];
    }
}
