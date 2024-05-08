<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Models\Wiki\Image;

use App\Actions\Models\Wiki\UploadImageAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Illuminate\Validation\Rules\Enum;

/**
 * Class UploadImageTableAction.
 */
class UploadImageTableAction extends Action
{
    protected array $facets = [];

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (array $data) => (new UploadImageAction())->handle($data));
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