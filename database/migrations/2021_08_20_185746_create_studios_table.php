<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Models\Wiki\Studio;
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
        if (! Schema::hasTable(Studio::TABLE)) {
            Schema::create(Studio::TABLE, function (Blueprint $table) {
                $table->id(Studio::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(ModelConstants::ATTRIBUTE_DELETED_AT, 6);
                $table->string(Studio::ATTRIBUTE_SLUG);
                $table->string(Studio::ATTRIBUTE_NAME);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Studio::TABLE);
    }
};
