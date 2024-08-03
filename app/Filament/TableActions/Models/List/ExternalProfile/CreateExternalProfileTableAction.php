<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Models\List\ExternalProfile;

use App\Actions\Models\List\ExternalProfile\StoreExternalProfileAction;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Filament\Components\Fields\Select;
use App\Filament\TableActions\BaseTableAction;
use App\Models\List\ExternalProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;

/**
 * Class CreateExternalProfileTableAction.
 */
class CreateExternalProfileTableAction extends BaseTableAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.models.list.external_profile.create.name'));

        $this->authorize('create', ExternalProfile::class);
    }

    /**
     * Perform the action on the table.
     *
     * @param  array  $fields
     * @return void
     */
    public function handle(array $fields): void
    {
        $name = Arr::get($fields, ExternalProfile::ATTRIBUTE_NAME);
        $site = Arr::get($fields, ExternalProfile::ATTRIBUTE_SITE);
        $visibility = Arr::get($fields, ExternalProfile::ATTRIBUTE_VISIBILITY);

        $action = new StoreExternalProfileAction();

        $action->store(ExternalProfile::query(), [
            ExternalProfile::ATTRIBUTE_USER => Filament::auth()->id(),
            ExternalProfile::ATTRIBUTE_NAME => $name,
            ExternalProfile::ATTRIBUTE_SITE => ExternalProfileSite::from(intval($site))->localize(),
            ExternalProfile::ATTRIBUTE_VISIBILITY => ExternalProfileVisibility::from(intval($visibility))->localize(),
        ]);
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     */
    public function getForm(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(ExternalProfile::ATTRIBUTE_NAME)
                    ->label(__('filament.fields.external_profile.name.name'))
                    ->helperText(__('filament.fields.external_profile.name.help'))
                    ->required()
                    ->rules(['required']),

                Select::make(ExternalProfile::ATTRIBUTE_SITE)
                    ->label(__('filament.fields.external_profile.site.name'))
                    ->helperText(__('filament.fields.external_profile.site.help'))
                    ->options(ExternalProfileSite::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(ExternalProfileSite::class)]),

                Select::make(ExternalProfile::ATTRIBUTE_VISIBILITY)
                    ->label(__('filament.fields.external_profile.visibility.name'))
                    ->helperText(__('filament.fields.external_profile.visibility.help'))
                    ->options(ExternalProfileVisibility::asSelectArray())
                    ->required()
                    ->rules(['required', new Enum(ExternalProfileVisibility::class)]),
            ]);
    }
}
