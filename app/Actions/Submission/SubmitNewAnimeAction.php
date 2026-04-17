<?php

declare(strict_types=1);

namespace App\Actions\Submission;

use App\Enums\Models\User\SubmissionComparisonAction;
use App\Enums\Models\User\SubmissionStatus;
use App\Filament\Resources\User\SubmissionResource;
use App\Models\Auth\User;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionAnime;
use App\Models\User\Submission\SubmissionComparison;
use App\Models\User\Submission\SubmissionSeries;
use App\Models\User\Submission\SubmissionStudio;
use App\Models\User\Submission\SubmissionSynonym;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Series;
use App\Models\Wiki\Studio;
use Exception;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubmitNewAnimeAction
{
    public function handle(User $user, array $data): void
    {
        try {
            DB::beginTransaction();

            $submission = Submission::query()->create([
                Submission::ATTRIBUTE_SOURCE => Arr::get($data, Submission::ATTRIBUTE_SOURCE),
                Submission::ATTRIBUTE_STATUS => SubmissionStatus::PENDING,
                Submission::ATTRIBUTE_USER => $user->getKey(),
            ]);

            $anime = SubmissionAnime::query()->create(Arr::get($data, 'anime'));

            $submission->submitted()->associate($anime);

            $this->handleSynonyms($submission, $data);

            // TODO: handle themes.

            $this->handleRelation($submission, $data, SubmissionAnime::RELATION_SERIES, SubmissionSeries::class, Series::class);
            $this->handleRelation($submission, $data, SubmissionAnime::RELATION_RESOURCES, SubmissionResource::class, ExternalResource::class);
            $this->handleRelation($submission, $data, SubmissionAnime::RELATION_STUDIOS, SubmissionStudio::class, Studio::class);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());

            throw $e;
        }
    }

    protected function handleSynonyms(Submission $submission, array $data): void
    {
        foreach (Arr::get($data, SubmissionAnime::RELATION_SYNONYMS) as $synonym) {
            $submissionSynonym = SubmissionSynonym::query()->create($synonym);

            $submissionSynonym->submission()->attach([
                $submission->getKey() => [
                    SubmissionComparison::ATTRIBUTE_ACTION => SubmissionComparisonAction::CREATE,
                ],
            ]);
        }
    }

    protected function handleRelation(Submission $submission, array $data, string $relation, string $submissionModel, string $model): void
    {
        $list = Arr::get($data, $relation, []);

        foreach ($list as $item) {
            $type = Arr::string($item, 'type');

            if ($type === 'create') {
                SubmissionComparison::query()->create([
                    SubmissionComparison::ATTRIBUTE_ACTION => SubmissionComparisonAction::CREATE,
                    SubmissionComparison::ATTRIBUTE_SUBMISSION => $submission->getKey(),
                    SubmissionComparison::ATTRIBUTE_SUBMITTED_TYPE => Relation::getMorphAlias($submissionModel),
                    SubmissionComparison::ATTRIBUTE_SUBMITTED_ID => $submissionModel::query()->create($item['data'])->getKey(),
                ]);
            }

            if ($type === 'attach') {
                SubmissionComparison::query()->create([
                    SubmissionComparison::ATTRIBUTE_ACTION => SubmissionComparisonAction::ATTACH,
                    SubmissionComparison::ATTRIBUTE_SUBMISSION => $submission->getKey(),
                    SubmissionComparison::ATTRIBUTE_ACTIONABLE_TYPE => Relation::getMorphAlias($model),
                    SubmissionComparison::ATTRIBUTE_ACTIONABLE_ID => $item['data']['id'],
                ]);
            }
        }
    }
}
