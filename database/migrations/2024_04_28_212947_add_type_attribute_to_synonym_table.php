<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime\AnimeSynonym;
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
        if (! Schema::hasColumn(AnimeSynonym::TABLE, AnimeSynonym::ATTRIBUTE_TYPE)) {
            Schema::table(AnimeSynonym::TABLE, function (Blueprint $table) {
                $table->integer(AnimeSynonym::ATTRIBUTE_TYPE)->default(AnimeSynonymType::OTHER->value);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn(AnimeSynonym::TABLE, AnimeSynonym::ATTRIBUTE_TYPE)) {
            Schema::table(AnimeSynonym::TABLE, function (Blueprint $table) {
                $table->dropColumn(AnimeSynonym::ATTRIBUTE_TYPE);
            });
        }
    }
};
