<?php

declare(strict_types=1);

use App\Enums\Models\Admin\ApprovableStatus;
use App\Models\Admin\Report;
use App\Models\Admin\Report\ReportStep;
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
        if (! Schema::hasTable(Report::TABLE)) {
            Schema::create(Report::TABLE, function (Blueprint $table) {
                $table->id(Report::ATTRIBUTE_ID);

                $table->longText(Report::ATTRIBUTE_NOTES)->nullable();
                $table->integer(Report::ATTRIBUTE_STATUS)->default(ApprovableStatus::PENDING->value);

                $table->unsignedBigInteger(Report::ATTRIBUTE_USER)->nullable();
                $table->foreign(Report::ATTRIBUTE_USER)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->cascadeOnDelete();

                $table->unsignedBigInteger(Report::ATTRIBUTE_MODERATOR)->nullable();
                $table->foreign(Report::ATTRIBUTE_MODERATOR)->references(User::ATTRIBUTE_ID)->on(User::TABLE)->nullOnDelete();

                $table->longText(Report::ATTRIBUTE_MOD_NOTES)->nullable();
                $table->timestamp(Report::ATTRIBUTE_FINISHED_AT, 6)->nullable();

                $table->timestamps(6);;
            });
        }

        if (! Schema::hasTable(ReportStep::TABLE)) {
            Schema::create(ReportStep::TABLE, function (Blueprint $table) {
                $table->id(ReportStep::ATTRIBUTE_ID);

                $table->integer(ReportStep::ATTRIBUTE_ACTION)->nullable();
                $table->string(ReportStep::ATTRIBUTE_ACTIONABLE_TYPE)->nullable();
                $table->uuid(ReportStep::ATTRIBUTE_ACTIONABLE_ID)->nullable();
                $table->string(ReportStep::ATTRIBUTE_TARGET_TYPE)->nullable();
                $table->uuid(ReportStep::ATTRIBUTE_TARGET_ID)->nullable();
                $table->string(ReportStep::ATTRIBUTE_PIVOT_CLASS)->nullable();

                $table->json(ReportStep::ATTRIBUTE_FIELDS)->nullable();
                $table->integer(ReportStep::ATTRIBUTE_STATUS)->default(ApprovableStatus::PENDING->value);

                $table->unsignedBigInteger(ReportStep::ATTRIBUTE_REPORT)->nullable();
                $table->foreign(ReportStep::ATTRIBUTE_REPORT)->references(Report::ATTRIBUTE_ID)->on(Report::TABLE)->cascadeOnDelete();

                $table->timestamp(ReportStep::ATTRIBUTE_FINISHED_AT, 6)->nullable();
                $table->timestamps(6);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ReportStep::TABLE);
        Schema::dropIfExists(Report::TABLE);
    }
};
