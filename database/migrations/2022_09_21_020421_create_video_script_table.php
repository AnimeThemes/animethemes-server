<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Models\Wiki\Video;
use App\Models\Wiki\Video\VideoScript;
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
        if (! Schema::hasTable(VideoScript::TABLE)) {
            Schema::create(VideoScript::TABLE, function (Blueprint $table) {
                $table->id(VideoScript::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(ModelConstants::ATTRIBUTE_DELETED_AT, 6);
                $table->string(VideoScript::ATTRIBUTE_PATH);

                $table->unsignedBigInteger(VideoScript::ATTRIBUTE_VIDEO)->nullable();
                $table->foreign(VideoScript::ATTRIBUTE_VIDEO)->references(Video::ATTRIBUTE_ID)->on(Video::TABLE)->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(VideoScript::TABLE);
    }
};
