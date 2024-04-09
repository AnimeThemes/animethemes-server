<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Studio;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Actions\Action;

/**
 * Class AttachImageHeaderAction.
 */
abstract class AttachImageHeaderAction extends Action
{
    protected array $facets = [];

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

        if ($model instanceof Anime || $model instanceof Artist || $model instanceof Studio) {
            foreach ($this->facets as $facet) {
                $images = $model->images();
               // if ($images->where(Image::ATTRIBUTE_FACET, $facet->value)->exists()) continue;

                $fields[] = FileUpload::make($facet->name)
                    ->label($facet->localize())
                    ->image();
            }
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
