<?php

declare(strict_types=1);

namespace App\Nova\Resources\Admin;

use App\Models\Admin\Dump as DumpModel;
use App\Models\Auth\User;
use App\Nova\Actions\Repositories\Storage\Admin\Dump\ReconcileDumpAction;
use App\Nova\Actions\Storage\Admin\DumpDocumentAction;
use App\Nova\Actions\Storage\Admin\DumpWikiAction;
use App\Nova\Actions\Storage\Admin\PruneDumpAction;
use App\Nova\Resources\BaseResource;
use Exception;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Query\Search\Column;

/**
 * Class Dump.
 */
class Dump extends BaseResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static string $model = DumpModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = DumpModel::ATTRIBUTE_PATH;

    /**
     * The logical group associated with the resource.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function group(): string
    {
        return __('nova.resources.group.admin');
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
        return __('nova.resources.label.dumps');
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
        return __('nova.resources.singularLabel.dump');
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
            new Column(DumpModel::ATTRIBUTE_PATH),
        ];
    }

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Determine if this resource uses Laravel Scout.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function usesScout(): bool
    {
        return false;
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
            ID::make(__('nova.fields.base.id'), DumpModel::ATTRIBUTE_ID)
                ->sortable()
                ->showOnPreview(),

            Text::make(__('nova.fields.dump.path'), DumpModel::ATTRIBUTE_PATH)
                ->copyable()
                ->showOnPreview()
                ->filterable(),

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
                (new DumpWikiAction())
                    ->confirmButtonText(__('nova.actions.dump.dump.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSee(function (Request $request) {
                        $user = $request->user();

                        return $user instanceof User && $user->can('create dump');
                    }),

                (new DumpDocumentAction())
                    ->confirmButtonText(__('nova.actions.dump.dump.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSee(function (Request $request) {
                        $user = $request->user();

                        return $user instanceof User && $user->can('create dump');
                    }),

                (new PruneDumpAction())
                    ->confirmButtonText(__('nova.actions.storage.prune.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSee(function (Request $request) {
                        $user = $request->user();

                        return $user instanceof User && $user->can('delete dump');
                    }),

                (new ReconcileDumpAction())
                    ->confirmButtonText(__('nova.actions.repositories.confirmButtonText'))
                    ->cancelButtonText(__('nova.actions.base.cancelButtonText'))
                    ->onlyOnIndex()
                    ->standalone()
                    ->canSee(function (Request $request) {
                        $user = $request->user();

                        return $user instanceof User && $user->can('create dump');
                    }),
            ]
        );
    }
}
