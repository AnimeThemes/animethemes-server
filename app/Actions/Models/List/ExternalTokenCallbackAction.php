<?php

declare(strict_types=1);

namespace App\Actions\Models\List;

use App\Actions\Models\List\ExternalProfile\StoreExternalProfileTokenAction;
use App\Actions\Models\List\ExternalProfile\StoreExternalTokenAction;
use App\Models\List\ExternalProfile;
use Error;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ExternalTokenCallbackAction.
 */
class ExternalTokenCallbackAction
{
    /**
     * We should store the token, the profile and its entries.
     *
     * @param  array  $parameters
     * @return JsonResponse|ExternalProfile
     *
     * @throws Exception
     */
    public function store(array $parameters): JsonResponse|ExternalProfile
    {
        try {
            DB::beginTransaction();

            $action = new StoreExternalTokenAction();

            $externalToken = $action->store($parameters);

            if ($externalToken === null) {
                throw new Error('Invalid Code', 400);
            }

            $profileAction = new StoreExternalProfileTokenAction();

            $profile = $profileAction->findOrCreate($externalToken, $parameters);

            DB::commit();

            return $profile;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            DB::rollBack();

            return new JsonResponse([
                'error' => $e->getMessage(),
            ], $e->getCode());
        }
    }
}
