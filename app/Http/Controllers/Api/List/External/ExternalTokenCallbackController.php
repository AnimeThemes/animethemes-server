<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\External;

use App\Actions\Models\List\ExternalProfile\StoreExternalTokenAction;
use App\Features\AllowExternalProfileManagement;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Models\List\External\ExternalToken;
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

        $this->middleware($isExternalProfileManagementAllowed)->except(['index', 'show']);
    }

    /**
     * This is the redirect URL which is set in the external provider.
     *
     * @param  IndexRequest  $request
     */
    public function index(IndexRequest $request)
    {
        $query = new Query($request->validated());

        $action = new StoreExternalTokenAction();

        $action->store($query); // This stores the external token.

        // TODO: We should find or create a profile with the entries.

        // TODO: Then, the user should be redirect to the client page, e.g. /external/{site}/{profile}.
    }
}
