<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Models\Wiki\Image;

use App\Concerns\Models\CanCreateImage;
use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Components\Fields\Select;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\TableActions\BaseTableAction;
use App\Models\Wiki\Image;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;

/**
 * Class UploadImageTableAction.
 */
class UploadImageTableAction extends BaseTableAction
{
    use CanCreateImage;

    protected array $facets = [];

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.wiki.upload_image.name'));

        $this->facets([
            ImageFacet::GRILL,
            ImageFacet::DOCUMENT,
        ]);

        $this->authorize('create', Image::class);
        $this->visible(fn ($livewire) => $livewire instanceof BaseListResources);
    }

    /**
     * Perform the action on the table.
     *
     * @param  array  $fields
     * @return void
     */
    public function handle(array $fields): void
    {
        $image = Arr::get($fields, Image::ATTRIBUTE_PATH);
        $facet = ImageFacet::from(intval(Arr::get($fields, Image::ATTRIBUTE_FACET)));

        $this->createImageFromFile($image, $facet);
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     */
    public function getForm(Form $form): Form
    {
        $options = [];

        foreach ($this->facets as $facet) {
            $options[$facet->value] = $facet->localize();
        }

        return $form
            ->schema([
                Select::make(Image::ATTRIBUTE_FACET)
                    ->label(__('filament.fields.image.facet.name'))
                    ->helperText(__('filament.fields.image.facet.help'))
                    ->options($options)
                    ->required()
                    ->rules(['required', new Enum(ImageFacet::class)]),

                FileUpload::make(Image::ATTRIBUTE_PATH)
                    ->label(__('filament.fields.image.image.name'))
                    ->required()
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([null, '2:3'])
                    ->storeFiles(false),
            ])
            ->columns(1);
    }

    /**
     * Get the facets available for the action.
     *
     * @param  ImageFacet[]  $facets
     * @return static
     */
    public function facets(array $facets): static
    {
        $this->facets = $facets;

        return $this;
    }
}