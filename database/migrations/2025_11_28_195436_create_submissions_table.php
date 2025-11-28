<?php

declare(strict_types=1);

use App\Models\Auth\User;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStep;
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

                $table->longText(Submission::ATTRIBUTE_NOTES)->nullable();
                $table->longText(Submission::ATTRIBUTE_MOD_NOTES)->nullable();

                $table->integer(Submission::ATTRIBUTE_STATUS)->nullable();

                $table->unsignedBigInteger(Submission::ATTRIBUTE_USER)->nullable();
                $table->foreign(Submission::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->nullOnDelete();

                $table->unsignedBigInteger(Submission::ATTRIBUTE_MODERATOR)->nullable();
                $table->foreign(Submission::ATTRIBUTE_MODERATOR)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->nullOnDelete();

                $table->timestamp(Submission::ATTRIBUTE_FINISHED_AT, 6)->nullable();
                $table->timestamps(6);

                $table->index(Submission::ATTRIBUTE_STATUS);
            });
        }

        if (! Schema::hasTable(SubmissionStep::TABLE)) {
            Schema::create(SubmissionStep::TABLE, function (Blueprint $table) {
                $table->id(SubmissionStep::ATTRIBUTE_ID);

                $table->integer(SubmissionStep::ATTRIBUTE_ACTION)->nullable();
                $table->nullableMorphs(SubmissionStep::RELATION_ACTIONABLE);
                $table->nullableMorphs(SubmissionStep::RELATION_TARGET);
                $table->string(SubmissionStep::ATTRIBUTE_PIVOT)->nullable();

                $table->json(SubmissionStep::ATTRIBUTE_FIELDS)->nullable();
                $table->integer(SubmissionStep::ATTRIBUTE_STATUS)->nullable();

                $table->unsignedBigInteger(SubmissionStep::ATTRIBUTE_SUBMISSION)->nullable();
                $table->foreign(SubmissionStep::ATTRIBUTE_SUBMISSION)->references(Submission::ATTRIBUTE_ID)->on(Submission::TABLE)->cascadeOnDelete();

                $table->timestamp(SubmissionStep::ATTRIBUTE_FINISHED_AT, 6)->nullable();
                $table->timestamps(6);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(SubmissionStep::TABLE);
        Schema::dropIfExists(Submission::TABLE);
    }
};
