<?php

declare(strict_types=1);

namespace App\Filament\Resources\Document;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Infolist\TextEntry;
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
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
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
     * The icon displayed to the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getNavigationIcon(): string
    {
        return __('filament.resources.icon.pages');
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
        return 'pages';
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
                    ->helperText(__('filament.fields.page.name.help'))
                    ->required()
                    ->maxLength(192)
                    ->rules(['required', 'max:192'])
                    ->live(true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set(PageModel::ATTRIBUTE_SLUG, Str::slug($state, '_'))),

                TextInput::make(PageModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.page.slug.name'))
                    ->helperText(__('filament.fields.page.slug.help'))
                    ->required()
                    ->unique(PageModel::class, PageModel::ATTRIBUTE_SLUG, ignoreRecord: true)
                    ->maxLength(192)
                    ->regex('/^[\pL\pM\pN\/_-]+$/u')
                    ->rules([
                        fn ($record) => [
                            'required',
                            'max:192',
                            'regex:/^[\pL\pM\pN\/_-]+$/u',
                            $record !== null
                                ? Rule::unique(PageModel::class)
                                    ->ignore($record->getKey(), PageModel::ATTRIBUTE_ID)
                                    ->__toString()
                                : Rule::unique(PageModel::class)->__toString(),
                        ]
                    ]),

                MarkdownEditor::make(PageModel::ATTRIBUTE_BODY)
                    ->label(__('filament.fields.page.body.name'))
                    ->helperText(__('filament.fields.page.body.help'))
                    ->required()
                    ->maxLength(16777215)
                    ->rules(['required', 'max:16777215'])
                    ->formatStateUsing(fn ($livewire) => PageModel::find($livewire->getRecord()->getKey())->body)
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
                    ->sortable(),

                TextColumn::make(PageModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.page.name.name'))
                    ->sortable()
                    ->searchable()
                    ->copyableWithMessage()
                    ->toggleable(),

                TextColumn::make(PageModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.page.slug.name'))
                    ->sortable()
                    ->copyableWithMessage()
                    ->toggleable(),

                TextColumn::make(PageModel::ATTRIBUTE_BODY)
                    ->label(__('filament.fields.page.body.name'))
                    ->sortable()
                    ->hidden(),
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
                        TextEntry::make(PageModel::ATTRIBUTE_ID)
                            ->label(__('filament.fields.base.id')),

                        TextEntry::make(PageModel::ATTRIBUTE_NAME)
                            ->label(__('filament.fields.page.name.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(PageModel::ATTRIBUTE_SLUG)
                            ->label(__('filament.fields.page.slug.name'))
                            ->copyableWithMessage(),

                        TextEntry::make(PageModel::ATTRIBUTE_BODY)
                            ->label(__('filament.fields.page.body.name'))
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make(__('filament.fields.base.timestamps'))
                    ->schema(parent::timestamps())
                    ->columns(3),
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
     * Get the header actions available for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
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
