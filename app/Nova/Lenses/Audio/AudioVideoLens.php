<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Audio;

use App\Models\BaseModel;
use App\Models\Wiki\Audio;
use App\Nova\Actions\Storage\Wiki\Audio\DeleteAudioAction;
use App\Nova\Lenses\BaseLens;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class AudioVideoLens.
 */
class AudioVideoLens extends BaseLens
{
    /**
     * Get the displayable name of the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.lenses.audio.video.name');
    }

    /**
     * The criteria used to refine the models for the lens.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public static function criteria(Builder $query): Builder
    {
        return $query->whereDoesntHave(Audio::RELATION_VIDEOS);
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @throws Exception
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('nova.fields.base.id'), Audio::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.fields.audio.basename.name'), Audio::ATTRIBUTE_BASENAME)
                ->onlyOnPreview(),

            Text::make(__('nova.fields.audio.filename.name'), Audio::ATTRIBUTE_FILENAME)
                ->sortable()
                ->copyable()
                ->showOnPreview()
                ->filterable(),

            Text::make(__('nova.fields.audio.path.name'), Audio::ATTRIBUTE_PATH)
                ->onlyOnPreview(),

            Number::make(__('nova.fields.audio.size.name'), Audio::ATTRIBUTE_SIZE)
                ->onlyOnPreview(),

            Text::make(__('nova.fields.audio.mimetype.name'), Audio::ATTRIBUTE_MIMETYPE)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.created_at'), BaseModel::ATTRIBUTE_CREATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.updated_at'), BaseModel::ATTRIBUTE_UPDATED_AT)
                ->onlyOnPreview(),

            DateTime::make(__('nova.fields.base.deleted_at'), BaseModel::ATTRIBUTE_DELETED_AT)
                ->onlyOnPreview(),
        ];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  NovaRequest  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function actions(NovaRequest $request): array
    {
        return [
            (new DeleteAudioAction())
                ->confirmText(__('nova.actions.audio.delete.confirmText'))
                ->confirmButtonText(__('nova.actions.audio.delete.confirmButtonText'))
                ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                ->exceptOnIndex()
                ->canSeeWhen('delete', Audio::class),
        ];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function uriKey(): string
    {
        return 'video-audio-lens';
    }
}
