<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions\Models\Wiki\Studio;

use App\Actions\Models\Wiki\BackfillStudioAction as BackfillStudioActionAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Sleep;

/**
 * Trait BackfillStudioActionTrait.
 */
trait BackfillStudioActionTrait
{
    use InteractsWithQueue;
    use Queueable;

    final public const IMAGES = BackfillStudioActionAction::IMAGES;

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
            $this->failedLog(__('filament.actions.studio.backfill.message.resource_required_failure'));
            return;
        }

        $action = new BackfillStudioActionAction($studio, $this->getToBackfill($fields));

        try {
            $result = $action->handle();
            // if ($result->hasFailed()) {
            //     Notification::make()
            //         ->body($result->getMessage())
            //         ->warning()
            //         ->actions([
            //             NotificationAction::make('mark-as-read')
            //                 ->button()
            //                 ->markAsRead(),
            //         ])
            //         ->sendToDatabase(Auth::user());
            // }
        } catch (Exception $e) {
            $this->failedLog($e);
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
     * Get what should be backfilled.
     *
     * @param  array  $fields
     * @return array
     */
    protected function getToBackfill(array $fields): array
    {
        $toBackfill = [];
        $toBackfill[self::IMAGES] = [];

        foreach ($this->getImagesMapping() as $field => $facets) {
            if (Arr::get($fields, $field) === true) {
                $toBackfill[self::IMAGES] = array_merge($toBackfill[self::IMAGES], $facets);
            }
        }

        return $toBackfill;
    }

    /**
     * Get the images for mapping.
     *
     * @return array
     */
    protected function getImagesMapping(): array
    {
        return [
            self::BACKFILL_LARGE_COVER => [ImageFacet::COVER_LARGE],
        ];
    }
}
