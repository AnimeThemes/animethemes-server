<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Report;

use App\Enums\Models\Admin\ApprovableStatus;
use App\Enums\Models\Admin\ReportActionType;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\KeyValueThreeEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\Admin\Report;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Admin\Report\ReportStep\Pages\ListReportSteps;
use App\Filament\Resources\Admin\Report\ReportStep\Pages\ViewReportStep;
use App\Models\Admin\Report\ReportStep as ReportStepModel;
use App\Models\Admin\Report as ReportModel;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;

/**
 * Class ReportStep.
 */
class ReportStep extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = ReportStepModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.report_step');
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
        return __('filament.resources.label.report_steps');
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
        return __('filament.resources.group.admin');
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
        return __('filament.resources.icon.report_steps');
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
        return 'report-steps';
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
        return ReportStepModel::ATTRIBUTE_ID;
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
        return $form;
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
                TextColumn::make(ReportStepModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ReportStepModel::ATTRIBUTE_ACTION)
                    ->label(__('filament.fields.report_step.actionable'))
                    ->html()
                    ->formatStateUsing(fn (ReportStepModel $record) => class_basename($record->actionable_type)),

                TextColumn::make(ReportStepModel::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.report.status'))
                    ->formatStateUsing(fn (ApprovableStatus $state) => $state->localize())
                    ->color(fn (ApprovableStatus $state) => $state->color())
                    ->badge(),

                TextColumn::make(ReportStepModel::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.report.finished_at'))
                    ->dateTime(),

                BelongsToColumn::make(ReportStepModel::RELATION_REPORT . '.' . ReportModel::ATTRIBUTE_ID)
                    ->resource(Report::class),
            ]);
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
                        TextEntry::make(ReportStepModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(ReportStepModel::ATTRIBUTE_ACTION)
                            ->label(__('filament.fields.report_step.action'))
                            ->html()
                            ->formatStateUsing(fn (ReportStepModel $record, ReportActionType $state) => static::getActionName($record, $state)),

                        TextEntry::make(ReportStepModel::ATTRIBUTE_PIVOT_CLASS)
                            ->label(__('filament.fields.report_step.pivot'))
                            ->hidden(fn ($state) => is_null($state)),

                        TextEntry::make(ReportStepModel::ATTRIBUTE_STATUS)
                            ->label(__('filament.fields.report.status'))
                            ->formatStateUsing(fn (ApprovableStatus $state) => $state->localize())
                            ->color(fn (ApprovableStatus $state) => $state->color())
                            ->badge(),

                        TextEntry::make(ReportStepModel::ATTRIBUTE_FINISHED_AT)
                            ->label(__('filament.fields.report.finished_at'))
                            ->dateTime(),

                        TextEntry::make(ReportStepModel::ATTRIBUTE_REPORT)
                            ->label(__('filament.resources.singularLabel.report'))
                            ->urlToRelated(Report::class, ReportStepModel::RELATION_REPORT),

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
                            ->hidden(fn (?array $state, ReportStepModel $record) => is_null($state) || $record->action === ReportActionType::UPDATE)
                            ->columnSpanFull(),
                        ])
                    ->columns(3),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
            ]);
    }

    /**
     * The title of the report step.
     *
     * @param  ReportStepModel  $record
     * @param  ReportActionType  $state
     * @return string
     */
    protected static function getActionName(ReportStepModel $record, ReportActionType $state): string
    {
        $actionableUrl = Filament::getUrl($record->actionable);
        $name = $state === ReportActionType::CREATE ? class_basename($record->actionable_type) : $record->actionable->getName();
        $actionableLink = "<a style='color: rgb(64, 184, 166);' href='{$actionableUrl}'>{$name}</a>";

        if ($state === ReportActionType::ATTACH || $state === ReportActionType::DETACH) {
            $targetUrl = Filament::getUrl($record->target);

            $targetLink = "<a style='color: rgb(64, 184, 166);' href='{$targetUrl}'>{$record->target->getName()}</a>";

            return $state->localize() . ' ' . $actionableLink . ' to ' . $targetLink . ' via ' . class_basename($record->pivot_class);
        }

        if ($state === ReportActionType::CREATE) {
            return $record->action->localize() . ' ' . $name;
        }

        return $record->action->localize() . ' ' . $actionableLink;
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
        return [];
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
     * Get the table actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getTableActions(): array
    {
        return array_merge(
            parent::getTableActions(),
            [],
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
            'index' => ListReportSteps::route('/'),
            'view' => ViewReportStep::route('/{record:step_id}'),
        ];
    }
}
