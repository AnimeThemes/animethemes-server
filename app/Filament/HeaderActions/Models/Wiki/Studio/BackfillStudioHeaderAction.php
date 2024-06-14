<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Studio;

use App\Actions\Models\BackfillAction;
use App\Actions\Models\Wiki\Studio\Image\BackfillLargeCoverImageAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Filament\HeaderActions\BaseHeaderAction;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Sleep;

/**
 * Class BackfillStudioHeaderAction.
 */
class BackfillStudioHeaderAction extends BaseHeaderAction implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    final public const BACKFILL_LARGE_COVER = 'backfill_large_cover';

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.studio.backfill.name'));

        $this->modalWidth(MaxWidth::FourExtraLarge);

        $this->authorize('update', Studio::class);

        $this->action(fn (Studio $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given models.
     *
     * @param  Studio  $studio
     * @param  array  $fields
     * @return void
     */
    public function handle(Studio $studio, array $fields): void
    {
        if ($studio->resources()->doesntExist()) {
            //$this->markAsFailed($studio, __('filament.actions.studio.backfill.message.resource_required_failure'));
            return;
        }

        $actions = $this->getActions($fields, $studio);

        try {
            foreach ($actions as $action) {
                $result = $action->handle();
                if ($result->hasFailed()) {
                    Notification::make()
                        ->body($result->getMessage())
                        ->warning()
                        ->actions([
                            NotificationAction::make('mark-as-read')
                                ->button()
                                ->markAsRead(),
                        ])
                        ->sendToDatabase(auth()->user());
                }
            }
        } catch (Exception $e) {
            //$this->markAsFailed($studio, $e);
        } finally {
            // Try not to upset third-party APIs
            Sleep::for(rand(3, 5))->second();
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getForm(Form $form): Form
    {
        $studio = $this->getRecord();

        return $form
            ->schema([
                Section::make(__('filament.actions.studio.backfill.fields.images.name'))
                    ->schema([
                        Checkbox::make(self::BACKFILL_LARGE_COVER)
                            ->label(__('filament.actions.studio.backfill.fields.images.large_cover.name'))
                            ->helperText(__('filament.actions.studio.backfill.fields.images.large_cover.help'))
                            ->default(fn () => $studio instanceof Studio && $studio->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE->value)->doesntExist()),
                    ]),
            ]);
    }

    /**
     * Get the selected actions for backfilling studios.
     *
     * @param  array  $fields
     * @param  Studio  $studio
     * @return BackfillAction[]
     */
    protected function getActions(array $fields, Studio $studio): array
    {
        $actions = [];

        foreach ($this->getActionMapping($studio) as $field => $action) {
            if (Arr::get($fields, $field) === true) {
                $actions[] = $action;
            }
        }

        return $actions;
    }

    /**
     * Get the mapping of actions to their form fields.
     *
     * @param  Studio  $studio
     * @return array<string, BackfillAction>
     */
    protected function getActionMapping(Studio $studio): array
    {
        return [
            self::BACKFILL_LARGE_COVER => new BackfillLargeCoverImageAction($studio),
        ];
    }
}
