<?php

declare(strict_types=1);

use App\Models\Admin\Announcement;
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
        if (! Schema::hasColumn(Announcement::TABLE, Announcement::ATTRIBUTE_PUBLIC)) {
            Schema::table(Announcement::TABLE, function (Blueprint $table) {
                $table->boolean(Announcement::ATTRIBUTE_PUBLIC)->default(false)->after(Announcement::ATTRIBUTE_UPDATED_AT);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(Announcement::TABLE, Announcement::ATTRIBUTE_PUBLIC)) {
            Schema::table(Announcement::TABLE, function (Blueprint $table) {
                $table->dropColumn(Announcement::ATTRIBUTE_PUBLIC);
            });
        }
    }
};
