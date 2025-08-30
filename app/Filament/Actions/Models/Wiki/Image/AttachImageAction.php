<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Image;

use App\Actions\Models\Wiki\AttachImageAction as AttachImageActionAction;
use App\Contracts\Models\HasImages;
use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\Actions\BaseAction;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Models\BaseModel;
use App\Models\Wiki\Image;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class AttachImageAction extends BaseAction
{
    protected array $facets = [
        ImageFacet::SMALL_COVER,
        ImageFacet::LARGE_COVER,
    ];

    public static function getDefaultName(): ?string
    {
        return 'attach-image';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.wiki.attach_image.name'));
        $this->icon(Heroicon::OutlinedPhoto);

        $this->visible(function ($livewire) {
            if (Auth::user()->cannot('create', Image::class)) {
                return false;
            }

            return $livewire instanceof BaseRelationManager && $livewire->getOwnerRecord() instanceof HasImages;
        });

        $this->action(fn (array $data) => $this->handle($data));
    }

    /**
     * Perform the action on the table.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): void
    {
        $action = new AttachImageActionAction();

        /** @var BaseRelationManager $livewire */
        $livewire = $this->getLivewire();

        /** @var BaseModel&HasImages $model */
        $model = $livewire->getOwnerRecord();

        $action->handle($model, $data, $this->facets);
    }

    public function getSchema(Schema $schema): Schema
    {
        $components = [];

        foreach ($this->facets as $facet) {
            $components[] = FileUpload::make($facet->name)
                ->label($facet->localize())
                ->helperText(__('filament.actions.models.wiki.attach_image.help'))
                ->imageCropAspectRatio('2:3')
                ->image()
                ->imageEditor()
                ->imageEditorAspectRatios([null, '2:3'])
                ->storeFiles(false);
        }

        return $schema
            ->components($components);
    }

    /**
     * Get the facets available for the action.
     *
     * @param  ImageFacet[]  $facets
     */
    public function facets(array $facets): static
    {
        $this->facets = $facets;

        return $this;
    }
}
