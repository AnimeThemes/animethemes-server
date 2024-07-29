<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki;

use App\Actions\Models\Wiki\AttachImageAction as AttachImageActionAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Actions\BaseAction;
use App\Models\BaseModel;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;

/**
 * Class AttachImageAction.
 */
class AttachImageAction extends BaseAction
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
        $this->icon('heroicon-o-photo');

        $this->authorize('create', Image::class);

        $this->action(fn (BaseModel $record, array $data) => (new AttachImageActionAction($record, $data, $this->facets))->handle());
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

        if ($model instanceof Anime || $model instanceof Artist || $model instanceof Studio) {
            foreach ($this->facets as $facet) {
                $images = $model->images();
                if ($images->where(Image::ATTRIBUTE_FACET, $facet->value)->exists()) continue;

                $fields[] = FileUpload::make($facet->name)
                    ->label($facet->localize())
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([null, '2:3'])
                    ->storeFiles(false);
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
