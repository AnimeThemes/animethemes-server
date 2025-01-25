<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin;

use App\Enums\Models\Admin\ApprovableStatus;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Admin\Report\Pages\ListReports;
use App\Filament\Resources\Admin\Report\Pages\ViewReport;
use App\Filament\Resources\Admin\Report\RelationManagers\StepReportRelationManager;
use App\Filament\Resources\Auth\User as UserResource;
use App\Models\Admin\Report as ReportModel;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Report.
 */
class Report extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = ReportModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.report');
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
        return __('filament.resources.label.reports');
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
        return __('filament-icons.resources.reports');
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
        return 'reports';
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
        return ReportModel::ATTRIBUTE_ID;
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
            ReportModel::RELATION_USER,
            ReportModel::RELATION_MODERATOR,
        ]);
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
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(ReportModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ReportModel::ATTRIBUTE_STATUS)
                    ->label(__('filament.fields.report.status'))
                    ->formatStateUsing(fn (ApprovableStatus $state) => $state->localize())
                    ->badge(),

                BelongsToColumn::make(ReportModel::RELATION_USER, UserResource::class),

                BelongsToColumn::make(ReportModel::RELATION_MODERATOR, UserResource::class)
                    ->label(__('filament.fields.report.moderator')),

                TextColumn::make(ReportModel::ATTRIBUTE_FINISHED_AT)
                    ->label(__('filament.fields.report.finished_at'))
                    ->dateTime(),
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
                        TextEntry::make(ReportModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(ReportModel::ATTRIBUTE_STATUS)
                            ->label(__('filament.fields.report.status'))
                            ->formatStateUsing(fn (ApprovableStatus $state) => $state->localize())
                            ->badge(),

                        BelongsToEntry::make(ReportModel::RELATION_USER, UserResource::class),

                        BelongsToEntry::make(ReportModel::RELATION_MODERATOR, UserResource::class)
                            ->label(__('filament.fields.report.moderator')),

                        TextEntry::make(ReportModel::ATTRIBUTE_FINISHED_AT)
                            ->label(__('filament.fields.report.finished_at'))
                            ->dateTime(),

                        TextEntry::make(ReportModel::ATTRIBUTE_NOTES)
                            ->label(__('filament.fields.report.notes'))
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
            ]);
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
     */
    public static function getTableActions(): array
    {
        return array_merge(
            parent::getTableActions(),
            [],
        );
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getLabel(),
                array_merge(
                    [
                        StepReportRelationManager::class,
                    ],
                )
            ),
        ];
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
            'index' => ListReports::route('/'),
            'view' => ViewReport::route('/{record:report_id}'),
        ];
    }
}
