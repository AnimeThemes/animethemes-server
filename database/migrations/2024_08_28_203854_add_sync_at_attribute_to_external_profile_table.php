<?php

declare(strict_types=1);

use App\Models\List\ExternalProfile;
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
        if (! Schema::hasColumn(ExternalProfile::TABLE, ExternalProfile::ATTRIBUTE_SYNC_AT)) {
            Schema::table(ExternalProfile::TABLE, function (Blueprint $table) {
                $table->timestamp(ExternalProfile::ATTRIBUTE_SYNC_AT, 6)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(ExternalProfile::TABLE, ExternalProfile::ATTRIBUTE_SYNC_AT)) {
            Schema::table(ExternalProfile::TABLE, function (Blueprint $table) {
                $table->dropColumn(ExternalProfile::ATTRIBUTE_SYNC_AT);
            });
        }
    }
};
