<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Models\Wiki;

use App\Actions\Models\Wiki\AttachImageAction as AttachImageActionAction;
use App\Contracts\Models\HasImages;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\BaseModel;
use App\Models\Wiki\Image;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;

/**
 * Trait AttachImageActionTrait.
 */
trait AttachImageActionTrait
{
    protected array $facets = [
        ImageFacet::COVER_SMALL,
        ImageFacet::COVER_LARGE,
    ];

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.wiki.attach_image.name'));
        $this->icon(__('filament-icons.actions.models.wiki.attach_image'));

        $this->authorize('create', Image::class);

        $this->action(fn (BaseModel&HasImages $record, array $data, AttachImageActionAction $attachImage) => $attachImage->handle($record, $data, $this->facets));
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     */
    public function getForm(Form $form): Form
    {
        $fields = [];
        $model = $form->getRecord();

        if (!($model instanceof HasImages)) return $form;

        $images = $model->images()
            ->get([Image::ATTRIBUTE_FACET])
            ->pluck(Image::ATTRIBUTE_FACET)
            ->keyBy(fn (ImageFacet $facet) => $facet->value)
            ->keys();

        foreach ($this->facets as $facet) {
            if ($images->contains($facet->value)) continue;

            $fields[] = FileUpload::make($facet->name)
                ->label($facet->localize())
                ->helperText(__('filament.actions.models.wiki.attach_image.help'))
                ->imageCropAspectRatio('2:3')
                ->image()
                ->imageEditor()
                ->imageEditorAspectRatios([null, '2:3'])
                ->storeFiles(false);
        }

        return $form
            ->schema($fields);
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
