<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\External;

use App\Actions\Models\List\ExternalTokenCallbackAction;
use App\Features\AllowExternalProfileManagement;
use App\Http\Api\Schema\List\ExternalProfileSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Models\List\External\ExternalToken;
use App\Models\List\ExternalProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

/**
 * Class ExternalTokenCallbackController.
 */
class ExternalTokenCallbackController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(ExternalToken::class, 'externaltoken');

        $isExternalProfileManagementAllowed = Str::of(EnsureFeaturesAreActive::class)
            ->append(':')
            ->append(AllowExternalProfileManagement::class)
            ->__toString();

        $this->middleware($isExternalProfileManagementAllowed);
    }

    /**
     * This is the redirect URL which is set in the external provider.
     *
     * @param  IndexRequest  $request
     * @return RedirectResponse|JsonResponse
     */
    public function index(IndexRequest $request): RedirectResponse|JsonResponse
    {
        $validated = array_merge(
            $request->all(),
            [ExternalProfile::ATTRIBUTE_USER => Auth::id()]
        );

        $action = new ExternalTokenCallbackAction();

        $response = $action->store($validated);

        if (!($response instanceof ExternalProfile)) {
            return $response;
        }

        // https://animethemes.moe/external/{mal|anilist}/{profile_name}
        $clientUrl = Str::of(Config::get('wiki.external_profile'))
            ->append('/')
            ->append(Str::lower($response->site->name))
            ->append('/')
            ->append($response->getName())
            ->__toString();

        return Redirect::to($clientUrl);
    }

    /**
     * Get the underlying schema.
     *
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function schema(): Schema
    {
        return new ExternalProfileSchema();
    }
}
