<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable(Submission::TABLE)) {
            Schema::create(Submission::TABLE, function (Blueprint $table) {
                $table->id(Submission::ATTRIBUTE_ID);

                $table->nullableUuidMorphs(Submission::RELATION_ACTIONABLE);
                $table->string(Submission::ATTRIBUTE_TYPE);
                $table->longText(Submission::ATTRIBUTE_MODERATOR_NOTES)->nullable();

                $table->integer(Submission::ATTRIBUTE_STATUS)->nullable();

                $table->unsignedBigInteger(Submission::ATTRIBUTE_USER)->nullable();
                $table->foreign(Submission::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->nullOnDelete();

                $table->unsignedBigInteger(Submission::ATTRIBUTE_MODERATOR)->nullable();
                $table->foreign(Submission::ATTRIBUTE_MODERATOR)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->nullOnDelete();

                $table->boolean(Submission::ATTRIBUTE_LOCKED)->default(false);
                $table->timestamp(Submission::ATTRIBUTE_FINISHED_AT, 6)->nullable();
                $table->timestamps(6);

                $table->index(Submission::ATTRIBUTE_STATUS);
            });
        }

        if (! Schema::hasTable(SubmissionStage::TABLE)) {
            Schema::create(SubmissionStage::TABLE, function (Blueprint $table) {
                $table->id(SubmissionStage::ATTRIBUTE_ID);

                $table->integer(SubmissionStage::ATTRIBUTE_STAGE);

                $table->json(SubmissionStage::ATTRIBUTE_FIELDS)->nullable();
                $table->longText(SubmissionStage::ATTRIBUTE_NOTES)->nullable();
                $table->longText(Submission::ATTRIBUTE_MODERATOR_NOTES)->nullable();

                $table->unsignedBigInteger(SubmissionStage::ATTRIBUTE_SUBMISSION);
                $table->foreign(SubmissionStage::ATTRIBUTE_SUBMISSION)->references(Submission::ATTRIBUTE_ID)->on(Submission::TABLE)->cascadeOnDelete();

                $table->unsignedBigInteger(SubmissionStage::ATTRIBUTE_MODERATOR)->nullable();
                $table->foreign(SubmissionStage::ATTRIBUTE_MODERATOR)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->nullOnDelete();

                $table->timestamps(6);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(SubmissionStage::TABLE);
        Schema::dropIfExists(Submission::TABLE);
    }
};
