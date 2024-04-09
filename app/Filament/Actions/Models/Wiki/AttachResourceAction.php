<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;

/**
 * Class AttachResourceAction.
 */
abstract class AttachResourceAction extends Action
{
    protected array $sites = [];

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form|null
     */
    public function getForm(Form $form): ?Form
    {
        $fields = [];
        $model = $this->getRecord();

        foreach ($this->sites as $resourceSite) {
            if ($model instanceof Anime || $model instanceof Artist || $model instanceof Song || $model instanceof Studio) {
                $resources = $model->resources();
                if ($resources->where(ExternalResource::ATTRIBUTE_SITE, $resourceSite->value)->exists()) continue;
            }
            
            $resourceSiteLower = strtolower($resourceSite->name);

            $fields[] = TextInput::make($resourceSite->name)
                            ->label($resourceSite->localize())
                            ->helperText(__("nova.actions.models.wiki.attach_resource.fields.{$resourceSiteLower}.help"));
        }

        return $form
            ->schema($fields);
    }

    /**
     * Get the sites available for the action.
     *
     * @param  ResourceSite[]  $sites
     * @return static
     */
    public function sites($sites): static
    {
        $this->sites = $sites;

        return $this;
    }
}
