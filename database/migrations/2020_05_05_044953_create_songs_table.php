<?php

declare(strict_types=1);

use App\Models\BaseModel;
use App\Models\Wiki\Song;
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
        if (! Schema::hasTable(Song::TABLE)) {
            Schema::create(Song::TABLE, function (Blueprint $table) {
                $table->id(Song::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(BaseModel::ATTRIBUTE_DELETED_AT, 6);
                $table->string(Song::ATTRIBUTE_TITLE)->nullable();
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
        Schema::dropIfExists(Song::TABLE);
    }
};
