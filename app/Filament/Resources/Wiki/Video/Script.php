<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video;

use App\Filament\Actions\Storage\Wiki\Video\Script\DeleteScriptAction;
use App\Filament\Actions\Storage\Wiki\Video\Script\MoveScriptAction;
use App\Filament\BulkActions\Storage\Wiki\Video\Script\DeleteScriptBulkAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Video\Script\Pages\EditScript;
use App\Filament\Resources\Wiki\Video\Script\Pages\ListScripts;
use App\Filament\Resources\Wiki\Video\Script\Pages\ViewScript;
use App\Filament\TableActions\Repositories\Storage\Wiki\Video\Script\ReconcileScriptTableAction;
use App\Filament\TableActions\Storage\Wiki\Video\Script\UploadScriptTableAction;
use App\Models\Wiki\Video\VideoScript as ScriptModel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;

/**
 * Class Script.
 */
class Script extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = ScriptModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.video_script');
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
        return __('filament.resources.label.video_scripts');
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
        return __('filament.resources.group.wiki');
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
        return __('filament.resources.icon.video_scripts');
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
        return 'video-scripts';
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
        return ScriptModel::ATTRIBUTE_PATH;
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
        return $form
            ->schema([
                TextInput::make(ScriptModel::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.video_script.path'))
                    ->hiddenOn(['create', 'edit']),
            ])
            ->columns(1);
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
                TextColumn::make(ScriptModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(ScriptModel::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.video_script.path'))
                    ->copyableWithMessage(),
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
                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps()),
            ]);
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
                    [],
                    parent::getBaseRelations(),
                )
            ),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public static function getFilters(): array
    {
        return array_merge(
            parent::getFilters(),
            []
        );
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
            [
                ActionGroup::make([
                    MoveScriptAction::make('move-script'),

                    DeleteScriptAction::make('delete-script'),
                ]),
            ],
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
            [
                DeleteScriptBulkAction::make('delete-script'),
            ],
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
        return [
            ActionGroup::make([
                UploadScriptTableAction::make('upload-script'),

                ReconcileScriptTableAction::make('reconcile-script'),
            ]),
        ];
    }

    /**
     * Determine whether the related model can be created.
     *
     * @return bool
     */
    public static function canCreate(): bool
    {
        return false;
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
            'index' => ListScripts::route('/'),
            'view' => ViewScript::route('/{record:script_id}'),
            'edit' => EditScript::route('/{record:script_id}/edit'),
        ];
    }
}
