<?php

declare(strict_types=1);

namespace App\Nova\Actions\Wiki\Studio;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use App\Pipes\BasePipe;
use App\Pipes\Wiki\Studio\Image\BackfillLargeCoverImage;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Http\Requests\NovaRequest;

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
        return __('nova.backfill_studio');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Studio>  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models): mixed
    {
        foreach ($models as $studio) {
            if ($studio->resources()->doesntExist()) {
                $this->markAsFailed($studio, 'At least one Resource is required to backfill Studio');
                continue;
            }

            $pipes = $this->getPipes($fields, $studio);

            $pipeline = new Pipeline(Container::getInstance());

            try {
                $pipeline->send($this->user)
                    ->through($pipes)
                    ->then(fn () => $this->markAsFinished($studio));
            } catch (Exception $e) {
                $this->markAsFailed($studio, $e);
            } finally {
                // Try not to upset third-party APIs
                sleep(rand(3, 5));
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
        $studio = $request->resourceId !== null
            ? $request->findModel()
            : null;

        return [
            Heading::make(__('nova.backfill_images')),

            Boolean::make(__('nova.backfill_large_cover'), self::BACKFILL_LARGE_COVER)
                ->help(__('nova.backfill_studio_large_cover_help'))
                ->default(fn () => $studio instanceof Studio && $studio->images()->where(Image::ATTRIBUTE_FACET, ImageFacet::COVER_LARGE)->doesntExist()),
        ];
    }

    /**
     * Get the selected pipes for backfilling studios.
     *
     * @param  ActionFields  $fields
     * @param  Studio  $studio
     * @return BasePipe[]
     */
    protected function getPipes(ActionFields $fields, Studio $studio): array
    {
        $pipes = [];

        foreach ($this->getPipeMapping($studio) as $field => $pipe) {
            if (Arr::get($fields, $field) === true) {
                $pipes[] = $pipe;
            }
        }

        return $pipes;
    }

    /**
     * Get the mapping of studio pipes to their form fields.
     *
     * @param  Studio  $studio
     * @return array<string, BasePipe>
     */
    protected function getPipeMapping(Studio $studio): array
    {
        return [
            self::BACKFILL_LARGE_COVER => new BackfillLargeCoverImage($studio),
        ];
    }
}
