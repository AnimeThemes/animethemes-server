<?php

declare(strict_types=1);

use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
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
        if (! Schema::hasColumn(Video::TABLE, Video::ATTRIBUTE_AUDIO)) {
            Schema::table(Video::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger(Video::ATTRIBUTE_AUDIO)->nullable();
                $table->foreign(Video::ATTRIBUTE_AUDIO)->references(Audio::ATTRIBUTE_ID)->on(Audio::TABLE)->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(Video::TABLE, Video::ATTRIBUTE_AUDIO)) {
            Schema::table(Video::TABLE, function (Blueprint $table) {
                $table->dropConstrainedForeignId(Video::ATTRIBUTE_AUDIO);
            });
        }
    }
};
