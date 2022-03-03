<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\VideoOverlap;
use App\Models\BaseModel;
use App\Models\Wiki\Video;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(Video::TABLE, function (Blueprint $table) {
            $table->id(Video::ATTRIBUTE_ID);
            $table->timestamps(6);
            $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
            $table->string(Video::ATTRIBUTE_BASENAME);
            $table->string(Video::ATTRIBUTE_FILENAME);
            $table->string(Video::ATTRIBUTE_PATH);
            $table->integer(Video::ATTRIBUTE_SIZE);
            $table->string(Video::ATTRIBUTE_MIMETYPE);
            $table->integer(Video::ATTRIBUTE_RESOLUTION)->nullable();
            $table->boolean(Video::ATTRIBUTE_NC)->default(false);
            $table->boolean(Video::ATTRIBUTE_SUBBED)->default(false);
            $table->boolean(Video::ATTRIBUTE_LYRICS)->default(false);
            $table->boolean(Video::ATTRIBUTE_UNCEN)->default(false);
            $table->integer(Video::ATTRIBUTE_OVERLAP)->default(VideoOverlap::NONE);
            $table->integer(Video::ATTRIBUTE_SOURCE)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Video::TABLE);
    }
};
