<?php

declare(strict_types=1);

namespace App\Nova\Resources\Wiki\Video;

use App\Models\Wiki\Video\VideoScript;
use App\Nova\Actions\Repositories\Storage\Wiki\Video\Script\ReconcileScriptAction;
use App\Nova\Actions\Storage\Wiki\Video\Script\DeleteScriptAction;
use App\Nova\Actions\Storage\Wiki\Video\Script\MoveScriptAction;
use App\Nova\Actions\Storage\Wiki\Video\Script\UploadScriptAction;
use App\Nova\Resources\BaseResource;
use App\Nova\Resources\Wiki\Video;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Script.
 */
class Script extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = VideoScript::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = VideoScript::ATTRIBUTE_PATH;

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
        return __('nova.resources.label.video_scripts');
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
        return __('nova.resources.singularLabel.video_script');
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function uriKey(): string
    {
        return 'video-scripts';
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
            new Column(VideoScript::ATTRIBUTE_PATH),
        ];
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  NovaRequest  $request
     * @param  Builder  $query
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->with(VideoScript::RELATION_VIDEO);
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
            BelongsTo::make(__('nova.resources.singularLabel.video'), VideoScript::RELATION_VIDEO, Video::class)
                ->sortable()
                ->filterable()
                ->searchable()
                ->nullable()
                ->showOnPreview(),

            ID::make(__('nova.fields.base.id'), VideoScript::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview()
                ->showWhenPeeking(),

            Text::make(__('nova.fields.video_script.path'), VideoScript::ATTRIBUTE_PATH)
                ->copyable()
                ->showOnPreview()
                ->filterable()
                ->maxlength(192)
                ->enforceMaxlength()
                ->showWhenPeeking(),

            Panel::make(__('nova.fields.base.timestamps'), $this->timestamps()),
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
                (new UploadScriptAction())
                    ->confirmButtonText(__('nova.actions.storage.upload.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSeeWhen('create', VideoScript::class),

                (new MoveScriptAction())
                    ->confirmButtonText(__('nova.actions.storage.move.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->exceptOnIndex()
                    ->canSeeWhen('create', VideoScript::class),

                (new DeleteScriptAction())
                    ->confirmText(__('nova.actions.video_script.delete.confirmText'))
                    ->confirmButtonText(__('nova.actions.storage.delete.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->exceptOnIndex()
                    ->canSeeWhen('delete', $this),

                (new ReconcileScriptAction())
                    ->confirmButtonText(__('nova.actions.repositories.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSeeWhen('create', VideoScript::class),
            ],
        );
    }
}
