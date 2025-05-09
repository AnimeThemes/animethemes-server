<?php

declare(strict_types=1);

use App\Models\Admin\ActionLog;
use App\Models\Auth\User;
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
        if (! Schema::hasTable(ActionLog::TABLE)) {
            Schema::create(ActionLog::TABLE, function (Blueprint $table) {
                $table->bigIncrements(ActionLog::ATTRIBUTE_ID);
                $table->string(ActionLog::ATTRIBUTE_BATCH_ID);

                $table->unsignedBigInteger(ActionLog::ATTRIBUTE_USER);
                $table->foreign(ActionLog::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->cascadeOnDelete();

                $table->string(ActionLog::ATTRIBUTE_NAME);
                $table->morphs(ActionLog::ATTRIBUTE_ACTIONABLE);
                $table->morphs(ActionLog::ATTRIBUTE_TARGET);
                $table->string(ActionLog::ATTRIBUTE_MODEL_TYPE);
                $table->uuid(ActionLog::ATTRIBUTE_MODEL_ID)->nullable();
                $table->integer(ActionLog::ATTRIBUTE_STATUS)->nullable();
                $table->json(ActionLog::ATTRIBUTE_FIELDS)->nullable();
                $table->text(ActionLog::ATTRIBUTE_EXCEPTION)->nullable();
                $table->timestamps(6);
                $table->timestamp(ActionLog::ATTRIBUTE_FINISHED_AT, 6)->nullable();

                $table->index([ActionLog::ATTRIBUTE_BATCH_ID, ActionLog::ATTRIBUTE_MODEL_TYPE, ActionLog::ATTRIBUTE_MODEL_ID]);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ActionLog::TABLE);
    }
};
