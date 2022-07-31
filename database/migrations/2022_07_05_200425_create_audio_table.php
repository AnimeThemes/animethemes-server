<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Wiki\Audio;
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
        if (! Schema::hasTable(Audio::TABLE)) {
            Schema::create(Audio::TABLE, function (Blueprint $table) {
                $table->id(Audio::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
                $table->string(Audio::ATTRIBUTE_BASENAME);
                $table->string(Audio::ATTRIBUTE_FILENAME);
                $table->string(Audio::ATTRIBUTE_PATH);
                $table->integer(Audio::ATTRIBUTE_SIZE);
                $table->string(Audio::ATTRIBUTE_MIMETYPE);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(Audio::TABLE);
    }
};
