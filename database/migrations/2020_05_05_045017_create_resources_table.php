<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Models\Wiki\ExternalResource;
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
        if (! Schema::hasTable(ExternalResource::TABLE)) {
            Schema::create(ExternalResource::TABLE, function (Blueprint $table) {
                $table->id(ExternalResource::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(ModelConstants::ATTRIBUTE_DELETED_AT, 6);
                $table->integer(ExternalResource::ATTRIBUTE_SITE)->nullable();
                $table->string(ExternalResource::ATTRIBUTE_LINK)->nullable();
                $table->integer(ExternalResource::ATTRIBUTE_EXTERNAL_ID)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ExternalResource::TABLE);
    }
};
