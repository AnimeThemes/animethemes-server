<?php

declare(strict_types=1);

use App\Models\List\Playlist;
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
        if (! Schema::hasColumn(Playlist::TABLE, Playlist::ATTRIBUTE_DESCRIPTION)) {
            Schema::table(Playlist::TABLE, function (Blueprint $table) {
                $table->text(Playlist::ATTRIBUTE_DESCRIPTION)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(Playlist::TABLE, Playlist::ATTRIBUTE_DESCRIPTION)) {
            Schema::table(Playlist::TABLE, function (Blueprint $table) {
                $table->dropColumn(Playlist::ATTRIBUTE_DESCRIPTION);
            });
        }
    }
};
