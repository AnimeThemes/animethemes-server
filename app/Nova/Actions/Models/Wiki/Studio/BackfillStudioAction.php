<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Wiki\Studio;

use App\Actions\Models\BackfillAction;
use App\Actions\Models\Wiki\Studio\Image\BackfillLargeCoverImageAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Nova\Resources\Wiki\Studio as StudioResource;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Sleep;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Notifications\NovaNotification;

/**
 * Class BackfillStudioAction.
 */
class BackfillStudioAction extends Action implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    final public const BACKFILL_LARGE_COVER = 'backfill_large_cover';

    /**
     * Create a new action instance.
     *
     * @param  User  $user
     */
    public function __construct(protected User $user)
    {
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.actions.studio.backfill.name');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Studio>  $models
     * @return Collection
     */
    public function handle(ActionFields $fields, Collection $models): Collection
    {
        $uriKey = StudioResource::uriKey();

        foreach ($models as $studio) {
            if ($studio->resources()->doesntExist()) {
                $this->markAsFailed($studio, __('nova.actions.studio.backfill.message.resource_required_failure'));
                continue;
            }

            $actions = $this->getActions($fields, $studio);

            try {
                foreach ($actions as $action) {
                    $result = $action->handle();
                    if ($result->hasFailed()) {
                        $this->user->notify(
                            NovaNotification::make()
                                ->icon('flag')
                                ->message($result->getMessage())
                                ->type(NovaNotification::WARNING_TYPE)
                                ->url("/resources/$uriKey/{$studio->getKey()}")
                        );
                    }
                }
            } catch (Exception $e) {
                $this->markAsFailed($studio, $e);
            } finally {
                // Try not to upset third-party APIs
                Sleep::for(rand(3, 5))->second();
            }
        }

        return $models;
    }

    /**
     * Get the fields available on the action.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(NovaRequest $request): array
    {
        $studio = $request->findModelQuery()->first();

        return [
            Heading::make(__('nova.actions.studio.backfill.fields.images.name')),

            Boolean::make(__('nova.actions.studio.backfill.fields.images.large_cover.name'), self::BACKFILL_LARGE_COVER)
                ->help(__('nova.actions.studio.backfill.fields.images.large_cover.help'))
                ->default(fn () => $studio instanceof Studio && $studio->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE->value)->doesntExist()),
        ];
    }

    /**
     * Get the selected actions for backfilling studios.
     *
     * @param  ActionFields  $fields
     * @param  Studio  $studio
     * @return BackfillAction[]
     */
    protected function getActions(ActionFields $fields, Studio $studio): array
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
