<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video;

use App\Enums\Filament\NavigationGroup;
use App\Filament\Actions\Repositories\Storage\Wiki\Video\Script\ReconcileScriptAction;
use App\Filament\Actions\Storage\Wiki\Video\Script\DeleteScriptAction;
use App\Filament\Actions\Storage\Wiki\Video\Script\MoveScriptAction;
use App\Filament\Actions\Storage\Wiki\Video\Script\UploadScriptAction;
use App\Filament\BulkActions\Storage\Wiki\Video\Script\DeleteScriptBulkAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Video\Script\Pages\ListScripts;
use App\Filament\Resources\Wiki\Video\Script\Pages\ViewScript;
use App\Models\Wiki\Video\VideoScript as ScriptModel;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Script extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = ScriptModel::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.video_script');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.video_scripts');
    }

    public static function getNavigationGroup(): NavigationGroup
    {
        return NavigationGroup::CONTENT;
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::OutlinedDocumentText;
    }

    public static function getRecordSlug(): string
    {
        return 'video-scripts';
    }

    public static function getRecordTitleAttribute(): string
    {
        return ScriptModel::ATTRIBUTE_PATH;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(ScriptModel::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.video_script.path'))
                    ->hiddenOn(['create', 'edit']),
            ])
            ->columns(1);
    }

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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * @return array<int, \Filament\Actions\Action|ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            MoveScriptAction::make(),

            DeleteScriptAction::make(),
        ];
    }

    /**
     * @param  array<int, ActionGroup|\Filament\Actions\Action>|null  $actionsIncludedInGroup
     * @return array<int, ActionGroup|\Filament\Actions\Action>
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions([
                DeleteScriptBulkAction::make(),
            ]),
        ];
    }

    /**
     * @return array<int, ActionGroup|\Filament\Actions\Action>
     */
    public static function getTableActions(): array
    {
        return [
            UploadScriptAction::make(),

            ActionGroup::make([
                ReconcileScriptAction::make(),
            ]),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListScripts::route('/'),
            'view' => ViewScript::route('/{record:script_id}'),
        ];
    }
}
