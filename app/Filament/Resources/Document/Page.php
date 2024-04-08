<?php

declare(strict_types=1);

namespace App\Filament\Resources\Document;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Document\Page\Pages\CreatePage;
use App\Filament\Resources\Document\Page\Pages\EditPage;
use App\Filament\Resources\Document\Page\Pages\ListPages;
use App\Filament\Resources\Document\Page\Pages\ViewPage;
use App\Models\Document\Page as PageModel;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Class Page.
 */
class Page extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string|null
     */
    protected static ?string $model = PageModel::class;

    /**
     * The icon displayed to the resource.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getLabel(): string
    {
        return __('filament.resources.singularLabel.page');
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
        return __('filament.resources.label.pages');
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
        return __('filament.resources.group.document');
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
        return 'pages';
    }

    /**
     * Get the route key for the resource.
     *
     * @return string|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getRecordRouteKeyName(): ?string
    {
        return PageModel::ATTRIBUTE_ID;
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
                TextInput::make(PageModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.page.name.name'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192'])
                    ->live(true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(PageModel::ATTRIBUTE_SLUG, Str::of($state, '_'))),

                TextInput::make(PageModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.page.slug.name'))
                    ->helperText(__('filament.fields.page.slug.help'))
                    ->required()
                    ->maxLength(192)
                   // ->regex('/^[\pL\pM\pN\/_-]+$/u')
                  ->rules(['required', 'max:192', /*'regex:/^[\pL\pM\pN\/_-]+$/u',*/ /*Rule::unique(PageModel::class)*/]),

                MarkdownEditor::make(PageModel::ATTRIBUTE_BODY)
                    ->label(__('filament.fields.page.body.name'))
                    ->helperText(__('filament.fields.page.body.help'))
                    ->required()
                    ->maxLength(16777215)
                    ->rules(['required', 'max:16777215'])
                    ->columnSpan(2),
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
                TextColumn::make(PageModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make(PageModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.page.name.name'))
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                TextColumn::make(PageModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.page.slug.name'))
                    ->sortable()
                    ->copyable(),

                TextColumn::make(PageModel::ATTRIBUTE_BODY)
                    ->label(__('filament.fields.page.body.name'))
                    ->sortable()
                    ->copyable()
                    ->hidden(),
            ])
            ->defaultSort(PageModel::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
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
        return [];
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
            parent::getFilters(),
            []
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
     * Get the pages available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'view' => ViewPage::route('/{record:page_id}'),
            'edit' => EditPage::route('/{record:page_id}/edit'),
        ];
    }
}
