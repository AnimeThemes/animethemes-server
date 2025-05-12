<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Models\Wiki\Image;

use App\Actions\Models\Wiki\AttachImageAction as AttachImageActionAction;
use App\Contracts\Models\HasImages;
use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\TableActions\BaseTableAction;
use App\Models\BaseModel;
use App\Models\Wiki\Image;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;

/**
 * Class AttachImageTableAction.
 */
class AttachImageTableAction extends BaseTableAction
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

        $this->visible(function ($livewire) {
            if (Auth::user()->cannot('create', Image::class)) {
                return false;
            }

            return $livewire instanceof BaseRelationManager && $livewire->getOwnerRecord() instanceof HasImages;
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
        $action = new AttachImageActionAction();

        /** @var BaseRelationManager $livewire */
        $livewire = $this->getLivewire();

        /** @var BaseModel&HasImages $model */
        $model = $livewire->getOwnerRecord();

        $action->handle($model, $fields, $this->facets);
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

        foreach ($this->facets as $facet) {
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
