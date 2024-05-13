<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Image\Pages\CreateImage;
use App\Filament\Resources\Wiki\Image\Pages\EditImage;
use App\Filament\Resources\Wiki\Image\Pages\ListImages;
use App\Filament\Resources\Wiki\Image\Pages\ViewImage;
use App\Filament\Resources\Wiki\Image\RelationManagers\AnimeImageRelationManager;
use App\Filament\Resources\Wiki\Image\RelationManagers\ArtistImageRelationManager;
use App\Filament\Resources\Wiki\Image\RelationManagers\PlaylistImageRelationManager;
use App\Filament\Resources\Wiki\Image\RelationManagers\StudioImageRelationManager;
use App\Filament\TableActions\Models\Wiki\Image\UploadImageTableAction;
use App\Models\Wiki\Image as ImageModel;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rules\Enum;

/**
 * Class Image.
 */
class Image extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = ImageModel::class;

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.image');
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
        return __('filament.resources.label.images');
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
        return __('filament.resources.icon.images');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getSlug(): string
    {
        return static::getDefaultSlug().'images';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): string
    {
        return ImageModel::ATTRIBUTE_ID;
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
                Select::make(ImageModel::ATTRIBUTE_FACET)
                    ->label(__('filament.fields.image.facet.name'))
                    ->helperText(__('filament.fields.image.facet.help'))
                    ->options(ImageFacet::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(ImageFacet::class)]),
            ])
            ->columns(2);
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
                TextColumn::make(ImageModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->sortable(),

                SelectColumn::make(ImageModel::ATTRIBUTE_FACET)
                    ->label(__('filament.fields.image.facet.name'))
                    ->options(ImageFacet::asSelectArray())
                    ->sortable()
                    ->toggleable(),

                ImageColumn::make(ImageModel::ATTRIBUTE_PATH)
                    ->label(__('nova.fields.image.image.name'))
                    ->disk(Config::get('image.disk'))
                    ->toggleable(),
            ])
            ->defaultSort(ImageModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions())
            ->headerActions(static::getHeaderActions());
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
            RelationGroup::make(static::getLabel(), [
                AnimeImageRelationManager::class,
                ArtistImageRelationManager::class,
                PlaylistImageRelationManager::class,
                StudioImageRelationManager::class,
            ]),
        ];
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
        return array_merge(
            [
                SelectFilter::make(ImageModel::ATTRIBUTE_FACET)
                    ->label(__('filament.fields.image.facet.name'))
                    ->options(ImageFacet::asSelectArray()),
            ],
            parent::getFilters(),
        );
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
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
    * Get the header actions available for the resource.
    *
    * @return array
    *
    * @noinspection PhpMissingParentCallCommonInspection
    */
   public static function getHeaderActions(): array
   {
       return [
            UploadImageTableAction::make('upload-image')
                ->label(__('filament.actions.models.wiki.upload_image.name'))
                ->requiresConfirmation()
                ->facets([ImageFacet::GRILL, ImageFacet::DOCUMENT])
                ->modalWidth(MaxWidth::FourExtraLarge)
                ->authorize('create', ImageModel::class),
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
            'index' => ListImages::route('/'),
            'create' => CreateImage::route('/create'),
            'view' => ViewImage::route('/{record:image_id}'),
            'edit' => EditImage::route('/{record:image_id}/edit'),
        ];
    }
}
