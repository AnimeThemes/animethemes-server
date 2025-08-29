<?php

declare(strict_types=1);

namespace App\Filament\Resources\User\Report;

use App\Contracts\Models\Nameable;
use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\ReportActionType;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\KeyValueThreeEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\User\Report as ReportResource;
use App\Filament\Resources\User\Report\ReportStep\Pages\ListReportSteps;
use App\Filament\Resources\User\Report\ReportStep\Pages\ViewReportStep;
use App\Models\User\Report\ReportStep as ReportStepModel;
use Filament\Facades\Filament;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ReportStep extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ReportStepModel::class;

    /**
     * Get the displayable singular label of the resource.
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.report_step');
    }

    /**
     * Get the displayable label of the resource.
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.report_steps');
    }

    /**
     * The logical group associated with the resource.
     */
    public static function getNavigationGroup(): string
    {
        return __('filament.resources.group.user');
    }

    /**
     * The icon displayed to the resource.
     */
    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedLightBulb;
    }

    /**
     * Get the slug (URI key) for the resource.
     */
    public static function getRecordSlug(): string
    {
        return 'report-steps';
    }

    /**
     * Get the title attribute for the resource.
     */
    public static function getRecordTitleAttribute(): string
    {
        return ReportStepModel::ATTRIBUTE_ID;
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
        return $query->with([
            ReportStepModel::RELATION_REPORT,
        ]);
    }

    /**
     * The form to the actions.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    /**
     * The index page of the resource.
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(ReportStepModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ReportStepModel::ATTRIBUTE_ACTION)
                    ->label(__('filament.fields.report_step.actionable'))
                    ->html()
                    ->formatStateUsing(fn (ReportStepModel $record) => class_basename($record->actionable_type)),

                TextColumn::make(ReportStepModel::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.report.status'))
                    ->formatStateUsing(fn (ApprovableStatus $state) => $state->localize())
                    ->badge(),

                TextColumn::make(ReportStepModel::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.report.finished_at'))
                    ->dateTime(),

                BelongsToColumn::make(ReportStepModel::RELATION_REPORT, ReportResource::class),
            ]);
    }

    /**
     * Get the infolist available for the resource.
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(ReportStepModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(ReportStepModel::ATTRIBUTE_ACTION)
                            ->label(__('filament.fields.report_step.action'))
                            ->html()
                            ->formatStateUsing(fn (ReportStepModel $record, ReportActionType $state) => static::getActionName($record, $state)),

                        TextEntry::make(ReportStepModel::ATTRIBUTE_STATUS)
                            ->label(__('filament.fields.report.status'))
                            ->formatStateUsing(fn (ApprovableStatus $state) => $state->localize())
                            ->badge(),

                        TextEntry::make(ReportStepModel::ATTRIBUTE_FINISHED_AT)
                            ->label(__('filament.fields.report.finished_at'))
                            ->dateTime(),

                        BelongsToEntry::make(ReportStepModel::RELATION_REPORT, ReportResource::class)
                            ->label(__('filament.resources.singularLabel.report')),

                        KeyValueThreeEntry::make(ReportStepModel::ATTRIBUTE_FIELDS)
                            ->label(__('filament.fields.report_step.fields.name'))
                            ->leftLabel(__('filament.fields.report_step.fields.columns'))
                            ->middleLabel(__('filament.fields.report_step.fields.old_values'))
                            ->rightLabel(__('filament.fields.report_step.fields.values'))
                            ->middleValueThroughState(fn (ReportStepModel $record) => $record->formatFields($record->actionable->attributesToArray()))
                            ->visible(fn (ReportStepModel $record) => $record->action === ReportActionType::UPDATE)
                            ->state(fn (ReportStepModel $record) => $record->formatFields())
                            ->columnSpanFull(),

                        KeyValueEntry::make(ReportStepModel::ATTRIBUTE_FIELDS)
                            ->label(__('filament.fields.report_step.fields.name'))
                            ->keyLabel(__('filament.fields.report_step.fields.columns'))
                            ->valueLabel(__('filament.fields.report_step.fields.values'))
                            ->hidden(fn (?array $state, ReportStepModel $record) => is_null($state) || $record->action === ReportActionType::UPDATE)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * The title of the report step.
     */
    protected static function getActionName(ReportStepModel $record, ReportActionType $state): string
    {
        $name = $record->actionable instanceof Nameable ? $record->actionable->getName() : $record->actionable_type;

        if ($state === ReportActionType::CREATE) {
            return $record->action->localize().' '.$name;
        }

        $actionableUrl = Filament::getUrl($record->actionable);
        $actionableLink = "<a style='color: rgb(64, 184, 166);' href='{$actionableUrl}'>{$name}</a>";

        if ($state === ReportActionType::ATTACH || $state === ReportActionType::DETACH) {
            $targetUrl = Filament::getUrl($record->target);

            $targetName = $record->target instanceof Nameable ? $record->target->getName() : $record->target_type;

            $targetLink = "<a style='color: rgb(64, 184, 166);' href='{$targetUrl}'>{$targetName}</a>";

            return $state->localize().' '.$actionableLink.' to '.$targetLink.' via '.class_basename($record->pivot);
        }

        return $record->action->localize().' '.$actionableLink;
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListReportSteps::route('/'),
            'view' => ViewReportStep::route('/{record:step_id}'),
        ];
    }
}
