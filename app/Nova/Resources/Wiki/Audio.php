<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki;

use App\Models\Wiki\Audio as AudioModel;
use App\Nova\Actions\Repositories\Storage\Wiki\Audio\ReconcileAudioAction;
use App\Nova\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Nova\Actions\Storage\Wiki\Audio\MoveAudioAction;
use App\Nova\Actions\Storage\Wiki\Audio\UploadAudioAction;
use App\Nova\Lenses\Audio\AudioVideoLens;
use App\Nova\Resources\BaseResource;
use Exception;
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
        return __('nova.resources.group.wiki');
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
        return __('nova.resources.label.audios');
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
        return __('nova.resources.singularLabel.audio');
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
            ID::make(__('nova.fields.base.id'), AudioModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            HasMany::make(__('nova.resources.label.videos'), AudioModel::RELATION_VIDEOS, Video::class),

            Panel::make(__('nova.fields.base.file_properties'), $this->fileProperties())
                ->collapsable(),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps())
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
            Text::make(__('nova.fields.audio.basename.name'), AudioModel::ATTRIBUTE_BASENAME)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.audio.filename.name'), AudioModel::ATTRIBUTE_FILENAME)
                ->sortable()
                ->copyable()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.audio.path.name'), AudioModel::ATTRIBUTE_PATH)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Number::make(__('nova.fields.audio.size.name'), AudioModel::ATTRIBUTE_SIZE)
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.audio.mimetype.name'), AudioModel::ATTRIBUTE_MIMETYPE)
                ->copyable()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->showOnPreview()
                ->filterable()
                ->showWhenPeeking(),
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
                    ->confirmButtonText(__('nova.actions.storage.upload.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSeeWhen('create', AudioModel::class),

                (new MoveAudioAction())
                    ->confirmButtonText(__('nova.actions.storage.move.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->exceptOnIndex()
                    ->canSeeWhen('create', AudioModel::class),

                (new DeleteAudioAction())
                    ->confirmText(__('nova.actions.audio.delete.confirmText'))
                    ->confirmButtonText(__('nova.actions.storage.delete.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->exceptOnIndex()
                    ->canSeeWhen('delete', $this),

                (new ReconcileAudioAction())
                    ->confirmButtonText(__('nova.actions.repositories.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSeeWhen('create', AudioModel::class),
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
