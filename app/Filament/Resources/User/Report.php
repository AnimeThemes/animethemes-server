<?php

declare(strict_types=1);

namespace App\Filament\Resources\User;

use App\Enums\Models\User\ApprovableStatus;
use App\Filament\Components\Columns\BelongsToColumn;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\BelongsToEntry;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\Auth\User as UserResource;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\User\Report\Pages\ListReports;
use App\Filament\Resources\User\Report\Pages\ViewReport;
use App\Filament\Resources\User\Report\RelationManagers\StepReportRelationManager;
use App\Models\User\Report as ReportModel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Report.
 */
class Report extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ReportModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
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
    public static function getPluralModelLabel(): string
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
        return __('filament.resources.group.user');
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
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Schema $schema): Schema
    {
        return $schema;
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
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
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

                TimestampSection::make(),
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
     * Get the relationships available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                StepReportRelationManager::class,
            ]),
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
