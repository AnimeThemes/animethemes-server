<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Submission;

use App\Contracts\Models\Nameable;
use App\Enums\Filament\NavigationGroup;
use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\SubmissionActionType;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\KeyValueThreeEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\User\Submission as SubmissionResource;
use App\Filament\Resources\User\Submission\SubmissionStep\Pages\ListSubmissionSteps;
use App\Filament\Resources\User\Submission\SubmissionStep\Pages\ViewSubmissionStep;
use App\Models\User\Submission\SubmissionStep as SubmissionStepModel;
use Filament\Facades\Filament;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SubmissionStep extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = SubmissionStepModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.submission_step');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.submission_steps');
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
        return 'submission-steps';
    }

    public static function getRecordTitleAttribute(): string
    {
        return SubmissionStepModel::ATTRIBUTE_ID;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Necessary to prevent lazy loading when loading related resources
        return $query->with([
            SubmissionStepModel::RELATION_SUBMISSION,
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
                TextColumn::make(SubmissionStepModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(SubmissionStepModel::ATTRIBUTE_ACTION)
                    ->label(__('filament.fields.submission_step.actionable'))
                    ->html()
                    ->formatStateUsing(fn (SubmissionStepModel $record): string => class_basename($record->actionable_type)),

                TextColumn::make(SubmissionStepModel::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.submission.status'))
                    ->formatStateUsing(fn (ApprovableStatus $state): ?string => $state->localize())
                    ->badge(),

                TextColumn::make(SubmissionStepModel::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.submission.finished_at'))
                    ->dateTime(),

                BelongsToColumn::make(SubmissionStepModel::RELATION_SUBMISSION, SubmissionResource::class),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(SubmissionStepModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(SubmissionStepModel::ATTRIBUTE_ACTION)
                            ->label(__('filament.fields.submission_step.action'))
                            ->html()
                            ->formatStateUsing(fn (SubmissionStepModel $record, SubmissionActionType $state): string => static::getActionName($record, $state)),

                        TextEntry::make(SubmissionStepModel::ATTRIBUTE_STATUS)
                            ->label(__('filament.fields.submission.status'))
                            ->formatStateUsing(fn (ApprovableStatus $state): ?string => $state->localize())
                            ->badge(),

                        TextEntry::make(SubmissionStepModel::ATTRIBUTE_FINISHED_AT)
                            ->label(__('filament.fields.submission.finished_at'))
                            ->dateTime(),

                        BelongsToEntry::make(SubmissionStepModel::RELATION_SUBMISSION, SubmissionResource::class)
                            ->label(__('filament.resources.singularLabel.submission')),

                        KeyValueThreeEntry::make(SubmissionStepModel::ATTRIBUTE_FIELDS)
                            ->label(__('filament.fields.submission_step.fields.name'))
                            ->leftLabel(__('filament.fields.submission_step.fields.columns'))
                            ->middleLabel(__('filament.fields.submission_step.fields.old_values'))
                            ->rightLabel(__('filament.fields.submission_step.fields.values'))
                            ->middleValueThroughState(fn (SubmissionStepModel $record): array => $record->formatFields($record->actionable->attributesToArray()))
                            ->visible(fn (SubmissionStepModel $record): bool => $record->action === SubmissionActionType::UPDATE)
                            ->state(fn (SubmissionStepModel $record): array => $record->formatFields())
                            ->columnSpanFull(),

                        KeyValueEntry::make(SubmissionStepModel::ATTRIBUTE_FIELDS)
                            ->label(__('filament.fields.submission_step.fields.name'))
                            ->keyLabel(__('filament.fields.submission_step.fields.columns'))
                            ->valueLabel(__('filament.fields.submission_step.fields.values'))
                            ->hidden(fn (?array $state, SubmissionStepModel $record): bool => is_null($state) || $record->action === SubmissionActionType::UPDATE)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * The title of the submission step.
     */
    protected static function getActionName(SubmissionStepModel $record, SubmissionActionType $state): string
    {
        $name = $record->actionable instanceof Nameable ? $record->actionable->getName() : $record->actionable_type;

        if ($state === SubmissionActionType::CREATE) {
            return $record->action->localize().' '.$name;
        }

        $actionableUrl = Filament::getUrl($record->actionable);
        $actionableLink = "<a style='color: rgb(64, 184, 166);' href='{$actionableUrl}'>{$name}</a>";

        if ($state === SubmissionActionType::ATTACH || $state === SubmissionActionType::DETACH) {
            $targetUrl = Filament::getUrl($record->target);

            $targetName = $record->target instanceof Nameable ? $record->target->getName() : $record->target_type;

            $targetLink = "<a style='color: rgb(64, 184, 166);' href='{$targetUrl}'>{$targetName}</a>";

            return $state->localize().' '.$actionableLink.' to '.$targetLink.' via '.class_basename($record->pivot);
        }

        return $record->action->localize().' '.$actionableLink;
    }

    /**
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListSubmissionSteps::route('/'),
            'view' => ViewSubmissionStep::route('/{record:step_id}'),
        ];
    }
}
