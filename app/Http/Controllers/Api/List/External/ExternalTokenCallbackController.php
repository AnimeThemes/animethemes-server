<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\External;

use App\Actions\Models\List\ExternalProfile\StoreExternalProfileAction;
use App\Actions\Models\List\ExternalProfile\StoreExternalTokenAction;
use App\Features\AllowExternalProfileManagement;
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
            $request->validated(),
            [ExternalProfile::ATTRIBUTE_USER => Auth::id()]
        );

        $action = new StoreExternalTokenAction();

        $externalToken = $action->store($validated);

        if ($externalToken === null) {
            return new JsonResponse([
                'error' => 'invalid code',
            ], 400);
        }

        $profileAction = new StoreExternalProfileAction();

        $profile = $profileAction->findOrCreateForExternalToken($externalToken, $validated); 

        // https://animethemes.moe/external/{mal|anilist}/{profile_name}
        $clientUrl = Str::of(Config::get('wiki.external_profile'))
            ->append('/')
            ->append(Str::lower($profile->site->name))
            ->append('/')
            ->append($profile->getName())
            ->__toString();

        return Redirect::to($clientUrl);
    }
}
