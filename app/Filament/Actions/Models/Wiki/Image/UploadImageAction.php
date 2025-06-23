<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Image;

use Filament\Schemas\Schema;
use App\Concerns\Models\CanCreateImage;
use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Components\Fields\Select;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Actions\BaseAction;
use App\Models\Wiki\Image;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * Class UploadImageAction.
 */
class UploadImageAction extends BaseAction
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

        $this->visible(function ($livewire) {
            if (Auth::user()->cannot('create', Image::class)) {
                return false;
            }

            return $livewire instanceof BaseListResources;
        });
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
     * @param  Schema  $schema
     * @return Schema
     */
    public function getSchema(Schema $schema): Schema
    {
        $options = [];

        foreach ($this->facets as $facet) {
            $options[$facet->value] = $facet->localize();
        }

        return $schema
            ->components([
                Select::make(Image::ATTRIBUTE_FACET)
                    ->label(__('filament.fields.image.facet.name'))
                    ->helperText(__('filament.fields.image.facet.help'))
                    ->options($options)
                    ->required()
                    ->enum(ImageFacet::class),

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