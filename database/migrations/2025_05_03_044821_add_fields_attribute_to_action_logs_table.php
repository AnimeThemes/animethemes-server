<?php

declare(strict_types=1);

use App\Models\Admin\ActionLog;
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
        if (! Schema::hasColumn(ActionLog::TABLE, ActionLog::ATTRIBUTE_FIELDS)) {
            Schema::table(ActionLog::TABLE, function (Blueprint $table) {
                $table->json(ActionLog::ATTRIBUTE_FIELDS)->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(ActionLog::TABLE, ActionLog::ATTRIBUTE_FIELDS)) {
            Schema::table(ActionLog::TABLE, function (Blueprint $table) {
                $table->dropColumn(ActionLog::ATTRIBUTE_FIELDS);
            });
        }
    }
};
