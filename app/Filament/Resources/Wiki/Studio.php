<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Filament\Actions\Models\Wiki\Studio\AttachStudioResourceAction;
use App\Filament\Actions\Models\Wiki\Studio\BackfillStudioAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Slug;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Studio\Pages\ListStudios;
use App\Filament\Resources\Wiki\Studio\Pages\ViewStudio;
use App\Filament\Resources\Wiki\Studio\RelationManagers\AnimeStudioRelationManager;
use App\Filament\Resources\Wiki\Studio\RelationManagers\ImageStudioRelationManager;
use App\Filament\Resources\Wiki\Studio\RelationManagers\ResourceStudioRelationManager;
use App\Models\Wiki\Studio as StudioModel;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class Studio extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>|null
     */
    protected static ?string $model = StudioModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getModelLabel(): string
    {
        return __('filament.resources.singularLabel.studio');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.label.studios');
    }

    /**
     * The logical group associated with the resource.
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
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament-icons.resources.studios');
    }

    /**
     * Get the slug (URI key) for the resource.
     */
    public static function getRecordSlug(): string
    {
        return 'studios';
    }

    /**
     * Get the title attribute for the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordTitleAttribute(): string
    {
        return StudioModel::ATTRIBUTE_NAME;
    }

    /**
     * The form to the actions.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make(StudioModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.studio.name.name'))
                    ->helperText(__('filament.fields.studio.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->afterStateUpdatedJs(<<<'JS'
                        $set('slug', slug($state ?? ''));
                    JS),

                Slug::make(StudioModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.studio.slug.name'))
                    ->helperText(__('filament.fields.studio.slug.help')),
            ]);
    }

    /**
     * The index page of the resource.
     */
    public static function table(Table $table): Table
    {
        return parent::table($table)
            ->columns([
                TextColumn::make(StudioModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make(StudioModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.studio.name.name'))
                    ->copyableWithMessage(),

                TextColumn::make(StudioModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.studio.slug.name')),
            ])
            ->searchable();
    }

    /**
     * Get the infolist available for the resource.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::getRecordTitle($schema->getRecord()))
                    ->schema([
                        TextEntry::make(StudioModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.studio.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(StudioModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.studio.slug.name')),

                        TextEntry::make(StudioModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),
                    ])
                    ->columns(3),

                TimestampSection::make(),
            ]);
    }

    /**
     * Get the relationships available for the resource.
     *
     * @return array<int, RelationGroup|class-string<\Filament\Resources\RelationManagers\RelationManager>>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRelations(): array
    {
        return [
            RelationGroup::make(static::getModelLabel(), [
                AnimeStudioRelationManager::class,
                ResourceStudioRelationManager::class,
                ImageStudioRelationManager::class,

                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Filament\Actions\Action|\Filament\Actions\ActionGroup>
     */
    public static function getRecordActions(): array
    {
        return [
            BackfillStudioAction::make(),

            AttachStudioResourceAction::make(),
        ];
    }

    /**
     * Get the pages available for the resource.
     *
     * @return array<string, \Filament\Resources\Pages\PageRegistration>
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListStudios::route('/'),
            'view' => ViewStudio::route('/{record:studio_id}'),
        ];
    }
}
