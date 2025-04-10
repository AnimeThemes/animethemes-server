<?php

declare(strict_types=1);

namespace App\Filament\Resources\Document;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Infolist\TextEntry;
use App\Filament\Components\Infolist\TimestampSection;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Document\Page\Pages\ListPages;
use App\Filament\Resources\Document\Page\Pages\ViewPage;
use App\Models\Document\Page as PageModel;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
        return __('filament-icons.resources.pages');
    }

    /**
     * Get the slug (URI key) for the resource.
     *
     * @return string
     */
    public static function getRecordSlug(): string
    {
        return 'pages';
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
        return PageModel::ATTRIBUTE_NAME;
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
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::setSlug($set, $get)),

                Select::make('section')
                    ->label(__('filament.fields.page.section.name'))
                    ->helperText(__('filament.fields.page.section.help'))
                    ->options(PageModel::getSections())
                    ->live(true)
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::setSlug($set, $get)),

                TextInput::make(PageModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.page.slug.name'))
                    ->helperText(__('filament.fields.page.slug.help'))
                    ->regex('/^[\pL\pM\pN\/_-]+$/u'),

                MarkdownEditor::make(PageModel::ATTRIBUTE_BODY)
                    ->label(__('filament.fields.page.body.name'))
                    ->helperText(__('filament.fields.page.body.help'))
                    ->required()
                    ->maxLength(16777215)
                    ->rules(['required', 'max:16777215'])
                    ->columnSpan(2)
                    ->formatStateUsing(fn (?PageModel $record) => $record?->body),
            ])
            ->columns(2);
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
                TextColumn::make(PageModel::ATTRIBUTE_ID)
                    ->label(__('filament.fields.base.id')),

                TextColumn::make('section')
                    ->label(__('filament.fields.page.section.name'))
                    ->state(function (PageModel $record) {
                        $slug = $record->slug;
                        $lastSlash = strrpos($slug, '/');

                        return $lastSlash ? substr($slug, 0, $lastSlash) : $slug;
                    })
                    ->badge(),

                TextColumn::make(PageModel::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.page.name.name'))
                    ->searchable()
                    ->copyableWithMessage(),

                TextColumn::make(PageModel::ATTRIBUTE_SLUG)
                    ->label(__('filament.fields.page.slug.name'))
                    ->copyableWithMessage(),

                TextColumn::make(PageModel::ATTRIBUTE_BODY)
                    ->label(__('filament.fields.page.body.name'))
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

                        TextEntry::make('section')
                            ->label(__('filament.fields.page.section.name'))
                            ->state(function (PageModel $record) {
                                $slug = $record->slug;
                                $lastSlash = strrpos($slug, '/');

                                return $lastSlash ? substr($slug, 0, $lastSlash) : $slug;
                            })
                            ->badge(),

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
                    ->columns(2),

                TimestampSection::make(),
            ]);
    }

    /**
     * Set the page slug.
     *
     * @param  Set  $set
     * @param  Get  $get
     * @return void
     */
    protected static function setSlug(Set $set, Get $get): void
    {
        $slug = Str::of('');
        $name = $get(PageModel::ATTRIBUTE_NAME);
        $section = $get('section');

        if (!empty($section)) {
            $slug = $slug->append($section)->append('/');
        }

        $slug = $slug->append(Str::slug($name, '_'));

        $set(PageModel::ATTRIBUTE_SLUG, $slug->__toString());
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
            RelationGroup::make(static::getLabel(),[
                ...parent::getBaseRelations(),
            ]),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public static function getFilters(): array
    {
        return [
            ...parent::getFilters(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            ...parent::getActions(),
        ];
    }

    /**
     * Get the bulk actions available for the resource.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
        ];
    }

    /**
     * Get the table actions available for the resource.
     *
     * @return array
     */
    public static function getTableActions(): array
    {
        return [
            ...parent::getTableActions(),
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
            'index' => ListPages::route('/'),
            'view' => ViewPage::route('/{record:page_id}'),
        ];
    }
}
