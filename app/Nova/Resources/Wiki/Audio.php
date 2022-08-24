<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Audio as AudioModel;
use App\Nova\Actions\Wiki\Audio\DeleteAudioAction;
use App\Nova\Actions\Wiki\Audio\ReconcileAudioAction;
use App\Nova\Actions\Wiki\Audio\UploadAudioAction;
use App\Nova\Lenses\Audio\AudioVideoLens;
use App\Nova\Resources\BaseResource;
use Exception;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Audio.
 */
class Audio extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = AudioModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = AudioModel::ATTRIBUTE_FILENAME;

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
    {
        return __('nova.wiki');
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function label(): string
    {
        return __('nova.audios');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function singularLabel(): string
    {
        return __('nova.audio');
    }

    /**
     * Get the searchable columns for the resource.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function searchableColumns(): array
    {
        return [
            new Column(AudioModel::ATTRIBUTE_FILENAME),
        ];
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @throws Exception
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.id'), AudioModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            HasMany::make(__('nova.videos'), AudioModel::RELATION_VIDEOS, Video::class),

            Panel::make(__('nova.file_properties'), $this->fileProperties())
                ->collapsable(),

            Panel::make(__('nova.timestamps'), $this->timestamps())
                ->collapsable(),
        ];
    }

    /**
     * File Properties Panel.
     *
     * @return array
     *
     * @throws Exception
     */
    protected function fileProperties(): array
    {
        return [
            Text::make(__('nova.basename'), AudioModel::ATTRIBUTE_BASENAME)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.filename'), AudioModel::ATTRIBUTE_FILENAME)
                ->sortable()
                ->copyable()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.path'), AudioModel::ATTRIBUTE_PATH)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),

            Number::make(__('nova.size'), AudioModel::ATTRIBUTE_SIZE)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.mimetype'), AudioModel::ATTRIBUTE_MIMETYPE)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable(),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request): array
    {
        return array_merge(
            parent::actions($request),
            [
                (new UploadAudioAction())
                    ->confirmButtonText(__('nova.upload'))
                    ->cancelButtonText(__('nova.cancel'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSee(function (Request $request) {
                        $user = $request->user();

                        return $user instanceof User && $user->can('create audio');
                    }),

                (new DeleteAudioAction())
                    ->confirmText(__('nova.remove_audio_text'))
                    ->confirmButtonText(__('nova.remove'))
                    ->cancelButtonText(__('nova.cancel'))
                    ->exceptOnIndex()
                    ->canSee(function (Request $request) {
                        $user = $request->user();

                        return $user instanceof User && $user->can('delete audio');
                    }),

                (new ReconcileAudioAction())
                    ->confirmButtonText(__('nova.reconcile'))
                    ->cancelButtonText(__('nova.cancel'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSee(function (Request $request) {
                        $user = $request->user();

                        return $user instanceof User && $user->can('create audio');
                    }),
            ]
        );
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request): array
    {
        return array_merge(
            parent::lenses($request),
            [
                new AudioVideoLens(),
            ]
        );
    }
}
