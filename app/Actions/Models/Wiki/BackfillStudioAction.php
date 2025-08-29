<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki;

use App\Actions\ActionResult;
use App\Actions\Models\BackfillWikiAction;
use App\Actions\Models\Wiki\Studio\ExternalApi\MalStudioExternalApiAction;
use App\Contracts\Actions\Models\Wiki\BackfillImages;
use App\Enums\Actions\ActionStatus;
use App\Models\Wiki\Studio;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillStudioAction extends BackfillWikiAction
{
    /**
     * @param  array  $toBackfill
     */
    public function __construct(protected Studio $studio, protected array $toBackfill)
    {
        parent::__construct($studio, $toBackfill);
    }

    public function handle(): ActionResult
    {
        foreach ($this->getExternalApiActions() as $api) {
            try {
                DB::beginTransaction();

                if (count($this->toBackfill[self::IMAGES]) === 0) {
                    // Don't make other requests if everything is backfilled
                    Log::info("Backfill action finished for Studio {$this->getModel()->getName()}");
                    DB::rollBack();

                    return new ActionResult(ActionStatus::SKIPPED);
                }

                $response = $api->handle($this->getModel()->resources());

                if ($api instanceof BackfillImages) {
                    $this->forImages($response);
                }

                DB::commit();
            } catch (Exception $e) {
                Log::error($e->getMessage());

                DB::rollBack();

                throw $e;
            }
        }

        return new ActionResult(ActionStatus::PASSED);
    }

    /**
     * Get the external API actions available for the backfill action.
     *
     * @return ExternalApiAction[]
     */
    protected function getExternalApiActions(): array
    {
        return [
            new MalStudioExternalApiAction(),
        ];
    }

    /**
     * Get the model for the action.
     */
    protected function getModel(): Studio
    {
        return $this->studio;
    }
}
