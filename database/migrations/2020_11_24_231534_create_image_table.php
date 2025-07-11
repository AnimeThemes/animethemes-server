<?php

declare(strict_types=1);

use App\Constants\ModelConstants;
use App\Models\Wiki\Image;
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
        if (! Schema::hasTable(Image::TABLE)) {
            Schema::create(Image::TABLE, function (Blueprint $table) {
                $table->id(Image::ATTRIBUTE_ID);
                $table->timestamps(6);
                $table->softDeletes(ModelConstants::ATTRIBUTE_DELETED_AT, 6);
                $table->string(Image::ATTRIBUTE_PATH);
                $table->integer(Image::ATTRIBUTE_FACET)->nullable();
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
        Schema::dropIfExists(Image::TABLE);
    }
};
