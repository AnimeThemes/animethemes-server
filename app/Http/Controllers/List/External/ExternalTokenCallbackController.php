<?php

declare(strict_types=1);

namespace App\Http\Controllers\List\External;

use App\Actions\Models\List\ExternalTokenCallbackAction;
use App\Features\AllowExternalProfileManagement;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Jobs\List\SyncExternalProfileJob;
use App\Models\List\ExternalProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

/**
 * Class ExternalTokenCallbackController.
 */
class ExternalTokenCallbackController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $isExternalProfileManagementAllowed = Str::of(EnsureFeaturesAreActive::class)
            ->append(':')
            ->append(AllowExternalProfileManagement::class)
            ->__toString();

        $this->middleware(EnabledOnlyOnLocalhost::class);
        $this->middleware($isExternalProfileManagementAllowed);
    }

    /**
     * This is the redirect URL which is set in the external provider.
     *
     * @param  Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function index(Request $request): RedirectResponse|JsonResponse
    {
        $validated = array_merge(
            $request->all(),
            [ExternalProfile::ATTRIBUTE_USER => Auth::id()]
        );

        $action = new ExternalTokenCallbackAction();

        $profile = $action->store($validated);

        $profile->startSyncEntriesJob();

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
